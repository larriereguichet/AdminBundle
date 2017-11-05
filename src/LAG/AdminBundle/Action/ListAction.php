<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Responder\ListResponder;
use LAG\AdminBundle\Filter\Factory\FilterFormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends Action
{
    /**
     * @var ListResponder
     */
    protected $responder;
    
    /**
     * @var FilterFormBuilder
     */
    protected $filterFormBuilder;
    
    /**
     * Action constructor.
     *
     * @param string               $name
     * @param FormFactoryInterface $formFactory
     * @param ListResponder        $responder
     * @param FilterFormBuilder    $filterFormBuilder
     */
    public function __construct(
        $name,
        FormFactoryInterface $formFactory,
        ListResponder $responder,
        FilterFormBuilder $filterFormBuilder
    ) {
        $this->name = $name;
        $this->formFactory = $formFactory;
        $this->responder = $responder;
        $this->filterFormBuilder = $filterFormBuilder;
    }
    
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        // build the filters form
        $filterForm = $this
            ->filterFormBuilder
            ->build($this->configuration)
        ;
        $filters = [];

        // load the filters
        if (null !== $filterForm) {
            $filterForm->handleRequest($request);
    
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $filters = $filterForm->getData();
            }
        }
        
        // load the entities
        $this
            ->admin
            ->handleRequest($request, $filters)
        ;
    
        $form = $this
            ->formFactory
            ->create(
                $this->configuration->getParameter('form'),
                $this->admin->getEntities(),
                $this->configuration->getParameter('form_options')
            )
        ;
        $form->handleRequest($request);
    
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form, $filterForm)
        ;
    }
}
