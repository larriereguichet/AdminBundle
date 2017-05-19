<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class ListResponder extends AbstractResponder
{
    /**
     * @param ActionConfiguration $configuration
     * @param AdminInterface      $admin
     * @param FormInterface       $form
     * @param FormInterface       $filterForm
     *
     * @return Response
     *
     */
    public function respond(
        ActionConfiguration $configuration,
        AdminInterface $admin,
        FormInterface $form,
        FormInterface $filterForm = null
    ) {
        $context = [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    
        if (null !== $filterForm) {
            $context['filterForm'] = $filterForm->createView();
        }
    
        return $this->render($configuration->getParameter('template'), $context);
    }
}
