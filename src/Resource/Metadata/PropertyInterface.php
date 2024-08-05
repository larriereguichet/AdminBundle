<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * A Property describes how an object (usually an entity) is displayed in an operation. Each property type could have its
 * own render template. It can also describe how the associated data should be transformed before passing it to the
 * view.
 *
 * The properties are linked to one or several Resource.
 */
interface PropertyInterface
{
    /**
     * Return the property name. It should be unique for a resource.
     */
    public function getName(): ?string;

    /**
     * Define the property name. It should be unique for a resource.
     */
    public function withName(string $property): self;

    /**
     * Return the property path used to retrieve property data from the object. The property path used the
     * PropertyAccess syntax.
     *
     * If the property path is true, the whole object is mapped.
     * If the property path is false, no data will be mapped.
     *
     * @see PropertyAccessorInterface
     */
    public function getPropertyPath(): string|null|bool;

    /**
     * Define the property path.
     *
     * If the property path is true, the whole object is mapped.
     * If the property path is false, no data will be mapped.
     */
    public function withPropertyPath(string|bool|null $propertyPath): self;

    /**
     * Return the property label. The label could be rendered differently according to the current Grid.
     */
    public function getLabel(): string|null|bool;

    /**
     * Define the property label.
     */
    public function withLabel(string|bool $label): self;

    /**
     * Return the property view template.
     */
    public function getTemplate(): ?string;

    /**
     * Define the property view template.
     */
    public function withTemplate(?string $template): self;

    /**
     * Return true if data can be sorted using this property.
     */
    public function isSortable(): bool;

    /**
     * Define if data can be sorted using this property.
     */
    public function withSortable(bool $sortable): self;

    /**
     * Return true if property data should be translated.
     */
    public function isTranslatable(): bool;

    /**
     * Return the property translation domain. It can override the operation one.
     */
    public function getTranslationDomain(): ?string;

    /**
     * Define the property translation domain.
     */
    public function withTranslationDomain(?string $translationDomain): self;

    /**
     * Define if property data should be translated.
     */
    public function withTranslatable(bool $translatable): self;

    /**
     * Return the property view html attributes.
     */
    public function getAttributes(): array;

    /**
     * Define the property view html attributes.
     */
    public function withAttributes(array $attributes): self;

    /**
     * Return a property view html attribute according to its name.
     */
    public function getAttribute(string $name): mixed;

    /**
     * Define a property view html attribute according to its name.
     */
    public function withAttribute(string $name, mixed $value): self;

    /**
     * Return the property view html attributes for the property element container.
     */
    public function getContainerAttributes(): array;

    /**
     * Define the property view html attributes for the property element container.
     */
    public function withContainerAttributes(array $attributes): self;

    /**
     * Return the property view html attributes for the property element header.
     */
    public function getHeaderAttributes(): array;

    /**
     * Define the property view html attributes for the property element header.
     */
    public function withHeaderAttributes(array $headerAttributes): self;

    /**
     * Return the property data transformer.
     */
    public function getDataTransformer(): ?string;

    /**
     * Define the property data transformer. It should be the id of a tagged container service using the
     * "lag_admin.data_transformer" tag.
     */
    public function withDataTransformer(?string $dataTransformer): self;
}
