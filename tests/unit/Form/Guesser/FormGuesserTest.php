<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Form\Guesser;

use LAG\AdminBundle\Form\Guesser\FormGuesser;
use LAG\AdminBundle\Form\Type\Text\TextareaType;
use LAG\AdminBundle\Metadata\Attribute\Boolean;
use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\Image;
use LAG\AdminBundle\Metadata\Attribute\Map;
use LAG\AdminBundle\Metadata\Attribute\RichText;
use LAG\AdminBundle\Metadata\Attribute\Slug;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Title;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class FormGuesserTest extends TestCase
{
    private FormGuesser $guesser;

    #[Test]
    #[DataProvider('properties')]
    public function itGuessFormTypeAndOptions(
        OperationInterface $operation,
        PropertyInterface $property,
        ?string $expectedFormType,
        array $expectedFormOptions,
    ): void {
        $formType = $this->guesser->guessFormType($operation, $property);
        $formOptions = $this->guesser->guessFormOptions($operation, $property);

        self::assertEquals($expectedFormType, $formType);
        self::assertEquals($expectedFormOptions, $formOptions);
    }

    public static function properties(): iterable
    {
        $operation = new Update(identifiers: ['id']);

        yield [$operation, new Text(), TextType::class, ['required' => false]];
        yield [$operation, new Text(propertyPath: 'name'), TextType::class, ['required' => false, 'property_path' => 'name']];

        yield [$operation, new Boolean(), CheckboxType::class, ['required' => false]];
        yield [$operation, new Boolean(propertyPath: 'enabled'), CheckboxType::class, ['required' => false, 'property_path' => 'enabled']];

        yield [$operation, new Date(), DateType::class, ['required' => false]];
        yield [$operation, new Date(propertyPath: 'publishedAt'), DateType::class, ['required' => false, 'property_path' => 'publishedAt']];

        yield [
            $operation,
            new Map(map: ['value' => 'label']),
            ChoiceType::class,
            ['required' => false, 'choices' => ['label' => 'value']],
        ];
        yield [
            $operation,
            new Map(propertyPath: 'state', map: ['value' => 'label']),
            ChoiceType::class,
            ['required' => false, 'property_path' => 'state', 'choices' => ['label' => 'value']],
        ];

        yield [$operation, new RichText(), TextareaType::class, ['required' => false]];
        yield [$operation, new RichText(propertyPath: 'description'), TextareaType::class, ['required' => false, 'property_path' => 'description']];

        yield [$operation, new Slug(), TextType::class, ['required' => false]];
        yield [$operation, new Title(), TextType::class, ['required' => false]];

        $entryProperty = new Text(name: 'item');
        $collectionProperty = new Collection(name: 'tags', entryProperty: $entryProperty);
        yield [
            $operation,
            $collectionProperty,
            CollectionType::class,
            ['required' => false, 'entry_type' => TextType::class, 'entry_options' => ['required' => false]],
        ];

        $operation2 = new Update(identifiers: ['id', 'uuid']);
        $idProperty = new Text(name: 'id');
        yield [$operation2, $idProperty, null, ['required' => false]];

        yield [$operation, new Image(name: 'cover'), null, ['required' => false]];
    }

    protected function setUp(): void
    {
        $this->guesser = new FormGuesser();
    }
}
