<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Listener\Theme;

use LAG\AdminBundle\Event\Events\Configuration\FieldConfigurationEvent;
use LAG\AdminBundle\Field\FieldInterface;

class FieldThemeListener
{
    public function __invoke(FieldConfigurationEvent $event): void
    {
        if ($event->getType() === FieldInterface::TYPE_ACTION) {
            $options = $event->getOptions();

            if (empty($options['attr'])) {
                $options['attr'] = [];
            }
            $class = '';

            if (!empty($options['attr']['class'])) {
                $class = $options['attr']['class'];
            }
            $newClass = 'btn btn-info btn-sm';
            $actionName = !empty($options['action']) && $options['action'] !== null ? $options['action'] : '';

            if ($actionName === 'create' || $actionName === 'update') {
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
