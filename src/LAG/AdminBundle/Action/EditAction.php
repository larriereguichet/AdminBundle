<?php

namespace LAG\AdminBundle\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends Action
{
    /**
     * Edit and update an entity using the EditForm handler.
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
        
        // create the associated form type
        $form = $this
            ->formFactory
            ->create(
                $this->configuration->getParameter('form'),
                $this->admin->getUniqueEntity(),
                $this->configuration->getParameter('form_options')
            )
        ;
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // save the updated entity
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
