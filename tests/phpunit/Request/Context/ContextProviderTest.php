<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Context;

use LAG\AdminBundle\Request\Context\ContextProvider;
use LAG\AdminBundle\Resource\Metadata\Show;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ContextProviderTest extends TestCase
{
    private ContextProvider $provider;

    #[Test]
    public function itProvidesContext(): void
    {
        $operation = new Show(context: ['a_key' => 'a_value']);

        $context = $this->provider->getContext($operation, new Request());

        self:self::assertEquals(['a_key' => 'a_value'], $context);
    }

    protected function setUp(): void
    {
        $this->provider = new ContextProvider();
    }
}
