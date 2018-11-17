<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Filter\Filter;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FilterSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public static function getSubscribedEvents()
    {
        return [
            Events::FILTER => 'onFilter',
        ];
    }

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function onFilter(FilterEvent $event): void
    {
        // Retrieve the configured filters for the current admin and action
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();
        $filters = $configuration->getParameter('filters');

        // Nothing to do if not filters are configured
        if (0 === count($filters)) {
            return;
        }
        // As filters should be applied before entity loading, the filter form is created and submitted at the same
        // time, unlike the classic forms
        $form = $this->createForm($filters);
        $form->handleRequest($event->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($filters as $name => $options) {
                // Nothing to do if the data is not submitted or if it is null
                if (!key_exists($name, $data) || null === $data[$name]) {
                    continue;
                }

                // Do not submit false boolean values to improve user experience
                if (is_bool($data[$name]) && false === $data[$name]) {
                    continue;
                }

                // Create a new filter with submitted and configured values
                $filter = new Filter($options['name'], $data[$name], $options['comparator'], $options['operator']);
                $event->addFilter($filter);
            }
        }

        // Add the newly created form to the form collection to be displayed on the view
        $event->addForm($form, 'filter');
    }

    private function createForm(array $filters): FormInterface
    {
        $form = $this
            ->formFactory
            ->createNamed('filter', FormType::class)
        ;

        foreach ($filters as $name => $filter) {
            $options = array_merge([
                // All filters are optional
                'required' => false,
            ], $filter['options']);
            $type = FormUtils::convertShortFormType($filter['type']);

            $form->add($name, $type, $options);
        }

        return $form;
    }
}
