<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Condition\ConditionalInterface;
use LAG\AdminBundle\Security\RolesOwnerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * A Property describes how an object (usually an entity) is displayed in an operation. Each property type could have its
 * own render template. It can also describe how the associated data should be transformed before passing it to the
 * view.
 *
 * The properties are linked to one or several Resource.
 */
interface PropertyInterface extends RolesOwnerInterface, ConditionalInterface
{
    /**
     * Return the property name. It should be unique for a resource.
     */
    public function getName(): ?string;

    /**
     * Return the property path used to retrieve property data from the object. The property path used the
     * PropertyAccess syntax.
     *
     * If the property path is true, the whole object is mapped.
     * If the property path is false, no data will be mapped.
     *
     * @see PropertyAccessorInterface
     */
    public function getPropertyPath(): string|bool|null;

    /**
     * Return the property label. The label could be rendered differently according to the current Grid.
     */
    public function getLabel(): string|bool|null;

    /**
     * Return the property view template.
     */
    public function getTemplate(): ?string;

    /**
     * Return true if data can be sorted using this property.
     */
    public function isSortable(): bool;

    /**
     * Return true if property data should be translated.
     */
    public function isTranslatable(): bool;

    /**
     * Return the property view HTML attributes.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * Return a property view HTML attribute according to its name.
     */
    public function getAttribute(string $name): mixed;

    /**
     * Return the property view HTML attributes for the property element container.
     *
     * @return array<string, mixed>
     */
    public function getRowAttributes(): array;

    /**
     * Return the property view HTML attributes for the property element header.
     *
     * @return array<string, mixed>
     */
    public function getHeaderAttributes(): array;

    /**
     * Return the property data transformer.
     */
    public function getDataTransformer(): ?string;

    public function getSortingPath(): ?string;
}
