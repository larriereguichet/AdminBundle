<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Security\PermissibleInterface;
use LAG\AdminBundle\Workflow\WorkflowSubjectInterface;
use LAG\AdminBundle\Workflow\WorkflowTransitionSubjectInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

interface OperationInterface extends PermissibleInterface, WorkflowSubjectInterface, WorkflowTransitionSubjectInterface
{
    public function getName(): string;

    public function getFullName(): ?string;

    public function withName(?string $name): static;

    public function getContext(): array;

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

    public function withPermissions(?array $permissions): static;

    public function getController(): ?string;

    public function withController(?string $controller): static;

    public function getRoute(): ?string;

    public function withRoute(?string $route): static;

    public function getRouteParameters(): ?array;

    public function withRouteParameters(?array $routeParameters): static;

    public function getPath(): ?string;

    public function withPath(?string $path): static;

    public function getRedirectRoute(): ?string;

    public function withRedirectRoute(?string $targetRoute): static;

    public function getRedirectRouteParameters(): ?array;

    public function withRedirectRouteParameters(?array $targetRouteParameters): static;

    public function getForm(): ?string;

    public function withForm(?string $form): static;

    public function getFormOptions(): ?array;

    public function withFormOptions(?array $formOptions): static;

    public function getFormOption(string $option): mixed;

    public function withFormOption(string $option, mixed $value): static;

    public function getFormTemplate(): ?string;

    public function withFormTemplate(?string $formTemplate): static;

    public function getProcessor(): string;

    public function withProcessor(string $processor): static;

    public function getProvider(): string;

    public function withProvider(string $provider): static;

    public function getMethods(): array;

    public function withMethods(array $methods): static;

    /** @return string[]|null */
    public function getIdentifiers(): ?array;

    public function withIdentifiers(array $identifiers): static;

    #[Ignore]
    public function getResource(): ?Resource;

    public function setResource(Resource $resource): static;

    /** @return Link[]|null */
    public function getContextualActions(): ?array;

    public function withContextualActions(array $contextualActions): static;

    /** @return Link[]|null */
    public function getItemActions(): ?array;

    /** @param array $itemActions Link[]|null */
    public function withItemActions(array $itemActions): static;

    public function getRedirectOperation(): ?string;

    public function withRedirectOperation(?string $redirectOperation): static;

    public function hasValidation(): ?bool;

    public function withValidation(bool $validation): static;

    public function getValidationContext(): ?array;

    public function withValidationContext(array $context): static;

    public function hasAjax(): ?bool;

    public function withAjax(?bool $ajax): static;

    public function getNormalizationContext(): ?array;

    public function withNormalizationContext(array $context): static;

    public function getDenormalizationContext(): ?array;

    public function withDenormalizationContext(array $context): static;

    public function getInput(): ?string;

    public function withInput(?string $input): static;

    public function getOutput(): ?string;

    public function withOutput(?string $output): static;

    public function setWorkflow(?string $workflow): static;

    public function getWorkflowTransition(): ?string;

    public function setWorkflowTransition(?string $workflowTransition): static;

    public function isPartial(): bool;

    public function withPartial(bool $partial): static;

    public function getSuccessMessage(): ?string;

    public function withSuccessMessage(?string $successMessage): static;
}
