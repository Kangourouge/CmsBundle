<?php

namespace KRG\SeoBundle\Form\EventListener;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SeoEventListener implements EventSubscriberInterface
{
    /**
     * @var ClassMetadataFactory
     */
    protected $metadataClassFactory;

    /**
     * SeoPageSeoType constructor.
     *
     * @param ClassMetadataFactory $metadataClassFactory
     */
    public function __construct(ClassMetadataFactory $metadataClassFactory)
    {
        $this->metadataClassFactory = $metadataClassFactory;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData'
        ];
    }

    public function onPreSetData(FormEvent $event) {
        $seo = $event->getData();
        if ($seo === null) {
            $className = $this->metadataClassFactory->getMetadataFor(SeoInterface::class)->getName();
            $event->setData(new $className);
        }
    }
}