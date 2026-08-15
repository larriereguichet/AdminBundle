<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\View;

use LAG\AdminBundle\Twig\Component\TemplateComponentInterface;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final readonly class TemplateComponentRenderListener
{
    public function __invoke(PreRenderEvent $event): void
    {
        $component = $event->getComponent();

        if (!$component instanceof TemplateComponentInterface || $component->getTemplate() === null) {
            return;
        }
        $event->setTemplate($component->getTemplate());
    }
}
