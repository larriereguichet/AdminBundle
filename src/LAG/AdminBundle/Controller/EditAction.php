<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Responder\EditResponder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditAction extends Action
{
    /**
     * @var EditResponder
     */
    protected $responder;
    
    /**
     * Action constructor.
     *
     * @param string               $name
     * @param FormFactoryInterface $formFactory
     * @param EditResponder        $responder
     */
    public function __construct(
        $name,
        FormFactoryInterface $formFactory,
        EditResponder $responder
    ) {
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->responder = $responder;
    }
    
    /**
     * Edit and update an entity using the EditForm handler.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        // the admin with automatically injected thanks to the KernelSubscriber
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
    
        // return a Response using the EditResponder
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form, $request->request->get('submit'))
        ;
    }
}
