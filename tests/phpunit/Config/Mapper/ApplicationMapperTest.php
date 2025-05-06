<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config\Mapper;

use LAG\AdminBundle\Config\Mapper\ApplicationMapper;
use LAG\AdminBundle\Metadata\Application;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApplicationMapperTest extends TestCase
{
    #[Test]
    public function itCreatesAnApplicationFromAnArray(): void
    {
        $mapper = new ApplicationMapper();
        $application = $mapper->fromArray([
            'name' => 'My application',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'translation_domain' => 'messages',
            'translation_pattern' => '{application}.{message}',
            'route_pattern' => '{application}.{resource}.{operation}',
            'base_template' => 'some_template.html.twig',
            'permissions' => ['ROLE_ADMIN'],
        ]);

        self::assertInstanceOf(Application::class, $application);
        self::assertEquals('My application', $application->getName());
        self::assertEquals('Y-m-d', $application->getDateFormat());
        self::assertEquals('H:i:s', $application->getTimeFormat());
        self::assertEquals('messages', $application->getTranslationDomain());
        self::assertEquals('{application}.{message}', $application->getTranslationPattern());
        self::assertEquals('{application}.{resource}.{operation}', $application->getRoutePattern());
        self::assertEquals('some_template.html.twig', $application->getBaseTemplate());
        self::assertEquals(['ROLE_ADMIN'], $application->getPermissions());
    }

    #[Test]
    public function itConvertsAnApplicationToAnArray(): void
    {
        $mapper = new ApplicationMapper();
        $data = $mapper->toArray(new Application(
            name: 'My application',
            dateFormat: 'Y-m-d',
            timeFormat: 'H:i:s',
            translationDomain: 'messages',
            translationPattern: '{application}.{message}',
            routePattern: '{application}.{resource}.{operation}',
            baseTemplate: 'some_template.html.twig',
            permissions: ['ROLE_ADMIN'],
        ));

        self::assertEquals([
            'name' => 'My application',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'translation_domain' => 'messages',
            'translation_pattern' => '{application}.{message}',
            'route_pattern' => '{application}.{resource}.{operation}',
            'base_template' => 'some_template.html.twig',
            'permissions' => ['ROLE_ADMIN'],
        ], $data);
    }
}
