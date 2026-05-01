<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\Resource\MissingApplicationException;
use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Factory\ApplicationMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApplicationMetadataFactoryTest extends TestCase
{
    private const array APPLICATION_CONFIG = [
        'name' => 'admin',
        'date_format' => 'd/m/Y',
        'time_format' => 'H:i',
        'translation_domain' => 'messages',
        'translation_pattern' => '{application}.{resource}.{message}',
        'route_pattern' => '{application}.{resource}.{operation}',
        'base_template' => '@LAGAdmin/base.html.twig',
    ];

    #[Test]
    public function itCreatesApplicationMetadata(): void
    {
        $factory = new ApplicationMetadataFactory(['admin' => self::APPLICATION_CONFIG]);
        $application = $factory->createMetadata('admin');

        self::assertInstanceOf(Application::class, $application);
        self::assertSame('admin', $application->getName());
        self::assertSame('d/m/Y', $application->getDateFormat());
        self::assertSame('H:i', $application->getTimeFormat());
        self::assertSame('messages', $application->getTranslationDomain());
        self::assertSame('{application}.{resource}.{message}', $application->getTranslationPattern());
        self::assertSame('{application}.{resource}.{operation}', $application->getRoutePattern());
        self::assertSame('@LAGAdmin/base.html.twig', $application->getBaseTemplate());
    }

    #[Test]
    public function itUsesApplicationNameFromConfigKey(): void
    {
        $config = array_merge(self::APPLICATION_CONFIG, ['name' => null]);
        $factory = new ApplicationMetadataFactory(['my_admin' => $config]);
        $application = $factory->createMetadata('my_admin');

        self::assertSame('my_admin', $application->getName());
    }

    #[Test]
    public function itThrowsExceptionForMissingApplication(): void
    {
        $factory = new ApplicationMetadataFactory([]);

        $this->expectException(MissingApplicationException::class);
        $factory->createMetadata('missing');
    }
}
