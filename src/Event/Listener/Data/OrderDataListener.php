<?php

namespace LAG\AdminBundle\Event\Listener\Data;

use LAG\AdminBundle\Event\Events\DataEvent;

class OrderDataListener
{
    public function __invoke(DataEvent $event): void
    {
        $request = $event->getRequest();
        $admin = $event->getAdmin();
        $configuration = $admin->getAction()->getConfiguration();
        $sort = $request->get('sort');

        // The sort from the request override the configured one
        if ($sort) {
            $order = $request->get('order', 'asc');
            $event->addOrderBy($sort, $order);
        } else {
            foreach ($configuration->getOrder() as $field => $order) {
                $event->addOrderBy($field, $order);
            }
        }
    }
}
