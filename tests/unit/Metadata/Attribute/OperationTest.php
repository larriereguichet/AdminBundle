<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\MissingOperationResourceException;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OperationTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultShortName(): void
    {
        self::assertSame('index', (new Index())->getShortName());
        self::assertSame('show', (new Show())->getShortName());
    }

    #[Test]
    public function itThrowsWhenGettingNameWithoutResource(): void
    {
        $this->expectException(MissingOperationResourceException::class);
        (new Index())->getName();
    }

    #[Test]
    public function itReturnsNameAfterResourceIsSet(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);
        $operation = new Index();
        $operation->setResource($resource);

        self::assertSame('admin.book.index', $operation->getName());
    }

    #[Test]
    public function itAllowsSettingSameResourceTwice(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);
        $operation = new Index();
        $operation->setResource($resource);
        $operation->setResource($resource);

        self::assertSame('admin.book.index', $operation->getName());
    }

    #[Test]
    public function itThrowsWhenChangingToDifferentResource(): void
    {
        $resource1 = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);
        $resource2 = new Resource(shortName: 'author', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);
        $operation = new Index();
        $operation->setResource($resource1);

        $this->expectException(Exception::class);
        $operation->setResource($resource2);
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $operation = new Index();

        $new = $operation->withShortName('custom_index');
        self::assertNotSame($operation, $new);
        self::assertSame('custom_index', $new->getShortName());
        self::assertSame('index', $operation->getShortName());

        $new = $operation->withTitle('My Title');
        self::assertNotSame($operation, $new);
        self::assertSame('My Title', $new->getTitle());
        self::assertNull($operation->getTitle());

        $new = $operation->withRoute('admin.book.index');
        self::assertNotSame($operation, $new);
        self::assertSame('admin.book.index', $new->getRoute());
        self::assertNull($operation->getRoute());

        $new = $operation->withPath('/books/index');
        self::assertNotSame($operation, $new);
        self::assertSame('/books/index', $new->getPath());

        $new = $operation->withPermissions(['ROLE_ADMIN']);
        self::assertNotSame($operation, $new);
        self::assertSame(['ROLE_ADMIN'], $new->getRoles());

        $new = $operation->withBaseTemplate('@LAGAdmin/base.html.twig');
        self::assertNotSame($operation, $new);
        self::assertSame('@LAGAdmin/base.html.twig', $new->getBaseTemplate());

        $new = $operation->withIdentifiers(['id']);
        self::assertNotSame($operation, $new);
        self::assertSame(['id'], $new->getIdentifiers());

        $new = $operation->withRouteParameters(['id' => 'id']);
        self::assertNotSame($operation, $new);
        self::assertSame(['id' => 'id'], $new->getRouteParameters());

        $new = $operation->withRedirectRoute('admin.book.index');
        self::assertNotSame($operation, $new);
        self::assertSame('admin.book.index', $new->getRedirectRoute());

        $new = $operation->withRedirectOperation('index');
        self::assertNotSame($operation, $new);
        self::assertSame('index', $new->getRedirectOperation());

        $new = $operation->withValidation(false);
        self::assertNotSame($operation, $new);
        self::assertFalse($new->hasValidation());

        $new = $operation->withAjax(false);
        self::assertNotSame($operation, $new);
        self::assertFalse($new->hasAjax());
    }

    #[Test]
    public function itReturnsFormOptionByKey(): void
    {
        $operation = (new Index())->withFormOptions(['csrf_protection' => false]);

        self::assertFalse($operation->getFormOption('csrf_protection'));
        self::assertNull($operation->getFormOption('missing_key'));
    }

    #[Test]
    public function itAddsASingleFormOption(): void
    {
        $operation = (new Index())->withFormOption('csrf_protection', false);

        self::assertFalse($operation->getFormOption('csrf_protection'));
    }

    #[Test]
    public function itThrowsWhenGettingResourceNotSet(): void
    {
        $this->expectException(Exception::class);
        (new Index())->getResource();
    }

    #[Test]
    public function itReturnsResourceAfterItIsSet(): void
    {
        $resource = new Resource(shortName: 'book', application: 'admin', resourceClass: \stdClass::class, operations: [new Index()]);
        $operation = new Index();
        $operation->setResource($resource);

        self::assertSame($resource, $operation->getResource());
    }

    #[Test]
    public function itReturnsImmutableCopiesForRemainingWithMethods(): void
    {
        $operation = new Index();

        $new = $operation->withContext(['locale' => 'fr']);
        self::assertNotSame($operation, $new);
        self::assertSame(['locale' => 'fr'], $new->getContext());
        self::assertSame([], $operation->getContext());

        $new = $operation->withDescription('List all books');
        self::assertNotSame($operation, $new);
        self::assertSame('List all books', $new->getDescription());
        self::assertNull($operation->getDescription());

        $new = $operation->withIcon('list');
        self::assertNotSame($operation, $new);
        self::assertSame('list', $new->getIcon());
        self::assertNull($operation->getIcon());

        $new = $operation->withTemplate('@App/index.html.twig');
        self::assertNotSame($operation, $new);
        self::assertSame('@App/index.html.twig', $new->getTemplate());

        $new = $operation->withController('App\Controller\BookController');
        self::assertNotSame($operation, $new);
        self::assertSame('App\Controller\BookController', $new->getController());

        $new = $operation->withRedirectRouteParameters(['id' => 'id']);
        self::assertNotSame($operation, $new);
        self::assertSame(['id' => 'id'], $new->getRedirectRouteParameters());

        $new = $operation->withForm('App\Form\BookType');
        self::assertNotSame($operation, $new);
        self::assertSame('App\Form\BookType', $new->getForm());

        $new = $operation->withFormOptions(['csrf_protection' => false]);
        self::assertNotSame($operation, $new);
        self::assertSame(['csrf_protection' => false], $new->getFormOptions());

        $new = $operation->withFormTemplate('@App/form.html.twig');
        self::assertNotSame($operation, $new);
        self::assertSame('@App/form.html.twig', $new->getFormTemplate());

        $new = $operation->withProcessor('App\State\Processor\BookProcessor');
        self::assertNotSame($operation, $new);
        self::assertSame('App\State\Processor\BookProcessor', $new->getProcessor());

        $new = $operation->withProvider('App\State\Provider\BookProvider');
        self::assertNotSame($operation, $new);
        self::assertSame('App\State\Provider\BookProvider', $new->getProvider());

        $new = $operation->withMethods(['GET']);
        self::assertNotSame($operation, $new);
        self::assertSame(['GET'], $new->getMethods());

        $new = $operation->withContextualLinks([]);
        self::assertNotSame($operation, $new);
        self::assertSame([], $new->getContextualLinks());

        $new = $operation->withItemLinks([]);
        self::assertNotSame($operation, $new);
        self::assertSame([], $new->getItemLinks());

        $new = $operation->withValidationContext(['groups' => ['Default']]);
        self::assertNotSame($operation, $new);
        self::assertSame(['groups' => ['Default']], $new->getValidationContext());

        $new = $operation->withNormalizationContext(['groups' => ['read']]);
        self::assertNotSame($operation, $new);
        self::assertSame(['groups' => ['read']], $new->getNormalizationContext());

        $new = $operation->withDenormalizationContext(['groups' => ['write']]);
        self::assertNotSame($operation, $new);
        self::assertSame(['groups' => ['write']], $new->getDenormalizationContext());

        $new = $operation->withInput('App\Dto\BookInput');
        self::assertNotSame($operation, $new);
        self::assertSame('App\Dto\BookInput', $new->getInput());

        $new = $operation->withOutput('App\Dto\BookOutput');
        self::assertNotSame($operation, $new);
        self::assertSame('App\Dto\BookOutput', $new->getOutput());

        $new = $operation->withWorkflow('publishing');
        self::assertNotSame($operation, $new);
        self::assertSame('publishing', $new->getWorkflow());

        $new = $operation->withWorkflowTransition('publish');
        self::assertNotSame($operation, $new);
        self::assertSame('publish', $new->getWorkflowTransition());

        $new = $operation->withEmbedded(true);
        self::assertNotSame($operation, $new);
        self::assertTrue($new->isEmbedded());
        self::assertFalse($operation->isEmbedded());

        $new = $operation->withSuccessMessage('Book saved!');
        self::assertNotSame($operation, $new);
        self::assertSame('Book saved!', $new->getSuccessMessage());
    }
}
