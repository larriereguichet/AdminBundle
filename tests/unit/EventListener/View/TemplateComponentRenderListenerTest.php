<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\View;

use LAG\AdminBundle\EventListener\View\TemplateComponentRenderListener;
use LAG\AdminBundle\Twig\Component\TemplateComponentInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\ComponentMetadata;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Symfony\UX\TwigComponent\MountedComponent;
use Twig\Runtime\EscaperRuntime;

final class TemplateComponentRenderListenerTest extends TestCase
{
    private TemplateComponentRenderListener $listener;

    #[Test]
    public function itDoesNothingForNonTemplateComponent(): void
    {
        $component = new \stdClass();
        $event = $this->createEvent($component);

        $this->listener->__invoke($event);

        self::assertEquals('components/default.html.twig', $event->getTemplate());
    }

    #[Test]
    public function itDoesNothingWhenTemplateIsNull(): void
    {
        $component = $this->createStub(TemplateComponentInterface::class);
        $component->method('getTemplate')->willReturn(null);

        $event = $this->createEvent($component);

        $this->listener->__invoke($event);

        self::assertEquals('components/default.html.twig', $event->getTemplate());
    }

    #[Test]
    public function itSetsTheTemplate(): void
    {
        $component = $this->createStub(TemplateComponentInterface::class);
        $component->method('getTemplate')->willReturn('components/my_template.html.twig');

        $event = $this->createEvent($component);

        $this->listener->__invoke($event);

        self::assertEquals('components/my_template.html.twig', $event->getTemplate());
    }

    protected function setUp(): void
    {
        $this->listener = new TemplateComponentRenderListener();
    }

    private function createEvent(object $component): PreRenderEvent
    {
        $mounted = new MountedComponent(
            'my_component',
            $component,
            new ComponentAttributes([], new EscaperRuntime()),
            null,
            [],
        );
        $metadata = new ComponentMetadata([
            'key' => 'my_component',
            'template' => 'components/default.html.twig',
        ]);

        return new PreRenderEvent($mounted, $metadata, []);
    }
}
