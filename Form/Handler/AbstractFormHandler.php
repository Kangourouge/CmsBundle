<?php

namespace KRG\CmsBundle\Form\Handler;

use AppBundle\Doctrine\DBAL\AttributeTagEnum;
use AppBundle\Entity\Estate;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFormHandler implements FormHandlerInterface
{
    public function perform(Request $request, FormInterface $form)
    {
        $form->handleRequest($request);
        $filter = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $filter = $form->getData();

            $queryBuilder = $this->getDoctrine()->getRepository(Estate::class)->getQueryBuilder($filter, [AttributeTagEnum::PRICE => 'ASC']);

            $adapter = new DoctrineORMAdapter($queryBuilder);
            $pager = new Pagerfanta($adapter);

            try {
                $estates = $pager->setMaxPerPage(5)->setCurrentPage($page)->getCurrentPageResults();
            } catch (NotValidCurrentPageException $e) {
                throw $this->createNotFoundException("Page is not valid.");
            }
        }

        return null;
    }
}
