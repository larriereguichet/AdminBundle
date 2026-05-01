<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface ResourceMetadataInterface extends ResourceInterface
{
    public function withShortName(?string $shortName): self;

    public function withResourceClass(?string $resourceClass): self;

    public function withTitle(string $title): self;

    public function withGroup(string $group): self;

    public function getIcon(): ?string;

    public function withIcon(string $icon): self;

    /** @return array<int|string, OperationMetadataInterface> */
    public function getOperations(): array;

    /** @param array<int|string, OperationMetadataInterface> $operations */
    public function withOperations(array $operations): self;

    /** @param array<int|string, PropertyInterface> $properties */
    public function withProperties(array $properties): self;

    public function withProcessor(string $processor): self;

    public function withProvider(string $provider): self;

    public function withRoutePattern(string $routePattern): self;

    public function withPathPrefix(?string $prefix): self;

    /** @param array<string> $identifiers */
    public function withIdentifiers(array $identifiers): self;

    public function withTranslationPattern(?string $translationPattern): self;

    public function withTranslationDomain(?string $translationDomain): self;

    public function withApplication(?string $application): self;

    public function withForm(?string $form): self;

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(?array $formOptions): self;

    public function withFormTemplate(?string $formTemplate): self;

    public function withValidation(bool $validation): self;

    /** @param array<string, mixed> $context */
    public function withValidationContext(array $context): self;

    public function withAjax(bool $ajax): self;

    /** @param array<string, mixed> $context */
    public function withNormalizationContext(array $context): self;

    /** @param array<string, mixed> $context */
    public function withDenormalizationContext(array $context): self;

    /** @param array<string> $permissions */
    public function withPermissions(array $permissions): self;

    public function withInput(?string $input): self;

    public function withOutput(?string $output): self;
}
