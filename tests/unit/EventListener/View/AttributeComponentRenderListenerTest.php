<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\View;

use LAG\AdminBundle\EventListener\View\AttributeComponentRenderListener;
use LAG\AdminBundle\Twig\Component\AttributeComponentInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\ComponentMetadata;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Symfony\UX\TwigComponent\MountedComponent;
use Twig\Runtime\EscaperRuntime;

final class AttributeComponentRenderListenerTest extends TestCase
{
    private AttributeComponentRenderListener $listener;

    #[Test]
    public function itDoesNothingForNonAttributeComponent(): void
    {
        $component = new \stdClass();
        $event = $this->createEvent($component, ['attributes' => new ComponentAttributes(['class' => 'foo'], new EscaperRuntime())]);

        $this->listener->__invoke($event);

        self::assertSame(['attributes' => $event->getVariables()['attributes']], $event->getVariables());
    }

    #[Test]
    public function itDoesNothingWhenAttributesAreEmpty(): void
    {
        $component = $this->createStub(AttributeComponentInterface::class);
        $component->method('getAttributes')->willReturn([]);

        $original = new ComponentAttributes(['class' => 'foo'], new EscaperRuntime());
        $event = $this->createEvent($component, ['attributes' => $original]);

        $this->listener->__invoke($event);

        self::assertSame($original, $event->getVariables()['attributes']);
    }

    #[Test]
    public function itDoesNothingWhenVariablesHaveNoAttributes(): void
    {
        $component = $this->createStub(AttributeComponentInterface::class);
        $component->method('getAttributes')->willReturn(['class' => 'bar']);

        $event = $this->createEvent($component, []);

        $this->listener->__invoke($event);

        self::assertSame([], $event->getVariables());
    }

    #[Test]
    public function itMergesAttributesIntoVariables(): void
    {
        $component = $this->createStub(AttributeComponentInterface::class);
        $component->method('getAttributes')->willReturn(['id' => 'my-id']);

        $original = new ComponentAttributes(['class' => 'foo'], new EscaperRuntime());
        $event = $this->createEvent($component, ['attributes' => $original]);

        $this->listener->__invoke($event);

        $merged = $event->getVariables()['attributes'];
        self::assertInstanceOf(ComponentAttributes::class, $merged);
        self::assertSame('foo', $merged->all()['class'] ?? $merged['class'] ?? null);
        self::assertSame('my-id', $merged->all()['id'] ?? $merged['id'] ?? null);
    }

    protected function setUp(): void
    {
        $this->listener = new AttributeComponentRenderListener();
    }

    /** @param array<string, mixed> $variables */
    private function createEvent(object $component, array $variables): PreRenderEvent
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

        return new PreRenderEvent($mounted, $metadata, $variables);
    }
}
