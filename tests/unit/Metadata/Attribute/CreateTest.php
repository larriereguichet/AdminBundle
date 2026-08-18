<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\State\Provider\CreateProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $operation = new Create();

        self::assertSame('create', $operation->getShortName());
        self::assertSame('@LAGAdmin/resources/create.html.twig', $operation->getTemplate());
        self::assertSame(CreateProvider::class, $operation->getProvider());
        self::assertSame(ORMProcessor::class, $operation->getProcessor());
        self::assertSame(['POST', 'GET'], $operation->getMethods());
        self::assertSame('lag_admin.ui.create_success', $operation->getSuccessMessage());
    }
}
