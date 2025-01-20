<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Context;

use LAG\AdminBundle\Request\ContextBuilder\AjaxContextBuilder;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Resource\Metadata\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class AjaxContextProviderTest extends TestCase
{
    private AjaxContextBuilder $provider;
    private MockObject $decorated;

    #[Test]
    public function itProvidesAjaxContext(): void
    {
        $operation = new Update(ajax: true);
        $request = new Request(server: ['CONTENT_TYPE' => 'application/json']);

        $this->decorated
            ->expects(self::once())
            ->method('buildContext')
            ->with($operation, $request)
            ->willReturn(['a_key' => 'a_value'])
        ;

        $context = $this->provider->buildContext($operation, $request);

        self::assertEquals([
            'a_key' => 'a_value',
            'ajax' => true,
        ], $context);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ContextBuilderInterface::class);
        $this->provider = new AjaxContextBuilder($this->decorated);
    }
}
