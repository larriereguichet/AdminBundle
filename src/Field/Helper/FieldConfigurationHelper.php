<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @deprecated
 */
class FieldConfigurationHelper
{
    private ApplicationConfiguration $appConfig;
    private MetadataHelperInterface $metadataHelper;
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        ApplicationConfiguration $appConfig,
        MetadataHelperInterface $metadataHelper
    ) {
        $this->translator = $translator;
        $this->metadataHelper = $metadataHelper;
        $this->appConfig = $appConfig;
    }

    public function addDefaultFields(array &$configuration, $entityClass, $adminName): void
    {
        $mapping = [
            'string' => [
                'type' => 'string',
                'options' => [
                    'length' => 100,
                ],
            ],
            'boolean' => [
                'type' => 'boolean',
                'options' => [],
            ],
            'datetime' => [
                'type' => 'date',
                'options' => [],
            ],
        ];

        foreach ($configuration['actions'] as $actionName => $action) {
            if (null === $action || false === $action) {
                $action = [];
            }
            $metadata = $this->metadataHelper->findMetadata($entityClass);

            if (null === $metadata) {
                continue;
            }

            // If fields are already defined, nothing to do
            if (\array_key_exists('fields', $action) && \is_array($action['fields']) && \count($action['fields'])) {
                $fields = $action['fields'];
            } else {
                $fields = [];

                // Get fields names from the metadata if no configuration is defined
                foreach ($metadata->getFieldNames() as $fieldName) {
                    $fields[$fieldName] = null;
                }
                $fields['_delete'] = null;
            }

            foreach ($fields as $fieldName => $fieldConfiguration) {
                if (\is_array($fieldConfiguration) && \array_key_exists('type', $fieldConfiguration)) {
                    continue;
                }
                $fieldType = $metadata->getTypeOfField($fieldName);

                if (
                    '_delete' === $fieldName &&
                    !$metadata->hasField('_delete') &&
                    null === $fieldConfiguration &&
                    \array_key_exists('delete', $configuration['actions'])
                ) {
                    $text = 'Delete';

                    if ($this->appConfig->get('translation')) {
                        $text = $this->translator->trans('lag.admin.delete', [], $this->appConfig->getTranslationCatalog());
                    }
                    // If a "delete" field is declared, and if it is not configured in the metadata, and if no
                    // configuration is declared for this field, and if the "delete" action is allowed, we add a default
                    // "button" configuration
                    $fieldConfiguration = [
                        'type' => 'link',
                        'options' => [
                            'admin' => $adminName,
                            'action' => 'delete',
                            'route_parameters' => [
                                'id' => null,
                            ],
                            'text' => $text,
                            'icon' => 'times',
                        ],
                    ];
                } elseif (\array_key_exists($fieldType, $mapping)) {
                    $fieldConfiguration = $mapping[$metadata->getTypeOfField($fieldName)];
                }
                $configuration['actions'][$actionName]['fields'][$fieldName] = $fieldConfiguration;
            }
        }
    }

    public function addDefaultStrategy(array &$configuration)
    {
        $mapping = [
            'list' => AdminInterface::LOAD_STRATEGY_MULTIPLE,
            'create' => AdminInterface::LOAD_STRATEGY_NONE,
            'delete' => AdminInterface::LOAD_STRATEGY_UNIQUE,
            'edit' => AdminInterface::LOAD_STRATEGY_UNIQUE,
        ];

        foreach ($configuration['actions'] as $name => $action) {
            if (null === $action) {
                continue;
            }

            if (\array_key_exists('load_strategy', $action)) {
                continue;
            }

            if (!\array_key_exists($name, $mapping)) {
                continue;
            }
            $configuration['actions'][$name]['load_strategy'] = $mapping[$name];
        }
    }

    public function addDefaultRouteParameters(array &$configuration)
    {
        $mapping = [
            'edit' => [
                'id' => '\d+',
            ],
            'delete' => [
                'id' => '\d+',
            ],
        ];

        foreach ($configuration['actions'] as $name => $actionConfiguration) {
            if (null === $actionConfiguration) {
                $actionConfiguration = [];
            }

            if (\array_key_exists($name, $mapping) && !\array_key_exists('route_parameters', $actionConfiguration)) {
                $configuration['actions'][$name]['route_parameters'] = [
                    'id' => '\d+',
                ];
            }
        }
    }

    public function provideActionsFieldConfiguration(array &$configuration, string $adminName): void
    {
        foreach ($configuration['actions'] as $actionName => $actionConfiguration) {
            if (null === $actionConfiguration) {
                $actionConfiguration = [];
            }

            if (!\array_key_exists('fields', $actionConfiguration)) {
                continue;
            }

            if (!\array_key_exists('_actions', $actionConfiguration['fields'])) {
                continue;
            }

            if (null !== $actionConfiguration['fields']['_actions']) {
                continue;
            }

            if (!\array_key_exists('edit', $configuration['actions']) && !\array_key_exists('delete', $configuration['actions'])) {
                continue;
            }
            $configuration['actions'][$actionName]['fields']['_actions'] = [
                'type' => 'action_collection',
                'options' => [],
            ];

            if (\array_key_exists('edit', $configuration['actions'])) {
                $configuration['actions'][$actionName]['fields']['_actions']['options']['actions']['edit'] = [
                        'title' => 'test',
                        'route' => $this->appConfig->getRouteName($adminName, 'edit'),
                        'parameters' => [
                            'id' => null,
                        ],
                        'icon' => 'pencil',
                ];
            }

            if (\array_key_exists('delete', $configuration['actions'])) {
                $configuration['actions'][$actionName]['fields']['_actions']['options']['actions']['delete'] = [
                        'title' => 'test',
                        'route' => $this->appConfig->getRouteName($adminName, 'delete'),
                        'parameters' => [
                            'id' => null,
                        ],
                        'icon' => 'remove',
                ];
            }
        }
    }
}
