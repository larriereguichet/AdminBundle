<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Metadata\Attribute\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UpdateTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $operation = new Update();

        self::assertSame('update', $operation->getShortName());
        self::assertSame('@LAGAdmin/resources/update.html.twig', $operation->getTemplate());
        self::assertSame(ORMProvider::class, $operation->getProvider());
        self::assertSame(ORMProcessor::class, $operation->getProcessor());
        self::assertSame(['POST', 'PUT', 'GET'], $operation->getMethods());
        self::assertSame('lag_admin.ui.process_success', $operation->getSuccessMessage());
    }
}
