<?php

namespace KRG\CmsBundle\Form;

/**
 * Class FilterRegistry
 * @package KRG\CmsBundle\Form
 */
class FilterRegistry
{
    /**
     * @var array
     */
    private $forms;

    /**
     * FilterRegistry constructor.
     */
    function __construct()
    {
        $this->forms = [];
    }

    /**
     * @param      $form
     * @param      $alias
     * @param      $template
     * @param null $handler
     */
    public function add($form, $alias, $template, $handler = null)
    {
        $this->forms[$form] = [
            'form'     => $form,
            'alias'    => $alias,
            'template' => $template,
            'handler'  => $handler,
        ];
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->forms)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not registered with the service container.', $id));
        }

        return $this->forms[$id];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->forms;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->forms[$id]);
    }
}
