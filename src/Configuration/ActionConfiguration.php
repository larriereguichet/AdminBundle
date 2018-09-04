<?php

namespace LAG\AdminBundle\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Routing\RoutingLoader;
use LAG\AdminBundle\Utils\StringUtils;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfiguration extends Configuration
{
     /**
     * Related Action name.
     *
     * @var string
     */
    private $actionName;

    /**
     * @var AdminConfiguration
     */
    private $adminConfiguration;

    /**
     * @var string
     */
    private $adminName;

    /**
     * ActionConfiguration constructor.
     *
     * @param string             $actionName
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     */
    public function __construct(string $actionName, string $adminName, AdminConfiguration $adminConfiguration)
    {
        parent::__construct();

        $this->actionName = $actionName;
        $this->adminConfiguration = $adminConfiguration;
        $this->adminName = $adminName;
    }

    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'title' => $this->getDefaultTitle(),
                'class' => Action::class,
                'fields' => [],
                'permissions' => [
                    'ROLE_ADMIN',
                ],
                'export' => [],
                'order' => [],
                'route' => $this->generateRouteName(),
                'route_parameters' => [],
                'route_path' => $this->getDefaultRoutePath(),
                'route_requirements' => [],
                'route_defaults' => [
                    '_controller' => AdminAction::class,
                ],
                'icon' => null,
                'load_strategy' => LAGAdminBundle::LOAD_STRATEGY_NONE,
                'pager' => false,
                'max_per_page' => 30,
                'criteria' => [],
                'filters' => [],
                'menus' => [],
                'template' => $this->getDefaultTemplate(),
                'sortable' => false,
                'string_length' => $this->adminConfiguration->getParameter('string_length'),
                'string_length_truncate' => $this->adminConfiguration->getParameter('string_length_truncate'),
                'date_format' => $this->adminConfiguration->getParameter('date_format'),
                'use_form' => false,
            ])
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('class', 'string')
            ->setAllowedTypes('fields', 'array')
            ->setAllowedTypes('permissions', 'array')
            ->setAllowedTypes('export', 'array')
            ->setAllowedTypes('order', 'array')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedTypes('route_path', 'string')
            ->setAllowedTypes('route_defaults', 'array')
            ->setAllowedTypes('route_requirements', 'array')
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setNormalizer('fields', $this->getFieldsNormalizer())
            ->setNormalizer('order', $this->getOrderNormalizer())
            ->setNormalizer('load_strategy', $this->getLoadStrategyNormalizer())
            ->setNormalizer('criteria', $this->getCriteriaNormalizer())
            ->setNormalizer('menus', $this->getMenuNormalizer())
            ->setNormalizer('filters', $this->getFiltersNormalizer())
            ->setNormalizer('route_defaults', $this->getRouteDefaultNormalizer())
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
    }

    /**
     * Generate an admin route name using the pattern in the configuration.
     *
     * @return string
     *
     * @throws Exception
     */
    private function generateRouteName(): string
    {
        if (!array_key_exists($this->actionName, $this->adminConfiguration->getParameter('actions'))) {
            throw new Exception(
                sprintf('Invalid action name %s for admin %s (available action are: %s)',
                    $this->actionName,
                    $this->adminName,
                    implode(', ', array_keys($this->adminConfiguration->getParameter('actions'))))
            );
        }
        $routeName = RoutingLoader::generateRouteName(
            $this->adminName,
            $this->actionName,
            $this->adminConfiguration->getParameter('routing_name_pattern')
        );

        return $routeName;
    }

    /**
     * Return the field normalizer. It will transform null configuration into array to allow field type guessing
     * working.
     *
     * @return Closure
     */
    private function getFieldsNormalizer()
    {
        return function (Options $options, $fields) {
            $normalizedFields = [];

            foreach ($fields as $name => $field) {

                if ($field === null) {
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
    private function getOrderNormalizer()
    {
        return function (Options $options, $order) {
            foreach ($order as $field => $sort) {

                if (!is_string($sort) || !is_string($field) || !in_array(strtolower($sort), ['asc', 'desc'])) {
                    throw new Exception(
                        'Order value should be an array of string (["field" => $key]), got '.gettype($sort),
                        $this->actionName,
                        $this->adminName
                    );
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
    private function getLoadStrategyNormalizer()
    {
        return function (Options $options, $value) {
            if (!$value) {
                if ($this->actionName == 'create') {
                    $value = LAGAdminBundle::LOAD_STRATEGY_NONE;
                } else if ($this->actionName == 'list') {
                    $value = LAGAdminBundle::LOAD_STRATEGY_MULTIPLE;
                } else {
                    $value = LAGAdminBundle::LOAD_STRATEGY_UNIQUE;
                }
            }

            return $value;
        };
    }

    /**
     * Return the menu normalizer. It will transform false values into an empty array to allow default menu
     * configuration working.
     *
     * @return Closure
     */
    private function getMenuNormalizer()
    {
        return function (Options $options, $menus) {
            // set default to an array
            if ($menus === false) {
                $menus = [];
            }

            return $menus;
        };
    }

    /**
     * Return the criteria normalizer. It will add the id parameters for the edit and delete actions if no value is
     * provided.
     *
     * @return Closure
     */
    private function getCriteriaNormalizer()
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

    /**
     * @return string
     */
    public function getAdminName(): string
    {
        return $this->adminName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration(): AdminConfiguration
    {
        return $this->adminConfiguration;
    }

    /**
     * Return the filters normalizer.
     *
     * @return Closure
     */
    private function getFiltersNormalizer()
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

    private function getRouteDefaultNormalizer()
    {
        return function (Options $options, $value) {
            if (!is_array($value)) {
                $value = [];
            }
            $value['_admin'] = $this->adminName;
            $value['_action'] = $this->actionName;

            return $value;
        };
    }

    /**
     * Return the default title using the configured translation pattern.
     *
     * @return string
     */
    private function getDefaultTitle()
    {
        $translationPattern = $this
            ->adminConfiguration
            ->getParameter('translation_pattern');

        if (false !== $translationPattern) {
            // by default, the action title is action name using the configured translation pattern

            $actionTitle = StringUtils::getTranslationKey(
                $translationPattern,
                $this->actionName,
                $this->adminName
            );
        } else {
            // no admin was provided, we camelize the action name
            $actionTitle = Container::camelize($this->actionName);
        }

        return $actionTitle;
    }

    /**
     * Return the default route path according to the action name.
     *
     * @return string
     */
    private function getDefaultRoutePath()
    {
        $pattern = $this
            ->adminConfiguration
            ->getParameter('routing_url_pattern')
        ;
        $path = str_replace('{admin}', $this->adminName, $pattern);
        $path = str_replace('{action}', $this->actionName, $path);

        if (in_array($this->actionName, ['edit', 'delete'])) {
            $path .= '/{id}';
        }

        return $path;
    }

    private function getDefaultTemplate()
    {
        $mapping = [
            'list' => '@LAGAdmin/CRUD/list.html.twig',
            'edit' => '@LAGAdmin/CRUD/edit.html.twig',
            'create' => '@LAGAdmin/CRUD/create.html.twig',
            'delete' => '@LAGAdmin/CRUD/delete.html.twig',
        ];

        if (!$this->isActionInMapping($mapping)) {
            return null;
        }

        return $mapping[$this->actionName];
    }

    private function isActionInMapping(array $mapping)
    {
        return array_key_exists($this->actionName, $mapping);
    }
}
