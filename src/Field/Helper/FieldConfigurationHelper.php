<?php

namespace LAG\AdminBundle\Field\Helper;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Helper\MetadataTrait;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Routing\RoutingLoader;

class FieldConfigurationHelper
{
    use MetadataTrait;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    public function __construct(
        EntityManagerInterface $entityManager,
        ApplicationConfiguration $applicationConfiguration
    ) {
        $this->entityManager = $entityManager;
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function addDefaultFields(array &$configuration, $entityClass, $adminName)
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
            if (null === $action) {
                $action = [];
            }
            $metadata = $this->findMetadata($entityClass);

            if (null === $metadata) {
                continue;
            }

            // If fields are already defined, nothing to do
            if (key_exists('fields', $action) && is_array($action['fields']) && count($action['fields'])) {
                $fields = $action['fields'];
            } else {
                $fields = [];

                // Get fields names from the metadata if no configuration is defined
                foreach ($metadata->getFieldNames() as $fieldName) {
                    $fields[$fieldName] = null;
                }
            }
            $actionField = $this->getActionField($metadata->getFieldNames());

            foreach ($fields as $fieldName => $fieldConfiguration) {
                $fieldType = $metadata->getTypeOfField($fieldName);

                if (
                    'list' === $actionName &&
                    $fieldName === $actionField &&
                    key_exists('edit', $configuration['actions'])
                ) {
                    $fieldConfiguration = [
                        'type' => 'action',
                        'options' => [
                            'admin' => $adminName,
                            'action' => 'edit',
                            'parameters' => [
                                'id' => null,
                            ],
                        ]
                    ];

                } else if (
                    '_delete' === $fieldName &&
                    !$metadata->hasField('_delete') &&
                    null === $fieldConfiguration &&
                    key_exists('delete', $configuration['actions'])
                ) {
                    // If a "delete" field is declared, and if it is not configured in the metadata, and if no
                    // configuration is declared for this field, and if the "delete" action is allowed, we add a default
                    // "button" configuration
                    $fieldConfiguration = [
                        'type' => 'link',
                        'options' => [
                            'admin' => $adminName,
                            'action' => 'delete',
                            'parameters' => [
                                'id' => null,
                            ],
                            'text' => 'Delete',
                            'class' => 'btn btn-sm btn-danger',
                            'icon' => 'remove',
                        ],
                    ];

                } else if (key_exists($fieldType, $mapping)) {
                    $fieldConfiguration = $mapping[$metadata->getTypeOfField($fieldName)];
                }
                $configuration['actions'][$actionName]['fields'][$fieldName] = $fieldConfiguration;
            }
        }
    }

    public function addDefaultStrategy(array &$configuration)
    {
        $mapping = [
            'list' => LAGAdminBundle::LOAD_STRATEGY_MULTIPLE,
            'create' => LAGAdminBundle::LOAD_STRATEGY_NONE,
            'delete' => LAGAdminBundle::LOAD_STRATEGY_UNIQUE,
            'edit' => LAGAdminBundle::LOAD_STRATEGY_UNIQUE,
        ];

        foreach ($configuration['actions'] as $name => $action) {
            if (null === $action) {
                continue;
            }

            if (key_exists('load_strategy', $action)) {
                continue;
            }

            if (!key_exists($name, $mapping)) {
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
            if (key_exists($name, $mapping) && !key_exists('route_requirements', $actionConfiguration)) {
                $configuration['actions'][$name]['route_requirements'] = [
                    'id' => '\d+',
                ];
            }
        }
    }

    public function addDefaultFormUse(array &$configuration)
    {
        $mapping = [
            'edit',
            'create',
            'delete',
        ];

        foreach ($configuration['actions'] as $name => $action) {
            if (!in_array($name, $mapping) && !isset($action['use_form'])) {
                continue;
            }
            $configuration['actions'][$name]['use_form'] = true;
        }
    }

    public function provideActionsFieldConfiguration(array &$configuration, string $adminName)
    {
        foreach ($configuration['actions'] as $actionName => $actionConfiguration) {

            if (!key_exists('fields', $actionConfiguration)) {
                continue;
            }

            if (!key_exists('_actions', $actionConfiguration['fields'])) {
                continue;
            }

            if (null !== $actionConfiguration['fields']['_actions']) {
                continue;
            }

            if (!key_exists('edit', $configuration['actions']) && !key_exists('delete', $configuration['actions'])) {
                continue;
            }
            $configuration['actions'][$actionName]['fields']['_actions'] = [
                'type' => 'action_collection',
                'options' => [],
            ];
            $pattern = $this->applicationConfiguration->get('routing_name_pattern');

            if (key_exists('edit', $configuration['actions'])) {
                $configuration['actions'][$actionName]['fields']['_actions']['options']['fields']['edit'] = [
                    'type' => 'action',
                    'options' => [
                        'title' => 'test',
                        'route' => RoutingLoader::generateRouteName(
                            $adminName,
                            'edit',
                            $pattern
                        ),
                        'parameters' => [
                            'id' => null,
                        ],
                        'icon' => 'pencil',
                    ],
                ];
            }

            if (key_exists('delete', $configuration['actions'])) {
                $configuration['actions'][$actionName]['fields']['_actions']['options']['fields']['delete'] = [
                    'type' => 'action',
                    'options' => [
                        'title' => 'test',
                        'route' => RoutingLoader::generateRouteName(
                            $adminName,
                            'delete',
                            $pattern
                        ),
                        'parameters' => [
                            'id' => null,
                        ],
                        'icon' => 'remove',
                    ],
                ];
            }
        }
    }

    /**
     * Return the default action field if found.
     *
     * @param array $fields
     *
     * @return string|null
     */
    private function getActionField(array $fields): ?string
    {
        $mapping = [
            'title',
            'name',
            'id',
        ];

        foreach ($mapping as $name) {
            if (in_array($name, $fields)) {
                return $name;
            }
        }

        return null;
    }
}
