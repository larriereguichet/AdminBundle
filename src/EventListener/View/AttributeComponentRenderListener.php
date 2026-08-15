<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\View;

use LAG\AdminBundle\Twig\Component\AttributeComponentInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final class AttributeComponentRenderListener
{
    public function __invoke(PreRenderEvent $event): void
    {
        $component = $event->getComponent();

        if (!$component instanceof AttributeComponentInterface || $component->getAttributes() === []) {
            return;
        }
        $variables = $event->getVariables();
        /** @var ComponentAttributes|null $attributes */
        $attributes = $variables['attributes'] ?? null;

        if ($attributes === null) {
            return;
        }
        $variables['attributes'] = $attributes->defaults($component->getAttributes());
        $event->setVariables($variables);
    }
}
