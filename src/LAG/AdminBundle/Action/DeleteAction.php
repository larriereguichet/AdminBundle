<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Responder\DeleteResponder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteAction extends Action
{
    /**
     * @var DeleteResponder
     */
    protected $responder;
    
    /**
     * Action constructor.
     *
     * @param string               $name
     * @param FormFactoryInterface $formFactory
     * @param DeleteResponder      $responder
     */
    public function __construct(
        $name,
        FormFactoryInterface $formFactory,
        DeleteResponder $responder
    ) {
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->responder = $responder;
    }
    
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
        
        // create the entity removal form type
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
    
        // return a Response using the DeleteResponder
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form)
        ;
    }
}
