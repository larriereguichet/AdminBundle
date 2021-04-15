<?php

namespace LAG\AdminBundle\Event\Listener\Theme;

use LAG\AdminBundle\Event\Events\FieldEvent;
use LAG\AdminBundle\Field\FieldInterface;

class FieldThemeListener
{
    public function __invoke(FieldEvent $event): void
    {
        $type = $event->getType();
        $context = $event->getContext();
        $adminName = empty($context['admin_name']) ? null : $context['admin_name'];
        $actionName = empty($context['action_name']) ? null : $context['action_name'];

        if ($type === FieldInterface::TYPE_ACTION) {
            $options = $event->getOptions();

            if (empty($options['attr'])) {
                $options['attr'] = [];
            }
            $class = '';

            if (!empty($options['attr']['class'])) {
                $class = $options['attr']['class'];
            }
            $newClass = 'btn btn-default btn-sm';

            if ($actionName === 'create' || $actionName === 'edit') {
                $newClass = 'btn btn-primary btn-sm';
            }

            if ($actionName === 'delete') {
                $newClass = 'btn btn-danger btn-sm';
            }

            $class .= ' '.$newClass;
            $options['attr']['class'] = trim($class);

            $event->setOptions($options);
        }
    }
}
