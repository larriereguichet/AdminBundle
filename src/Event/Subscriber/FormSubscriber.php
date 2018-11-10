<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\FilterEvent;
use LAG\AdminBundle\Event\FormEvent;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\Filter\Filter;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::HANDLE_FORM => 'createEntityForm',
            Events::FILTER => 'createFilterForm',
        ];
    }

    /**
     * FormSubscriber constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param DataProviderFactory  $dataProviderFactory
     */
    public function __construct(FormFactoryInterface $formFactory, DataProviderFactory $dataProviderFactory)
    {
        $this->formFactory = $formFactory;
        $this->dataProviderFactory = $dataProviderFactory;
    }

    /**
     * Create a form for the loaded entity.
     *
     * @param FormEvent $event
     */
    public function createEntityForm(FormEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();

        if (!$configuration->getParameter('use_form')) {
            return;
        }
        $entity = null;

        if (LAGAdminBundle::LOAD_STRATEGY_UNIQUE === $configuration->get('load_strategy')) {
            $entity = $admin->getEntities()->first();
        }

        if (!$entity) {
            $dataProvider = $this
                ->dataProviderFactory
                ->get($admin->getConfiguration()->get('data_provider'))
            ;
            $entity = $dataProvider->create($admin);
        }

        $form = $this
            ->formFactory
            ->create($admin->getConfiguration()->getParameter('form'), $entity)
        ;
        $event->addForm($form, 'entity');
    }

    /**
     * Create a filter form from configuration and handle the form submission if required.
     *
     * @param FilterEvent $event
     */
    public function createFilterForm(FilterEvent $event)
    {
        $admin = $event->getAdmin();
        $action = $admin->getAction();
        $configuration = $action->getConfiguration();
        $filters = $configuration->getParameter('filters');

        if (0 === count($filters)) {
            return;
        }
        $form = $this
            ->formFactory
            ->createNamed('filter', FormType::class)
        ;

        foreach ($filters as $name => $filter) {
            $options = array_merge([
                'required' => false,
            ], $filter['options']);
            $type = FormUtils::convertShortFormType($filter['type']);

            $form->add($name, $type, $options);
        }
        $form->handleRequest($event->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($filters as $name => $options) {
                // If the data is not submitted or if it is null, we should do nothing
                if (!key_exists($name, $data) || null === $data[$name]) {
                    continue;
                }

                // Do not submit false boolean values to improve user experience
                if (is_bool($data[$name]) && false === $data[$name]) {
                    continue;
                }

                // Create a new filter with submitted and configured values
                $filter = new Filter($options['name'], $data[$name], $options['operator']);
                $event->addFilter($filter);
            }
        }

        $event->addForm($form, 'filter');
    }
}
