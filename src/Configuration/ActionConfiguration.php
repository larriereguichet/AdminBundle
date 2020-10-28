<?php

namespace LAG\AdminBundle\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Form\Type\DeleteType;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\TranslationUtils;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfiguration extends Configuration
{
    /**
     * Related Action name.
     *
     * @var string
     */
    protected $actionName;

    /**
     * @var AdminConfiguration
     */
    protected $adminConfiguration;

    /**
     * @var string
     */
    protected $adminName;

    /**
     * ActionConfiguration constructor.
     */
    public function __construct(string $actionItemName, string $adminName, AdminConfiguration $adminConfiguration)
    {
        parent::__construct();

        $this->actionName = $actionItemName;
        $this->adminConfiguration = $adminConfiguration;
        $this->adminName = $adminName;
    }

    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'title' => null,
                'class' => Action::class,
                'fields' => [],
                'permissions' => [
                    'ROLE_ADMIN',
                ],
                'export' => [],
                'order' => [],
                'icon' => null,
                'load_strategy' => LAGAdminBundle::LOAD_STRATEGY_NONE,
                'pager' => $this->adminConfiguration->get('pager'),
                'page_parameter' => $this->adminConfiguration->get('page_parameter'),
                'max_per_page' => 30,
                'criteria' => [],
                'filters' => [],
                'template' => $this->getDefaultTemplate(),
                'sortable' => false,
                'string_length' => $this->adminConfiguration->get('string_length'),
                'string_length_truncate' => $this->adminConfiguration->get('string_length_truncate'),
                'date_format' => $this->adminConfiguration->get('date_format'),
                'use_form' => true,
                'form' => null,
                'menus' => $this->adminConfiguration->get('menus'),
            ])
            ->setAllowedTypes('title', [
                'string',
                'null',
            ])
            ->setAllowedTypes('class', 'string')
            ->setAllowedTypes('fields', 'array')
            ->setAllowedTypes('permissions', 'array')
            ->setAllowedTypes('export', 'array')
            ->setAllowedTypes('order', 'array')
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setNormalizer('fields', $this->getFieldsNormalizer())
            ->setNormalizer('order', $this->getOrderNormalizer())
            ->setNormalizer('load_strategy', $this->getLoadStrategyNormalizer())
            ->setNormalizer('criteria', $this->getCriteriaNormalizer())
            ->setNormalizer('filters', $this->getFiltersNormalizer())
            ->setNormalizer('form', $this->getFormNormalizer())
            ->setNormalizer('title', $this->getTitleNormalizer())
            ->setAllowedValues('load_strategy', [
                LAGAdminBundle::LOAD_STRATEGY_NONE,
                LAGAdminBundle::LOAD_STRATEGY_UNIQUE,
                LAGAdminBundle::LOAD_STRATEGY_MULTIPLE,
            ])
            ->setAllowedValues('pager', [
                'pagerfanta',
                false,
                null,
            ])
        ;

        $this->configureMenu($resolver);
        $this->configureRepository($resolver);
        $this->configureRouting($resolver);
    }

    /**
     * Generate an admin route name using the pattern in the configuration.
     *
     * @throws Exception
     */
    protected function generateRouteName(): string
    {
        if (!array_key_exists($this->actionName, $this->adminConfiguration->get('actions'))) {
            throw new Exception(sprintf('Invalid action name %s for admin %s (available action are: %s)', $this->actionName, $this->adminName, implode(', ', array_keys($this->adminConfiguration->get('actions')))));
        }

        return RoutingLoader::generateRouteName(
            $this->adminName,
            $this->actionName,
            $this->adminConfiguration->get('routing_name_pattern')
        );
    }

    /**
     * Return the field normalizer. It will transform null configuration into array to allow field type guessing
     * working.
     *
     * @return Closure
     */
    protected function getFieldsNormalizer()
    {
        return function (Options $options, $fields) {
            $normalizedFields = [];

            foreach ($fields as $name => $field) {
                if (null === $field) {
                    $field = [];
                }

                $normalizedFields[$name] = $field;
            }

            return $normalizedFields;
        };
    }

    /**
     * Return the order normalizer. It will check if the order value passed are valid.
     *
     * @return Closure
     */
    protected function getOrderNormalizer()
    {
        return function (Options $options, $order) {
            foreach ($order as $field => $sort) {
                if (!is_string($sort) || !is_string($field) || !in_array(strtolower($sort), ['asc', 'desc'])) {
                    throw new Exception('Order value should be an array of string (["field" => $key]), got '.gettype($sort));
                }
            }

            return $order;
        };
    }

    /**
     * Return the load strategy normalizer. It will set the default strategy according to the action name, if no value
     * is provided.
     *
     * @return Closure
     */
    protected function getLoadStrategyNormalizer()
    {
        return function (Options $options, $value) {
            if (!$value) {
                if ('create' == $this->actionName) {
                    $value = LAGAdminBundle::LOAD_STRATEGY_NONE;
                } elseif ('list' == $this->actionName) {
                    $value = LAGAdminBundle::LOAD_STRATEGY_MULTIPLE;
                } else {
                    $value = LAGAdminBundle::LOAD_STRATEGY_UNIQUE;
                }
            }

            return $value;
        };
    }

    /**
     * Return the criteria normalizer. It will add the id parameters for the edit and delete actions if no value is
     * provided.
     *
     * @return Closure
     */
    protected function getCriteriaNormalizer()
    {
        return function (Options $options, $value) {
            if (!$value) {
                $idActions = [
                    'edit',
                    'delete',
                ];

                if (in_array($this->actionName, $idActions)) {
                    $value = [
                        'id',
                    ];
                }
            }

            return $value;
        };
    }

    public function getAdminName(): string
    {
        return $this->adminName;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function getAdminConfiguration(): AdminConfiguration
    {
        return $this->adminConfiguration;
    }

    public function getLoadStrategy(): string
    {
        return $this->parameters->get('load_strategy');
    }

    /**
     * Return the filters normalizer.
     */
    protected function getFiltersNormalizer(): Closure
    {
        return function (Options $options, $data) {
            $normalizedData = [];

            foreach ($data as $name => $field) {
                if (is_string($field)) {
                    $field = [
                        'type' => $field,
                        'options' => [],
                    ];
                }
                $field['name'] = $name;

                $resolver = new OptionsResolver();
                $filterConfiguration = new FilterConfiguration();
                $filterConfiguration->configureOptions($resolver);
                $filterConfiguration->setParameters($resolver->resolve($field));

                $normalizedData[$name] = $filterConfiguration->getParameters();
            }

            return $normalizedData;
        };
    }

    protected function getFormNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (null !== $value) {
                return $value;
            }
            $mapping = [
                'create' => $this->adminConfiguration->get('form'),
                'edit' => $this->adminConfiguration->get('form'),
                'delete' => DeleteType::class,
            ];

            if (key_exists($this->actionName, $mapping)) {
                $value = $mapping[$this->actionName];
            }

            return $value;
        };
    }

    protected function getTitleNormalizer(): Closure
    {
        return function (Options $options, $value) {
            // If the translation system is not used, return the provided value as is
            if (!$this->adminConfiguration->isTranslationEnabled()) {
                if (null === $value) {
                    return StringUtils::camelize($this->actionName);
                }

                return $value;
            }

            // If a value is defined, we should return the value
            if (null !== $value) {
                return $value;
            }
            // By default, the action title is action name using the configured translation pattern
            $value = TranslationUtils::getTranslationKey(
                $this->adminConfiguration->getTranslationPattern(),
                $this->adminName,
                $this->actionName
            );

            return $value;
        };
    }

    /**
     * Return the default route path according to the action name.
     */
    protected function getDefaultRoutePath(): string
    {
        $pattern = $this
            ->adminConfiguration
            ->get('routing_url_pattern')
        ;
        $path = str_replace('{admin}', $this->adminName, $pattern);
        $path = str_replace('{action}', $this->actionName, $path);

        if (in_array($this->actionName, ['edit', 'delete'])) {
            $path .= '/{id}';
        }

        return $path;
    }

    protected function getDefaultTemplate(): ?string
    {
        $mapping = [
            'list' => $this->adminConfiguration->get('list_template'),
            'edit' => $this->adminConfiguration->get('edit_template'),
            'create' => $this->adminConfiguration->get('create_template'),
            'delete' => $this->adminConfiguration->get('delete_template'),
        ];

        if (!$this->isActionInMapping($mapping)) {
            return null;
        }

        return $mapping[$this->actionName];
    }

    protected function isActionInMapping(array $mapping): bool
    {
        return array_key_exists($this->actionName, $mapping);
    }

    protected function configureMenu(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'add_return' => in_array($this->actionName, ['create', 'edit', 'delete']),
                'menus' => [],
            ])
            ->setAllowedTypes('add_return', 'boolean')
            ->setNormalizer('menus', function (Options $options, $value) {
                if (false === $value) {
                    $value = [];
                }

                return $value;
            })
        ;
    }

    protected function configureRepository(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'repository_method' => null,
            ])
        ;
    }

    protected function configureRouting(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'controller' => $this->adminConfiguration->get('controller'),
                'route' => $this->generateRouteName(),
                'route_parameters' => [],
                'route_path' => $this->getDefaultRoutePath(),
                'route_requirements' => [],
                'route_defaults' => [],
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedTypes('route_path', 'string')
            ->setAllowedTypes('route_defaults', 'array')
            ->setAllowedTypes('route_requirements', 'array')
            ->setNormalizer('route_defaults', function (Options $options, $value) {
                if (!$value || is_array($value)) {
                    $value = [];
                }

                if (!key_exists('_controller', $value) || !$value['_controller']) {
                    $value['_controller'] = $options->offsetGet('controller');
                }
                $value['_admin'] = $this->adminName;
                $value['_action'] = $this->actionName;

                return $value;
            })
        ;
    }
}
