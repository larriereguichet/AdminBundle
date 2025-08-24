<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\View;

use LAG\AdminBundle\View\Component\DynamicTemplateComponentInterface;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final readonly class DynamicUxComponentRenderListener
{
    public function __invoke(PreRenderEvent $event): void
    {
        $component = $event->getComponent();

        if (!$component instanceof DynamicTemplateComponentInterface || $component->getTemplate() === null) {
            return;
        }
        $event->setTemplate($component->getTemplate());
    }
}
