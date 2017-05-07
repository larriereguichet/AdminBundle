<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Filter\Factory\FilterFormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends Action
{
    /**
     * @var FilterFormBuilder
     */
    private $filterFormBuilder;
    
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
    
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO do something with the selected entities
        }
    
        return $this
            ->responder
            ->respond($this->configuration, $this->admin, $form, $filterForm)
        ;
    }
    
    /**
     * @param FilterFormBuilder $filterFormBuilder
     */
    public function setFilterFormBuilder($filterFormBuilder)
    {
        $this->filterFormBuilder = $filterFormBuilder;
    }
}
