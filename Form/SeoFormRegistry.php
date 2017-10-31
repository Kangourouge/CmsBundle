<?php

namespace KRG\SeoBundle\Form;

class SeoFormRegistry
{
    /**
     * @var array
     */
    private $seoForms;

    function __construct()
    {
        $this->seoForms = array();
    }

    public function add($form, $alias, $template, $handler = null)
    {
        $this->seoForms[$form] = array(
            'form'     => $form,
            'alias'    => $alias,
            'template' => $template,
            'handler'  => $handler,
        );
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->seoForms)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not registered with the service container.', $id));
        }

        return $this->seoForms[$id];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->seoForms;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->seoForms[$id]);
    }



}
