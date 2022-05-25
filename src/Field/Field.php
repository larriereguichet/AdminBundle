<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use Closure;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface Field
{
    public const TYPE_AUTO = 'auto';
    public const TYPE_STRING = 'string';
    public const TYPE_TEXT = 'text';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_LINK = 'link';
    public const TYPE_ARRAY = 'array';
    public const TYPE_DATE = 'date';
    public const TYPE_COUNT = 'count';
    public const TYPE_ACTION = 'action';
    public const TYPE_COLLECTION = 'collection';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_MAPPED = 'mapped';
    public const TYPE_ACTION_COLLECTION = 'action_collection';
    public const TYPE_HEADER = 'header';

    /**
     * Return the field name.
     */
    public function getName(): string;

    public function getLabel(): string;

    /**
     * Return the field type.
     */
    public function getType(): string;

    /**
     * Return the field options use to render it.
     */
    public function getOptions(): array;

    /**
     * Get a single option.
     */
    public function getOption(string $name): mixed;

    /**
     * Configure the field options. This method should be called before calling getOptions() , getOption() or
     * configureOptions() methods.
     */
    public function setOptions(array $options): void;

    /**
     * Configure the options to be resolved.
     */
    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * Return the type of the parent of the current field. The parent options wil be merged with the options of the
     * child field.
     */
    public function getParent(): ?string;

    /**
     * Return the field view to be rendered with Twig.
     */
    public function createView(): View;

    public function isSortable(): bool;

    /**
     * Transform the data to be passed to the view.
     */
    public function getDataTransformer(): ?Closure;
}
