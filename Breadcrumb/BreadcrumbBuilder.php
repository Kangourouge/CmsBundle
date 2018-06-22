<?php

namespace KRG\CmsBundle\Breadcrumb;

use KRG\CmsBundle\Annotation\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use KRG\CmsBundle\Menu\MenuBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RouterInterface */
    protected $router;

    /** @var AnnotationReader */
    protected $annotationReader;

    /** @var MenuBuilderInterface */
    protected $menuBuilder;

    /** @var FilesystemAdapter */
    private $filesystemAdapter;

    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, AnnotationReader $annotationReader, MenuBuilderInterface $menuBuilder, string $dataCacheDir)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->menuBuilder = $menuBuilder;
        $this->annotationReader = $annotationReader;
        $this->filesystemAdapter = new FilesystemAdapter('menu', 0, $dataCacheDir);
    }

    public function build(Request $request)
    {
        $this->menuBuilder->setAnnotations($this->getAnnotations($request));
    }

    public function getNodes(string $key = null)
    {
        return $this->menuBuilder->getActiveNodes($key);
    }

    private function getAnnotations(Request $request)
    {
        $annotations = [];
        try {
            $reflectionMethod = new \ReflectionMethod($request->get('_controller'));
            foreach ($this->annotationReader->getMethodAnnotations($reflectionMethod) as $key => $annotation) {
                if ($annotation instanceof Menu) {
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $attributes = $request->attributes->all();
                    $params = $annotation->getParams();
                    foreach ($params as $key => &$value) {
                        $value = $this->populate($propertyAccessor, $attributes, $value);
                    }
                    unset($value);
                    $annotation->setName($this->populate($propertyAccessor, $attributes, $annotation->getName()));
                    $annotation->setParams($params);
                    $annotations[] = $annotation;
                }
            }
        } catch (\ReflectionException $exception) {
        }

        return $annotations;
    }

    private function populate(PropertyAccessor $propertyAccessor, array $attributes, $value)
    {
        if (preg_match_all('`\{(([^\.]+)\.([^\}]+))\}`', $value, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Call getter from $object = $request->attributes->get(...);
                $_value = $propertyAccessor->getValue($attributes, sprintf('[%s].%s', $match[2], $match[3]));
                $value = preg_replace(sprintf('`%s`', preg_quote($match[0])), $_value, $value);
            }
        }

        return $value;
    }
}
