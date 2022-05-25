<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Context\AdminContextInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Index
{
    public function __construct(
        private AdminContextInterface $context,
        private FormFactoryInterface $formFactory,
        private DataProviderInterface $dataProvider,
        private GridFactoryInterface $gridFactory,
        private Environment $environment,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $admin = $this->context->getAdmin();
        $action = $admin->getCurrentAction();
        $filters = [];

        $form = $this->formFactory->create($admin->getFormType(), $admin->getFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
        }
        $data = $this->dataProvider->provide($admin, $action, [], [
            'filters' => $filters,
        ]);
        $grid = $this->gridFactory->create($data, $admin, $action);

        return new Response($this->environment->render($action->getTemplate(), [
            'grid' => new Grid($data),
            'admin' => $admin,
        ]));
    }
}
