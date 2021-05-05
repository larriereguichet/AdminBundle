<?php

namespace LAG\AdminBundle\Event\Listener\Data;

use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Factory\Form\FilterFormFactoryInterface;
use LAG\AdminBundle\Filter\Filter;

class CreateDataFilterListener
{
    private FilterFormFactoryInterface $filterFormFactory;

    public function __construct(FilterFormFactoryInterface $filterFormFactory)
    {
        $this->filterFormFactory = $filterFormFactory;
    }

    public function __invoke(DataEvent $event): void
    {
        $request = $event->getRequest();
        $admin = $event->getAdmin();
        $filters = $admin->getAction()->getConfiguration()->getFilters();

        if (\count($filters) === 0) {
            return;
        }
        $form = $this->filterFormFactory->create($admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($filters as $name => $options) {
                // Nothing to do if the data is not submitted or if it is null
                if (!\array_key_exists($name, $data) || null === $data[$name]) {
                    continue;
                }

                // Do not submit false boolean values. If we want to have three values (true false and null) we should
                // use a select
                if (\is_bool($data[$name]) && false === $data[$name]) {
                    continue;
                }

                if ($options['path'] === null) {
                    $options['path'] = $name;
                }

                if (is_array($data[$name])) {
                    $options['comparator'] = 'between';
                }
                // Create a new filter with submitted and configured values
                $filter = new Filter(
                    $options['name'],
                    $data[$name],
                    $options['type'],
                    $options['path'],
                    $options['comparator'],
                    $options['operator']
                );
                $event->addFilter($filter);
            }
        }
        $event->setFilterForm($form);
    }
}
