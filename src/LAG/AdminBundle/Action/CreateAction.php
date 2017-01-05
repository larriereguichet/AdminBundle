<?php

namespace LAG\AdminBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateAction extends Action
{
    /**
     * Create an action using the create action form handler.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        // the Admin with auto injected with the KernelSubscriber
        $this
            ->admin
            ->handleRequest($request)
        ;
        // create the new entity
        $entity = $this
            ->admin
            ->create()
        ;
        // create the associated form type
        $formType = $this
            ->configuration
            ->getParameter('form')
        ;
        $formOptions = $this
            ->configuration
            ->getParameter('form_options')
        ;
        $form = $this
            ->formFactory
            ->create($formType, $entity, $formOptions)
        ;
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $this
                ->admin
                ->save()
            ;
        }
    
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form, $request)
        ;
    }
}
