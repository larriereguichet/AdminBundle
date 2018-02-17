<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\Responder\ListResponder;
use LAG\AdminBundle\Admin\Request\RequestHandlerInterface;
use LAG\AdminBundle\Filter\Factory\FilterFormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListAction
{
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    public function __construct(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $admin = $this
            ->requestHandler
            ->handle($request)
        ;
        $admin->handleRequest($request);


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
