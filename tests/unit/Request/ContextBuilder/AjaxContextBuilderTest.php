<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Request\ContextBuilder\JsonContextBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class AjaxContextBuilderTest extends TestCase
{
    private JsonContextBuilder $provider;

    #[Test]
    public function itProvidesAjaxContext(): void
    {
        $operation = new Update(ajax: true);
        $request = new Request(server: ['CONTENT_TYPE' => 'application/json']);
        $context = $this->provider->buildContext($operation, $request);

        self::assertEquals([
            'json' => true,
        ], $context);
    }

    protected function setUp(): void
    {
        $this->provider = new JsonContextBuilder();
    }
}
