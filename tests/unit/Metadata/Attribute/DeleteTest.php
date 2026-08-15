<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DeleteTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $operation = new Delete();

        self::assertSame('delete', $operation->getShortName());
        self::assertSame('@LAGAdmin/resources/delete.html.twig', $operation->getTemplate());
        self::assertSame(ORMProvider::class, $operation->getProvider());
        self::assertSame(ORMProcessor::class, $operation->getProcessor());
        self::assertSame(['POST', 'GET'], $operation->getMethods());
        self::assertSame(DeleteType::class, $operation->getForm());
        self::assertSame('lag_admin.ui.delete_success', $operation->getSuccessMessage());
    }
}
