<?php

namespace KRG\CmsBundle\Routing\Generator\Dumper;

use Symfony\Component\Routing\Generator\Dumper\GeneratorDumper;

/**
 * Override Symfony\Component\Routing\Generator\Dumper\PhpGeneratorDumper
 * Original: https://github.com/symfony/symfony/blob/3.4/src/Symfony/Component/Routing/Generator/Dumper/PhpGeneratorDumper.php
 */
class PhpGeneratorDumper extends GeneratorDumper
{
    public function dump(array $options = array())
    {
        $options = array_merge(array(
            'class' => 'ProjectUrlGenerator',
            'base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
        ), $options);

        return <<<EOF
<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class {$options['class']} extends {$options['base_class']}
{
    private static \$declaredRoutes;

    public function __construct(RequestContext \$context, LoggerInterface \$logger = null)
    {
        \$this->context = \$context;
        \$this->logger = \$logger;
        if (null === self::\$declaredRoutes) {
            self::\$declaredRoutes = {$this->generateDeclaredRoutes()};
        }
    }

{$this->generateGenerateMethod()}
}

EOF;
    }

    /**
     * Generates PHP code representing an array of defined routes
     * together with the routes properties (e.g. requirements).
     *
     * @return string PHP code
     */
    private function generateDeclaredRoutes()
    {
        $routes = "array(\n";
        foreach ($this->getRoutes()->all() as $name => $route) {
            $compiledRoute = $route->compile();

            $properties = array();
            $properties[] = $compiledRoute->getVariables();
            $properties[] = $route->getDefaults();
            $properties[] = $route->getRequirements();
            $properties[] = $compiledRoute->getTokens();
            $properties[] = $compiledRoute->getHostTokens();
            $properties[] = $route->getSchemes();

            $routes .= sprintf("        '%s' => %s,\n", $name, str_replace("\n", '', var_export($properties, true)));
        }
        $routes .= '    )';

        return $routes;
    }

    /**
     * Generates PHP code representing the `generate` method that implements the UrlGeneratorInterface.
     *
     * @return string PHP code
     */
    private function generateGenerateMethod()
    {
        return <<<'EOF'
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $locale = $parameters['_locale'] ?? $this->context->getParameter('_locale');

        if (null !== $locale && ($localizedRoute = (self::$declaredRoutes[$name.'.'.$locale] ?? null)) && ($localizedRoute[1]['_canonical_route'] ?? null) === $name) {
            $name = $name.'.'.$locale;
        } elseif (!isset(self::$declaredRoutes[$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
        }

        list($variables, $defaults, $requirements, $tokens, $hostTokens, $requiredSchemes) = self::$declaredRoutes[$name];

        return $this->doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $referenceType, $hostTokens, $requiredSchemes);
    }
    
    protected function getDefaultLocale(array $routeData)
    {
        if (isset($routeData['_canonical_route']) && $route = (self::$declaredRoutes[$routeData['_canonical_route']] ?? null)) {
            return $route[1]['_locale'];
        }

        return null;
    }
EOF;
    }
}
