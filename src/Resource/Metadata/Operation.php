<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use LAG\AdminBundle\Validation\Constraint\AtLeastOneIdentifier;
use Symfony\Component\Validator\Constraints as Assert;

#[AtLeastOneIdentifier]
abstract class Operation implements OperationInterface
{
    private ?Resource $resource = null;

    public function __construct(
        #[Assert\NotBlank(message: 'The operation name should not be empty')]
        private ?string $name = null,

        #[Assert\Length(max: 255, maxMessage: 'The operation title should be shorter than 255 characters')]
        private ?string $title = null,

        private ?string $description = null,

        #[Assert\Length(max: 255, maxMessage: 'The operation icon should be shorter than 255 characters')]
        private ?string $icon = null,

        #[Assert\NotBlank(message: 'The operation template should not be empty')]
        private ?string $template = null,

        #[Assert\NotBlank(message: 'The operation base template should not be empty. Use "@LAGAdmin/partial.html.twig" instead if you want an empty base')]
        private ?string $baseTemplate = null,

        private ?array $permissions = [],

        #[Assert\NotBlank(message: 'The operation controller should not be empty')]
        private ?string $controller = null,

        #[Assert\NotBlank(message: 'The operation has an empty route')]
        private ?string $route = null,

        #[Assert\NotNull]
        private ?array $routeParameters = null,

        private array $methods = [],

        #[Assert\NotBlank]
        private ?string $path = null,

        #[Assert\NotBlank(allowNull: true, message: 'The redirect route should not be empty, use null instead')]
        private ?string $redirectRoute = null,

        #[Assert\NotNull]
        private ?array $redirectRouteParameters = null,

        private ?string $form = null,

        private array $formOptions = [],

        #[Assert\NotBlank(allowNull: true, message: 'The form template should not be empty. Use null instead')]
        private ?string $formTemplate = null,

        private string $processor = ORMProcessor::class,

        private string $provider = ORMProvider::class,

        #[Assert\NotNull]
        private ?array $identifiers = null,

        #[Assert\NotNull(message: 'The contextual links should not be null. Use an empty array instead')]
        #[Assert\All(constraints: [new Assert\Type(type: Link::class)])]
        #[Assert\Valid]
        private ?array $contextualActions = null,

        private ?array $itemActions = null,

        #[Assert\NotBlank(allowNull: true, message: 'The redirect application should not be empty, use null instead')]
        private ?string $redirectApplication = null,

        #[Assert\NotBlank(allowNull: true, message: 'The redirect resource should not be empty, use null instead')]
        private ?string $redirectResource = null,

        #[Assert\NotBlank(allowNull: true, message: 'The redirect operation should not be empty, use null instead')]
        private ?string $redirectOperation = null,

        private ?bool $validation = true,

        private ?array $validationContext = null,

        private ?bool $ajax = true,

        #[Assert\NotNull(message: 'The normalization context should not be null. Use an empty array instead')]
        private ?array $normalizationContext = null,

        #[Assert\NotNull(message: 'The denormalization context should not be null. Use an empty array instead')]
        private ?array $denormalizationContext = null,

        private ?string $input = null,

        private ?string $output = null,

        private ?string $workflow = null,

        private ?string $workflowTransition = null,

        private bool $partial = false,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function withName(?string $name): self
    {
        $self = clone $this;
        $self->name = $name;

        return $self;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function withTitle(?string $title): self
    {
        $self = clone $this;
        $self->title = $title;

        return $self;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function withDescription(?string $description): self
    {
        $self = clone $this;
        $self->description = $description;

        return $self;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function withIcon(?string $icon): self
    {
        $self = clone $this;
        $self->icon = $icon;

        return $self;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function withTemplate(?string $template): self
    {
        $self = clone $this;
        $self->template = $template;

        return $self;
    }

    public function getBaseTemplate(): ?string
    {
        return $this->baseTemplate;
    }

    public function withBaseTemplate(string $baseTemplate): self
    {
        $self = clone $this;
        $self->baseTemplate = $baseTemplate;

        return $self;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function withPermissions(?array $permissions): self
    {
        $self = clone $this;
        $self->permissions = $permissions;

        return $self;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function withController(?string $controller): self
    {
        $self = clone $this;
        $self->controller = $controller;

        return $self;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function withRoute(?string $route): self
    {
        $self = clone $this;
        $self->route = $route;

        return $self;
    }

    public function getRouteParameters(): ?array
    {
        return $this->routeParameters;
    }

    public function withRouteParameters(?array $routeParameters): self
    {
        $self = clone $this;
        $self->routeParameters = $routeParameters;

        return $self;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function withPath(?string $path): self
    {
        $self = clone $this;
        $self->path = $path;

        return $self;
    }

    public function getRedirectRoute(): ?string
    {
        return $this->redirectRoute;
    }

    public function withRedirectRoute(?string $targetRoute): self
    {
        $self = clone $this;
        $self->redirectRoute = $targetRoute;

        return $self;
    }

    public function getRedirectRouteParameters(): ?array
    {
        return $this->redirectRouteParameters;
    }

    public function withRedirectRouteParameters(?array $targetRouteParameters): self
    {
        $self = clone $this;
        $self->redirectRouteParameters = $targetRouteParameters;

        return $self;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function withForm(?string $form): self
    {
        $self = clone $this;
        $self->form = $form;

        return $self;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function withFormOptions(array $formOptions): self
    {
        $self = clone $this;
        $self->formOptions = $formOptions;

        return $self;
    }

    public function getFormTemplate(): ?string
    {
        return $this->formTemplate;
    }

    public function withFormTemplate(?string $formTemplate): self
    {
        $self = clone $this;
        $self->formTemplate = $formTemplate;

        return $self;
    }

    public function getProcessor(): string
    {
        return $this->processor;
    }

    public function withProcessor(string $processor): self
    {
        $self = clone $this;
        $self->processor = $processor;

        return $self;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function withProvider(string $provider): self
    {
        $self = clone $this;
        $self->provider = $provider;

        return $self;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function withMethods(array $methods): self
    {
        $self = clone $this;
        $self->methods = $methods;

        return $self;
    }

    public function getIdentifiers(): ?array
    {
        return $this->identifiers;
    }

    public function withIdentifiers(array $identifiers): self
    {
        $self = clone $this;
        $self->identifiers = $identifiers;

        return $self;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function withResource(?Resource $resource): self
    {
        $self = clone $this;
        $self->resource = $resource;

        return $self;
    }

    public function getContextualActions(): ?array
    {
        return $this->contextualActions;
    }

    public function withContextualActions(array $contextualActions): self
    {
        $self = clone $this;
        $self->contextualActions = $contextualActions;

        return $self;
    }

    public function getItemActions(): ?array
    {
        return $this->itemActions;
    }

    public function withItemActions(array $itemActions): self
    {
        $self = clone $this;
        $self->itemActions = $itemActions;

        return $self;
    }

    public function getRedirectResource(): ?string
    {
        return $this->redirectResource;
    }

    public function withRedirectResource(?string $redirectResource): self
    {
        $self = clone $this;
        $self->redirectResource = $redirectResource;

        return $self;
    }

    public function getRedirectApplication(): ?string
    {
        return $this->redirectApplication;
    }

    public function withRedirectApplication(?string $redirectApplication): self
    {
        $self = clone $this;
        $self->redirectApplication = $redirectApplication;

        return $self;
    }

    public function getRedirectOperation(): ?string
    {
        return $this->redirectOperation;
    }

    public function withRedirectOperation(?string $redirectOperation): self
    {
        $self = clone $this;
        $self->redirectOperation = $redirectOperation;

        return $self;
    }

    public function useValidation(): ?bool
    {
        return $this->validation;
    }

    public function withValidation(bool $validation): self
    {
        $self = clone $this;
        $self->validation = $validation;

        return $self;
    }

    public function getValidationContext(): ?array
    {
        return $this->validationContext;
    }

    public function withValidationContext(array $context): self
    {
        $self = clone $this;
        $self->validationContext = $context;

        return $self;
    }

    public function useAjax(): ?bool
    {
        return $this->ajax;
    }

    public function withAjax(?bool $ajax): self
    {
        $self = clone $this;
        $self->ajax = $ajax;

        return $self;
    }

    public function getNormalizationContext(): ?array
    {
        return $this->normalizationContext;
    }

    public function withNormalizationContext(array $context): self
    {
        $self = clone $this;
        $self->normalizationContext = $context;

        return $self;
    }

    public function getDenormalizationContext(): ?array
    {
        return $this->denormalizationContext;
    }

    public function withDenormalizationContext(array $context): self
    {
        $self = clone $this;
        $self->denormalizationContext = $context;

        return $self;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function withInput(?string $input): self
    {
        $self = clone $this;
        $self->input = $input;

        return $self;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function withOutput(?string $output): self
    {
        $self = clone $this;
        $self->output = $output;

        return $self;
    }

    public function getWorkflow(): ?string
    {
        return $this->workflow;
    }

    public function setWorkflow(?string $workflow): self
    {
        $self = clone $this;
        $self->workflow = $workflow;

        return $self;
    }

    public function getWorkflowTransition(): ?string
    {
        return $this->workflowTransition;
    }

    public function setWorkflowTransition(?string $workflowTransition): self
    {
        $self = clone $this;
        $self->workflowTransition = $workflowTransition;

        return $self;
    }

    public function isPartial(): bool
    {
        return $this->partial;
    }

    public function withPartial(bool $partial): self
    {
        $self = clone $this;
        $self->partial = $partial;

        return $self;
    }
}
