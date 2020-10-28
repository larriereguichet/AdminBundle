<?php

namespace LAG\AdminBundle\Field;

use Closure;
use LAG\AdminBundle\Field\View\FieldView;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldInterface
{
    const TYPE_AUTO = 'auto';
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';
    const TYPE_COUNT = 'count';
    const TYPE_ACTION = 'action';
    const TYPE_COLLECTION = 'collection';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MAPPED = 'mapped';
    const TYPE_ACTION_COLLECTION = 'action_collection';
    const TYPE_HEADER = 'header';

    /**
     * Return the field name.
     */
    public function getName(): string;

    /**
     * Return the field options use to render it.
     */
    public function getOptions(): array;

    /**
     * Get a single option.
     *
     * @return mixed
     */
    public function getOption(string $name);

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
     *
     * @return FieldView
     */
    public function createView(): View;

    /**
     * Transform the data to be passed to the view.
     */
    public function getDataTransformer(): ?Closure;
}
