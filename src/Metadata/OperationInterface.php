<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Security\RolesOwnerInterface;
use LAG\AdminBundle\Workflow\WorkflowAwareInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

interface OperationInterface extends RolesOwnerInterface, WorkflowAwareInterface
{
    public function getShortName(): ?string;

    public function getName(): string;

    /** @return array<string, mixed> */
    public function getContext(): array;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getIcon(): ?string;

    public function getTemplate(): ?string;

    public function getBaseTemplate(): ?string;

    public function getController(): ?string;

    public function getRoute(): ?string;

    /** @return array<string, mixed>|null */
    public function getRouteParameters(): ?array;

    public function getPath(): ?string;

    public function getRedirectRoute(): ?string;

    /** @return array<string, mixed>|null */
    public function getRedirectRouteParameters(): ?array;

    public function getForm(): ?string;

    /** @return array<string, mixed>|null */
    public function getFormOptions(): ?array;

    public function getFormOption(string $option): mixed;

    public function getFormTemplate(): ?string;

    public function getProcessor(): string;

    public function getProvider(): string;

    /** @return array<string> */
    public function getMethods(): array;

    /** @return string[]|null */
    public function getIdentifiers(): ?array;

    #[Ignore]
    public function getResource(): ResourceInterface;

    /** @return Link[]|null */
    public function getContextualLinks(): ?array;

    /** @return Link[]|null */
    public function getItemLinks(): ?array;

    public function getRedirectOperation(): ?string;

    public function hasValidation(): ?bool;

    /** @return array<string, mixed>|null */
    public function getValidationContext(): ?array;

    public function hasAjax(): ?bool;

    /** @return array<string, mixed>|null */
    public function getNormalizationContext(): ?array;

    /** @return array<string, mixed>|null */
    public function getDenormalizationContext(): ?array;

    public function getInput(): ?string;

    public function getOutput(): ?string;

    public function getWorkflowTransition(): ?string;

    public function canBeEmbedded(): bool;

    public function getSuccessMessage(): ?string;
}
