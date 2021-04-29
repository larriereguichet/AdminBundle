<?php

namespace LAG\AdminBundle\Field;

use Closure;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\View\FieldView;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

abstract class AbstractField implements FieldInterface
{
    private string $name;
    private string $type;
    private array $options = [];
    private bool $frozen = false;

    public function __construct(string $name, string $type)
    {
        if (class_exists($type)) {
            $type = u($type)->afterLast('\\')->lower();
        }
        $this->name = $name;
        $this->type = $type;
    }

    public function configureDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'attr' => [],
                'header_attr' => [],
                'label' => null,
                'mapped' => false,
                'property_path' => $this->getName(),
                'template' => '@LAGAdmin/fields/auto.html.twig',
                'translation' => false, // Most of fields are values from database and should not be translated
                'translation_domain' => 'admin',
                'sortable' => true,
            ])
            ->setAllowedTypes('attr', ['array', 'null'])
            ->setAllowedTypes('header_attr', ['array', 'null'])
            ->setAllowedTypes('label', ['string', 'null', 'boolean'])
            ->setAllowedTypes('mapped', ['boolean'])
            ->setAllowedTypes('property_path', ['string', 'null'])
            ->setAllowedTypes('template', ['string'])
            ->setAllowedTypes('translation', ['boolean'])
            ->setAllowedTypes('translation_domain', ['string', 'null'])
            ->setAllowedTypes('sortable', ['boolean'])
            ->addNormalizer('attr', function (Options $options, $value) {
                if ($value === null) {
                    $value = [];
                }

                if (!key_exists('class', $value)) {
                    $value['class'] = '';
                }
                $value['class'] .= ' admin-field admin-field-'.$this->getType();

                return $value;
            })
            ->addNormalizer('header_attr', function (Options $options, $value) {
                if ($value === null) {
                    $value = [];
                }

                if (!key_exists('class', $value)) {
                    $value['class'] = '';
                }
                $value['class'] .= ' admin-header admin-header-'.$this->getType();

                return $value;
            })
            ->setNormalizer('mapped', function (Options $options, $mapped) {
                if (u($this->getName())->startsWith('_')) {
                    return true;
                }

                return $mapped;
            })
            ->setNormalizer('property_path', function (Options $options, $propertyPath) {
                if (u($this->getName())->startsWith('_')) {
                    return null;
                }

                return $propertyPath;
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public function setOptions(array $options): void
    {
        if ($this->frozen) {
            throw new Exception('The options for the field "'.$this->name.'" have already been configured');
        }
        $this->options = $options;
        $this->frozen = true;
    }

    public function createView(): View
    {
        return new FieldView(
            $this->name,
            $this->getOption('template'),
            $this->getOptions(),
            $this->getDataTransformer()
        );
    }

    public function getParent(): ?string
    {
        return null;
    }

    public function getDataTransformer(): ?Closure
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        if (!\array_key_exists($name, $this->options)) {
            throw new Exception('Invalid option "'.$name.'" for field "'.$this->name.'"');
        }

        return $this->options[$name];
    }

    public function getType(): string
    {
        return $this->type;
    }
}
