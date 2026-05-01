<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ContextBuilderTest extends TestCase
{
    private ContextBuilder $provider;

    #[Test]
    public function itProvidesContext(): void
    {
        $operation = new Show(context: ['a_key' => 'a_value']);

        $context = $this->provider->buildContext($operation, new Request());

        self::assertEquals(['a_key' => 'a_value'], $context);
    }

    protected function setUp(): void
    {
        $this->provider = new ContextBuilder();
    }
}
