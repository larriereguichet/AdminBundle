<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\OperationMissingException;
use LAG\AdminBundle\Exception\Resource\MissingResourceNameException;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceTest extends TestCase
{
    #[Test]
    public function itThrowsWhenGettingNameWithNullShortName(): void
    {
        $resource = new Resource(application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $this->expectException(MissingResourceNameException::class);
        $resource->getName();
    }

    #[Test]
    public function itReturnsNameAsApplicationDotShortName(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        self::assertSame('admin.book', $resource->getName());
    }

    #[Test]
    public function itChecksWhetherOperationExists(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Show()],
        );

        self::assertTrue($resource->hasOperation('index'));
        self::assertTrue($resource->hasOperation('show'));
        self::assertFalse($resource->hasOperation('create'));
    }

    #[Test]
    public function itThrowsWhenGettingMissingOperation(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $this->expectException(OperationMissingException::class);
        $resource->getOperation('show');
    }

    #[Test]
    public function itGetsOperationByShortName(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Show()],
        );

        self::assertSame('index', $resource->getOperation('index')->getShortName());
        self::assertSame('show', $resource->getOperation('show')->getShortName());
    }

    #[Test]
    public function itFiltersCollectionOperations(): void
    {
        $index = new Index();
        $show = new Show();
        $create = new Create();
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$index, $show, $create],
        );

        $collectionOps = $resource->getCollectionOperations();

        self::assertCount(1, $collectionOps);
        self::assertContains($index, $collectionOps);
        self::assertNotContains($show, $collectionOps);
        self::assertNotContains($create, $collectionOps);
    }

    #[Test]
    public function itChecksWhetherPropertyExists(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
            properties: [new Text('title')],
        );

        self::assertTrue($resource->hasProperty('title'));
        self::assertFalse($resource->hasProperty('author'));
    }

    #[Test]
    public function itThrowsWhenGettingMissingProperty(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $this->expectException(Exception::class);
        $resource->getProperty('title');
    }

    #[Test]
    public function itGetsPropertyByName(): void
    {
        $title = new Text('title');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
            properties: [$title],
        );

        self::assertSame($title, $resource->getProperty('title'));
    }

    #[Test]
    public function itIndexesPropertiesByName(): void
    {
        $title = new Text('title');
        $author = new Text('author');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
            properties: [$title, $author],
        );
        $properties = $resource->getProperties();

        self::assertArrayHasKey('title', $properties);
        self::assertArrayHasKey('author', $properties);
        self::assertTrue($resource->hasProperties());
    }

    #[Test]
    public function itHasNoPropertiesWhenEmpty(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        self::assertFalse($resource->hasProperties());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $new = $resource->withShortName('article');
        self::assertNotSame($resource, $new);
        self::assertSame('article', $new->getShortName());
        self::assertSame('book', $resource->getShortName());

        $new = $resource->withApplication('shop');
        self::assertNotSame($resource, $new);
        self::assertSame('shop', $new->getApplication());

        $new = $resource->withResourceClass(\stdClass::class);
        self::assertNotSame($resource, $new);

        $new = $resource->withTitle('Books');
        self::assertNotSame($resource, $new);
        self::assertSame('Books', $new->getTitle());

        $new = $resource->withRoles(['ROLE_ADMIN']);
        self::assertNotSame($resource, $new);
        self::assertSame(['ROLE_ADMIN'], $new->getRoles());

        $new = $resource->withIdentifiers(['uuid']);
        self::assertNotSame($resource, $new);
        self::assertSame(['uuid'], $new->getIdentifiers());

        $new = $resource->withValidation(false);
        self::assertNotSame($resource, $new);
        self::assertFalse($new->hasValidation());

        $new = $resource->withAjax(false);
        self::assertNotSame($resource, $new);
        self::assertFalse($new->hasAjax());
    }

    #[Test]
    public function itReturnsResourceClass(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        self::assertSame(\stdClass::class, $resource->getResourceClass());
    }

    #[Test]
    public function itReturnsAllOperations(): void
    {
        $index = new Index();
        $show = new Show();
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$index, $show],
        );

        $operations = $resource->getOperations();
        self::assertCount(2, $operations);
    }

    #[Test]
    public function itReplacesOperationsViaWithOperations(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $new = $resource->withOperations([new Show()]);
        self::assertNotSame($resource, $new);
        self::assertTrue($new->hasOperation('show'));
        self::assertFalse($new->hasOperation('index'));
    }

    #[Test]
    public function itFiltersPropertiesByType(): void
    {
        $title = new Text('title');
        $createdAt = new Date('createdAt');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
            properties: [$title, $createdAt],
        );

        $textProperties = $resource->getPropertiesByType(Text::class);
        self::assertNotEmpty($textProperties);
        foreach ($textProperties as $property) {
            self::assertInstanceOf(Text::class, $property);
        }

        $dateProperties = $resource->getPropertiesByType(Date::class);
        self::assertNotEmpty($dateProperties);
        foreach ($dateProperties as $property) {
            self::assertInstanceOf(Date::class, $property);
        }
    }

    #[Test]
    public function itReplacesPropertiesViaWithProperties(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
            properties: [new Text('title')],
        );

        $author = new Text('author');
        $new = $resource->withProperties([$author]);
        self::assertNotSame($resource, $new);
        self::assertTrue($new->hasProperty('author'));
        self::assertFalse($new->hasProperty('title'));
    }

    #[Test]
    public function itReturnsImmutableCopiesForRemainingWithMethods(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);

        $new = $resource->withGroup('catalog');
        self::assertNotSame($resource, $new);
        self::assertSame('catalog', $new->getGroup());
        self::assertNull($resource->getGroup());

        $new = $resource->withIcon('book');
        self::assertNotSame($resource, $new);
        self::assertSame('book', $new->getIcon());
        self::assertNull($resource->getIcon());

        $new = $resource->withProcessor('App\State\Processor\BookProcessor');
        self::assertNotSame($resource, $new);
        self::assertSame('App\State\Processor\BookProcessor', $new->getProcessor());

        $new = $resource->withProvider('App\State\Provider\BookProvider');
        self::assertNotSame($resource, $new);
        self::assertSame('App\State\Provider\BookProvider', $new->getProvider());

        $new = $resource->withRoutePattern('{resource}.{operation}');
        self::assertNotSame($resource, $new);
        self::assertSame('{resource}.{operation}', $new->getRoutePattern());

        $new = $resource->withPathPrefix('/admin');
        self::assertNotSame($resource, $new);
        self::assertSame('/admin', $new->getPathPrefix());

        $new = $resource->withTranslationPattern('{resource}.{message}');
        self::assertNotSame($resource, $new);
        self::assertSame('{resource}.{message}', $new->getTranslationPattern());

        $new = $resource->withTranslationDomain('admin');
        self::assertNotSame($resource, $new);
        self::assertSame('admin', $new->getTranslationDomain());

        $new = $resource->withForm('App\Form\BookType');
        self::assertNotSame($resource, $new);
        self::assertSame('App\Form\BookType', $new->getForm());

        $new = $resource->withFormOptions(['csrf_protection' => false]);
        self::assertNotSame($resource, $new);
        self::assertSame(['csrf_protection' => false], $new->getFormOptions());

        $new = $resource->withFormTemplate('@App/form.html.twig');
        self::assertNotSame($resource, $new);
        self::assertSame('@App/form.html.twig', $new->getFormTemplate());

        $new = $resource->withValidationContext(['groups' => ['Default']]);
        self::assertNotSame($resource, $new);
        self::assertSame(['groups' => ['Default']], $new->getValidationContext());

        $new = $resource->withNormalizationContext(['groups' => ['read']]);
        self::assertNotSame($resource, $new);
        self::assertSame(['groups' => ['read']], $new->getNormalizationContext());

        $new = $resource->withDenormalizationContext(['groups' => ['write']]);
        self::assertNotSame($resource, $new);
        self::assertSame(['groups' => ['write']], $new->getDenormalizationContext());

        $new = $resource->withInput('App\Dto\BookInput');
        self::assertNotSame($resource, $new);
        self::assertSame('App\Dto\BookInput', $new->getInput());

        $new = $resource->withOutput('App\Dto\BookOutput');
        self::assertNotSame($resource, $new);
        self::assertSame('App\Dto\BookOutput', $new->getOutput());
    }
}
