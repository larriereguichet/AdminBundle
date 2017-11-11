<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Responder\CreateResponder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateAction extends Action
{
    /**
     * @var CreateResponder
     */
    protected $responder;
    
    /**
     * Action constructor.
     *
     * @param string               $name
     * @param FormFactoryInterface $formFactory
     * @param CreateResponder      $responder
     */
    public function __construct(
        $name,
        FormFactoryInterface $formFactory,
        CreateResponder $responder
    ) {
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->responder = $responder;
    }
    
    /**
     * Create an action using the create action form handler.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        // The Admin with auto injected in the KernelSubscriber
        $this
            ->admin
            ->handleRequest($request)
        ;
        $entity = $this
            ->admin
            ->create()
        ;
        
        // Create the associated form type
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
            ->respond($this->configuration, $this->admin, $form, $request->request->get('submit'))
        ;
    }
}
