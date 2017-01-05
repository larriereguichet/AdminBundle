<?php

namespace LAG\AdminBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends Action
{
    /**
     * Delete an entity.
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
        // create the configured form type
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
            ->create($formType, $this->admin->getUniqueEntity(), $formOptions)
        ;
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // remove the entity
            $this
                ->admin
                ->remove()
            ;
        }
    
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form, $request)
        ;
    }
}
