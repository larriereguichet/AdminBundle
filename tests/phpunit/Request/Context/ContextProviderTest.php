<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Context;

use LAG\AdminBundle\Request\ContextBuilder\OperationContextBuilder;
use LAG\AdminBundle\Resource\Metadata\Show;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ContextProviderTest extends TestCase
{
    private OperationContextBuilder $provider;

    #[Test]
    public function itProvidesContext(): void
    {
        $operation = new Show(context: ['a_key' => 'a_value']);

        $context = $this->provider->buildContext($operation, new Request());

        self:self::assertEquals(['a_key' => 'a_value'], $context);
    }

    protected function setUp(): void
    {
        $this->provider = new OperationContextBuilder();
    }
}
