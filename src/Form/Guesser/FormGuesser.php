<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Guesser;

use LAG\AdminBundle\Form\Type\Text\TextareaType;
use LAG\AdminBundle\Metadata\Attribute\Boolean;
use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\Map;
use LAG\AdminBundle\Metadata\Attribute\RichText;
use LAG\AdminBundle\Metadata\Attribute\Slug;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Title;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final readonly class FormGuesser implements FormGuesserInterface
{
    public function guessFormType(OperationInterface $operation, PropertyInterface $property): ?string
    {
        if (\in_array($property->getName(), $operation->getIdentifiers())) {
            return null;
        }

        if ($property instanceof Text || $property instanceof Slug || $property instanceof Title) {
            return TextType::class;
        }

        if ($property instanceof Boolean) {
            return CheckboxType::class;
        }

        if ($property instanceof Collection) {
            return CollectionType::class;
        }

        if ($property instanceof Date) {
            return DateType::class;
        }

        if ($property instanceof Map) {
            return ChoiceType::class;
        }

        if ($property instanceof RichText) {
            return TextareaType::class;
        }

        return null;
    }

    public function guessFormOptions(OperationInterface $operation, PropertyInterface $property): array
    {
        $options = ['required' => false];

        if (\is_string($property->getPropertyPath()) && $property->getPropertyPath() !== '.') {
            $options['property_path'] = $property->getPropertyPath();
        }

        if ($property instanceof Collection) {
            $options['entry_type'] = $this->guessFormType($operation, $property->getEntryProperty());
            $options['entry_options'] = $this->guessFormOptions($operation, $property->getEntryProperty());
        }

        if ($property instanceof Map) {
            $options['choices'] = array_flip($property->getMap());
        }

        return $options;
    }
}
