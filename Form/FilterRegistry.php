<?php

namespace KRG\CmsBundle\Form;

class FilterRegistry
{
    /** @var array */
    private $forms;

    function __construct()
    {
        $this->forms = [];
    }

    public function add($form, $alias, $template, $handler = null)
    {
        $this->forms[$form] = [
            'form'     => $form,
            'alias'    => $alias,
            'template' => $template,
            'handler'  => $handler,
        ];
    }

    public function get($id)
    {
        if (!array_key_exists($id, $this->forms)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not registered with the service container.', $id));
        }

        return $this->forms[$id];
    }

    public function all()
    {
        return $this->forms;
    }

    public function has($id)
    {
        return isset($this->forms[$id]);
    }
}
