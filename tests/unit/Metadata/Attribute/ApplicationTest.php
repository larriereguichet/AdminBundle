<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Application;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $application = new Application(name: 'admin');

        self::assertSame('admin', $application->getName());
        self::assertSame('d/m/Y', $application->getDateFormat());
        self::assertSame('H:i', $application->getTimeFormat());
        self::assertSame('messages', $application->getTranslationDomain());
        self::assertSame('{application}.{resource}.{message}', $application->getTranslationPattern());
        self::assertSame('{application}.{resource}.{operation}', $application->getRoutePattern());
        self::assertSame('@LAGAdmin/base.html.twig', $application->getBaseTemplate());
        self::assertSame(['ROLE_ADMIN'], $application->getPermissions());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $application = new Application(name: 'admin');

        $new = $application->withName('shop');
        self::assertNotSame($application, $new);
        self::assertSame('shop', $new->getName());
        self::assertSame('admin', $application->getName());

        $new = $application->withDateFormat('Y-m-d');
        self::assertNotSame($application, $new);
        self::assertSame('Y-m-d', $new->getDateFormat());

        $new = $application->withTimeFormat('H:i:s');
        self::assertNotSame($application, $new);
        self::assertSame('H:i:s', $new->getTimeFormat());

        $new = $application->withTranslationDomain('admin');
        self::assertNotSame($application, $new);
        self::assertSame('admin', $new->getTranslationDomain());

        $new = $application->withTranslationPattern('{resource}.{message}');
        self::assertNotSame($application, $new);
        self::assertSame('{resource}.{message}', $new->getTranslationPattern());

        $new = $application->withRoutePattern('{resource}.{operation}');
        self::assertNotSame($application, $new);
        self::assertSame('{resource}.{operation}', $new->getRoutePattern());

        $new = $application->withBaseTemplate('@App/base.html.twig');
        self::assertNotSame($application, $new);
        self::assertSame('@App/base.html.twig', $new->getBaseTemplate());

        $new = $application->withPermissions(['ROLE_SUPER_ADMIN']);
        self::assertNotSame($application, $new);
        self::assertSame(['ROLE_SUPER_ADMIN'], $new->getPermissions());
    }
}
