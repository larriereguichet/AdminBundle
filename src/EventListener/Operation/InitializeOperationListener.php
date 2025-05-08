<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Form\Type\Data\HiddenDataType;
use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Resource\Factory\ApplicationFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class InitializeOperationListener
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
        private ApplicationFactoryInterface $applicationFactory,
    ) {
    }

    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();
        $application = $this->applicationFactory->create($resource->getApplication());

        if ($operation->getTitle() === null) {
            $inflector = new EnglishInflector();

            if ($operation instanceof CollectionOperationInterface) {
                $title = u($inflector->pluralize($resource->getName())[0]);
            } else {
                $title = u($operation->getShortName())
                    ->append(' ')
                    ->append($resource->getName())
                ;
            }
            $operation = $operation->withTitle($title->replace('_', ' ')->title()->trim()->toString());
        }

        if ($operation->getForm() === null) {
            if ($operation instanceof Create || $operation instanceof Update) {
                if ($resource->getForm()) {
                    $operation = $operation
                        ->withForm($resource->getForm())
                        ->withFormOptions($resource->getFormOptions())
                    ;
                } else {
                    $operation = $operation
                        ->withForm(ResourceDataType::class)
                        ->withFormOptions([
                            'exclude' => $resource->getIdentifiers(),
                            'data_class' => $resource->getResourceClass(),
                            'application' => $resource->getApplication(),
                            'resource' => $resource->getName(),
                            'operation' => $operation->getName(),
                        ])
                    ;
                }
            }
        }

        if ($operation->getForm() === HiddenDataType::class && $operation->getFormOptions() === null) {
            $operation = $operation->withFormOptions([
                'application' => $resource->getApplication(),
                'resource' => $resource->getName(),
                'operation' => $operation->getName(),
                'translation_domain' => $resource->getTranslationDomain(),
            ]);
        }

        if ($operation->getFormOptions() === null) {
            $operation = $operation->withFormOptions([]);
        }

        if (empty($operation->getFormOptions()['translation_domain']) && $resource->getTranslationDomain() !== null) {
            $operation = $operation->withFormOptions(['translation_domain' => $resource->getTranslationDomain()]);
        }

        if ($operation->getBaseTemplate() === null) {
            $baseTemplate = '@LAGAdmin/base.html.twig';

            if ($application?->getBaseTemplate()) {
                $baseTemplate = $application->getBaseTemplate();
            }
            $operation = $operation->withBaseTemplate($operation->isPartial() ? '@LAGAdmin/partial.html.twig' : $baseTemplate);
        }

        if ($operation->getFormTemplate() === null && $resource->getFormTemplate() !== null) {
            $operation = $operation->withFormTemplate($resource->getFormTemplate());
        }

        if (!$operation->getRoute()) {
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $operation = $operation->withRoute($route);
        }

        if ($operation instanceof Delete && $operation->getForm() === DeleteType::class) {
            $operation = $operation->withFormOptions(array_merge($operation->getFormOptions(), ['resource' => $resource]));
        }

        if ($operation->getContextualActions() === null) {
            $operation = $operation->withContextualActions([]);
        }

        if (!$operation->getItemActions()) {
            $operation = $this->withDefaultItemActions($resource, $operation);
        }

        if (!$operation->getRedirectRouteParameters()) {
            $operation = $operation->withRedirectRouteParameters([]);
        }

        if ($resource->isValidationEnabled() !== null) {
            if ($operation->hasValidation() === null) {
                $operation = $operation->withValidation($resource->isValidationEnabled());
            }

            if ($resource->getValidationContext() !== null) {
                if ($operation->getValidationContext() === null) {
                    $operation = $operation->withValidationContext($resource->getValidationContext());
                }
            }
        }

        if ($resource->hasAjax() !== null) {
            if ($operation->hasAjax() === null) {
                $operation = $operation->withAjax($resource->hasAjax());
            }

            if ($operation->hasAjax()) {
                if ($resource->getNormalizationContext() !== null && $operation->getNormalizationContext() === null) {
                    $operation = $operation->withNormalizationContext($resource->getNormalizationContext());
                }

                if ($resource->getDenormalizationContext() !== null && $operation->getDenormalizationContext() === null) {
                    $operation = $operation->withDenormalizationContext($resource->getDenormalizationContext());
                }
            }
        }

        if ($operation->getPermissions() === null) {
            $operation = $operation->withPermissions($resource->getPermissions());
        }

        if ($operation->getPermissions() === null) {
            $operation = $operation->withPermissions([]);
        }

        if ($operation->getInput() === null & $resource->getInput() !== null) {
            $operation = $operation->withInput($resource->getInput());
        }

        if ($operation->getOutput() === null & $resource->getOutput() !== null) {
            $operation = $operation->withOutput($resource->getOutput());
        }

        if ($operation->getIdentifiers() === null) {
            if ($operation instanceof Create) {
                $operation = $operation->withIdentifiers([]);
            } else {
                $operation = $operation->withIdentifiers($resource->getIdentifiers());
            }
        }

        $event->setOperation($operation);
    }

    private function withDefaultItemActions(Resource $resource, OperationInterface $operation): OperationInterface
    {
        $actions = [];

        if ($operation instanceof CollectionOperationInterface) {
            if ($resource->hasOperation('update')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'update',
                    label: 'lag_admin.ui.update',
                );
            }

            if ($resource->hasOperation('delete')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'delete',
                    label: 'lag_admin.ui.delete',
                );
            }
        } else {
            if ($resource->hasOperation('index')) {
                $actions[] = new Link(
                    operation: $resource->getApplication().'.'.$resource->getName().'index',
                    label: 'lag_admin.ui.cancel',
                );
            }
        }

        return $operation->withItemActions($actions);
    }
}
