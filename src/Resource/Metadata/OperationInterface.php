<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Security\PermissibleInterface;
use LAG\AdminBundle\Workflow\WorkflowSubjectInterface;
use LAG\AdminBundle\Workflow\WorkflowTransitionSubjectInterface;

interface OperationInterface extends PermissibleInterface, WorkflowSubjectInterface, WorkflowTransitionSubjectInterface
{
    public function getName(): ?string;

    public function withName(?string $name): self;

    public function getContext(): array;

    public function withContext(array $context): self;

    public function getTitle(): ?string;

    public function withTitle(?string $title): self;

    public function getDescription(): ?string;

    public function withDescription(?string $description): self;

    public function getIcon(): ?string;

    public function withIcon(?string $icon): self;

    public function getTemplate(): ?string;

    public function withTemplate(?string $template): self;

    public function getBaseTemplate(): ?string;

    public function withBaseTemplate(string $baseTemplate): self;

    public function withPermissions(?array $permissions): self;

    public function getController(): ?string;

    public function withController(?string $controller): self;

    public function getRoute(): ?string;

    public function withRoute(?string $route): self;

    public function getRouteParameters(): ?array;

    public function withRouteParameters(?array $routeParameters): self;

    public function getPath(): ?string;

    public function withPath(?string $path): self;

    public function getRedirectRoute(): ?string;

    public function withRedirectRoute(?string $targetRoute): self;

    public function getRedirectRouteParameters(): ?array;

    public function withRedirectRouteParameters(?array $targetRouteParameters): self;

    public function getForm(): ?string;

    public function withForm(?string $form): self;

    public function getFormOptions(): ?array;

    public function withFormOptions(?array $formOptions): self;

    public function getFormTemplate(): ?string;

    public function withFormTemplate(?string $formTemplate): self;

    public function getProcessor(): string;

    public function withProcessor(string $processor): self;

    public function getProvider(): string;

    public function withProvider(string $provider): self;

    public function getMethods(): array;

    public function withMethods(array $methods): self;

    /** @return string[]|null */
    public function getIdentifiers(): ?array;

    public function withIdentifiers(array $identifiers): self;

    public function getResource(): ?Resource;

    public function withResource(?Resource $resource): self;

    /** @return Link[]|null */
    public function getContextualActions(): ?array;

    public function withContextualActions(array $contextualActions): self;

    /** @return Link[]|null */
    public function getItemActions(): ?array;

    /** @param array $itemActions Link[]|null */
    public function withItemActions(array $itemActions): self;

    public function getRedirectApplication(): ?string;

    public function withRedirectApplication(?string $redirectApplication): self;

    public function getRedirectResource(): ?string;

    public function withRedirectResource(?string $redirectResource): self;

    public function getRedirectOperation(): ?string;

    public function withRedirectOperation(?string $redirectOperation): self;

    public function useValidation(): ?bool;

    public function withValidation(bool $validation): self;

    public function getValidationContext(): ?array;

    public function withValidationContext(array $context): self;

    public function useAjax(): ?bool;

    public function withAjax(?bool $ajax): self;

    public function getNormalizationContext(): ?array;

    public function withNormalizationContext(array $context): self;

    public function getDenormalizationContext(): ?array;

    public function withDenormalizationContext(array $context): self;

    public function getInput(): ?string;

    public function withInput(?string $input): self;

    public function getOutput(): ?string;

    public function withOutput(?string $output): self;

    public function setWorkflow(?string $workflow): self;

    public function getWorkflowTransition(): ?string;

    public function setWorkflowTransition(?string $workflowTransition): self;

    public function isPartial(): bool;

    public function withPartial(bool $partial): self;

    public function getSuccessMessage(): ?string;

    public function withSuccessMessage(?string $successMessage): self;
}
