<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Action;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Security\RolesOwnerInterface;
use LAG\AdminBundle\Workflow\WorkflowSubjectInterface;
use LAG\AdminBundle\Workflow\WorkflowTransitionSubjectInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

interface OperationInterface extends RolesOwnerInterface, WorkflowSubjectInterface, WorkflowTransitionSubjectInterface
{
    public function getName(): string;

    public function getFullName(): ?string;

    public function withName(?string $name): static;

    /** @return array<string, mixed> */
    public function getContext(): array;

    /** @param  array<string, mixed> $context */
    public function withContext(array $context): static;

    public function getTitle(): ?string;

    public function withTitle(?string $title): static;

    public function getDescription(): ?string;

    public function withDescription(?string $description): static;

    public function getIcon(): ?string;

    public function withIcon(?string $icon): static;

    public function getTemplate(): ?string;

    public function withTemplate(?string $template): static;

    public function getBaseTemplate(): ?string;

    public function withBaseTemplate(string $baseTemplate): static;

    /** @param  array<string, mixed> $permissions */
    public function withPermissions(?array $permissions): static;

    public function getController(): ?string;

    public function withController(?string $controller): static;

    public function getRoute(): ?string;

    public function withRoute(?string $route): static;

    /** @return array<string, mixed>|null */
    public function getRouteParameters(): ?array;

    /** @param array<string, mixed> $routeParameters */
    public function withRouteParameters(array $routeParameters): static;

    public function getPath(): ?string;

    public function withPath(?string $path): static;

    public function getRedirectRoute(): ?string;

    public function withRedirectRoute(?string $targetRoute): static;

    /** @return array<string, mixed>|null */
    public function getRedirectRouteParameters(): ?array;

    /** @param array<string, mixed> $targetRouteParameters */
    public function withRedirectRouteParameters(?array $targetRouteParameters): static;

    public function getForm(): ?string;

    public function withForm(?string $form): static;

    /** @return array<string, mixed>|null */
    public function getFormOptions(): ?array;

    /** @param array<string, mixed> $formOptions */
    public function withFormOptions(?array $formOptions): static;

    public function getFormOption(string $option): mixed;

    public function withFormOption(string $option, mixed $value): static;

    public function getFormTemplate(): ?string;

    public function withFormTemplate(?string $formTemplate): static;

    public function getProcessor(): string;

    public function withProcessor(string $processor): static;

    public function getProvider(): string;

    public function withProvider(string $provider): static;

    /** @return array<string> */
    public function getMethods(): array;

    /** @param array<string> $methods */
    public function withMethods(array $methods): static;

    /** @return string[]|null */
    public function getIdentifiers(): ?array;

    /** @param array<string> $identifiers */
    public function withIdentifiers(array $identifiers): static;

    #[Ignore]
    public function getResource(): Resource;

    public function setResource(Resource $resource): static;

    /** @return Action[]|null */
    public function getContextualActions(): ?array;

    /** @param array<string, Action> $contextualActions */
    public function withContextualActions(array $contextualActions): static;

    /** @return Action[]|null */
    public function getItemActions(): ?array;

    /** @param array<Action> $itemActions */
    public function withItemActions(array $itemActions): static;

    public function getRedirectOperation(): ?string;

    public function withRedirectOperation(?string $redirectOperation): static;

    public function hasValidation(): ?bool;

    public function withValidation(bool $validation): static;

    /** @return array<string, mixed>|null */
    public function getValidationContext(): ?array;

    /** @param  array<string, mixed> $context */
    public function withValidationContext(array $context): static;

    public function hasAjax(): ?bool;

    public function withAjax(?bool $ajax): static;

    /** @return array<string, mixed>|null */
    public function getNormalizationContext(): ?array;

    /** @param  array<string, mixed> $context */
    public function withNormalizationContext(array $context): static;

    /** @return array<string, mixed>|null */
    public function getDenormalizationContext(): ?array;

    /** @param  array<string, mixed> $context */
    public function withDenormalizationContext(array $context): static;

    public function getInput(): ?string;

    public function withInput(?string $input): static;

    public function getOutput(): ?string;

    public function withOutput(?string $output): static;

    public function setWorkflow(?string $workflow): static;

    public function getWorkflowTransition(): ?string;

    public function setWorkflowTransition(?string $workflowTransition): static;

    public function canBeEmbedded(): bool;

    public function withEmbedded(bool $embedded): static;

    public function getSuccessMessage(): ?string;

    public function withSuccessMessage(?string $successMessage): static;
}
