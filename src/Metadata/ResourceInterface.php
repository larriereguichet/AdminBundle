<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface ResourceInterface
{
    /**
     * Return the resource name. It is the concatenation of the application name and the resource short name.
     */
    public function getName(): string;

    public function getShortName(): ?string;

    public function getResourceClass(): ?string;

    public function getTitle(): ?string;

    public function getGroup(): ?string;

    public function getIcon(): ?string;

    /** @return array<int, OperationInterface> */
    public function getOperations(): array;

    public function hasOperation(string $operationName): bool;

    public function getOperation(string $operationName): OperationInterface;

    /**
     * @return array<CollectionOperationInterface>
     */
    public function getCollectionOperations(): array;

    /** @return array<int|string, PropertyInterface> */
    public function getProperties(): array;

    public function getProperty(string $name): PropertyInterface;

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @return array<int|string, T>
     */
    public function getPropertiesByType(string $type): array;

    public function hasProperties(): bool;

    public function hasProperty(string $name): bool;

    public function getProcessor(): string;

    public function getProvider(): string;

    public function getRoutePattern(): ?string;

    public function getPathPrefix(): ?string;

    /** @return array<string>|null */
    public function getIdentifiers(): ?array;

    public function getTranslationPattern(): ?string;

    public function getTranslationDomain(): ?string;

    public function getApplicationName(): string;

    public function getForm(): ?string;

    /** @return array<string, mixed>|null */
    public function getFormOptions(): ?array;

    public function getFormTemplate(): ?string;

    public function hasValidation(): bool;

    /** @return array<string, mixed>|null */
    public function getValidationContext(): ?array;

    public function hasAjax(): bool;

    /** @return array<string, mixed>|null */
    public function getNormalizationContext(): ?array;

    /** @return array<string, mixed>|null */
    public function getDenormalizationContext(): ?array;

    /** @return array<string>|null */
    public function getPermissions(): ?array;

    public function getInput(): ?string;

    public function getOutput(): ?string;

    public function getApplication(): ApplicationInterface;
}
