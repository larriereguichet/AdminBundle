<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Link;

interface OperationMetadataInterface extends OperationInterface
{
    public function withShortName(?string $shortName): static;

    /** @param  array<string, mixed> $context */
    public function withContext(array $context): static;

    public function withTitle(string|false|null $title): static;

    public function withDescription(?string $description): static;

    public function withIcon(?string $icon): static;

    public function withTemplate(?string $template): static;

    public function withBaseTemplate(string $baseTemplate): static;

    /** @param array<int, string>|null $roles */
    public function withRoles(?array $roles): static;

    public function withController(?string $controller): static;

    public function withRoute(?string $route): static;

    /** @param array<string, mixed> $routeParameters */
    public function withRouteParameters(array $routeParameters): static;

    public function withPath(?string $path): static;

    public function withRedirectRoute(?string $targetRoute): static;

    /** @param array<string, mixed> $targetRouteParameters */
    public function withRedirectRouteParameters(?array $targetRouteParameters): static;

    public function withForm(?string $form): static;

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(?array $formOptions): static;

    public function withFormOption(string $option, mixed $value): static;

    public function withFormTemplate(?string $formTemplate): static;

    public function withProcessor(string $processor): static;

    public function withProvider(string $provider): static;

    /** @param array<string> $methods */
    public function withMethods(array $methods): static;

    /** @param array<string> $identifiers */
    public function withIdentifiers(array $identifiers): static;

    public function setResource(ResourceInterface $resource): static;

    /** @param array<string, Link> $contextualLinks */
    public function withContextualLinks(array $contextualLinks): static;

    /** @param array<Link> $itemLinks */
    public function withItemLinks(array $itemLinks): static;

    public function withRedirectOperation(?string $redirectOperation): static;

    public function withValidation(bool $validation): static;

    /** @param  array<string, mixed> $context */
    public function withValidationContext(array $context): static;

    public function withAjax(?bool $ajax): static;

    /** @param  array<string, mixed> $context */
    public function withNormalizationContext(array $context): static;

    /** @param  array<string, mixed> $context */
    public function withDenormalizationContext(array $context): static;

    public function withInput(?string $input): static;

    public function getOutput(): ?string;

    public function withOutput(?string $output): static;

    public function withWorkflow(?string $workflow): static;

    public function withWorkflowTransition(?string $workflowTransition): static;

    public function withEmbedded(bool $embedded): static;

    public function withSuccessMessage(?string $successMessage): static;
}
