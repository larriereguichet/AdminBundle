<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

/**
 * A Property describes how an object (usually an entity) is displayed in an operation. Each property type could have its
 * own render template. It can also describe how the associated data should be transformed before passing it to the
 * view.
 *
 * The properties are linked to one or several Resource.
 */
interface PropertyMetadataInterface extends PropertyInterface
{
    /**
     * Define the property name. It should be unique for a resource.
     */
    public function withName(string $property): self;

    /**
     * Define the property path.
     *
     * If the property path is true, the whole object is mapped.
     * If the property path is false, no data will be mapped.
     */
    public function withPropertyPath(string|bool|null $propertyPath): self;

    /**
     * Define the property label.
     */
    public function withLabel(string|bool $label): self;

    /**
     * Define the property view template.
     */
    public function withTemplate(?string $template): self;

    /**
     * Define if data can be sorted using this property.
     */
    public function withSortable(bool $sortable): self;

    /**
     * Define if property data should be translated.
     */
    public function withTranslatable(bool $translatable): self;

    /**
     * Define the property view HTML attributes.
     *
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): self;

    /**
     * Define a property view HTML attribute according to its name.
     */
    public function withAttribute(string $name, mixed $value): self;

    /**
     * Define the property view HTML attributes for the property element container.
     *
     * @param array<string, mixed> $attributes
     */
    public function withRowAttributes(array $attributes): self;

    /**
     * Define the property view HTML attributes for the property element header.
     *
     * @param array<string, mixed> $attributes
     */
    public function withHeaderAttributes(array $attributes): self;

    /**
     * Define the property data transformer. It should be the id of a tagged container service using the
     * "lag_admin.data_transformer" tag.
     */
    public function withDataTransformer(?string $dataTransformer): self;

    /**
     * Define the property permissions.
     *
     * @param array<string, string> $permissions
     */
    public function withPermissions(array $permissions): self;

    /**
     * Define the path by the provider used to sort with this property. Usually it is the Doctrine ORM query path.
     */
    public function withSortingPath(?string $sortingPath): self;
}
