<?php

namespace LAG\AdminBundle\Action\Configuration;

use Closure;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Configuration\Configuration;
use LAG\AdminBundle\Menu\Configuration\MenuConfiguration;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionConfiguration extends Configuration
{
    use TranslationKeyTrait;

    /**
     * Related Action name.
     *
     * @var string
     */
    protected $actionName;

    /**
     * Related Admin (optional)
     *
     * @var AdminInterface
     */
    protected $admin = null;

    /**
     * ActionConfiguration constructor.
     *
     * @param string $actionName
     * @param AdminInterface|null $admin
     */
    public function __construct($actionName, AdminInterface $admin = null)
    {
        parent::__construct();

        $this->actionName = $actionName;
        $this->admin = $admin;
    }

    /**
     * Define allowed parameters and values for this configuration, using optionsResolver component.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $this->configureDefaultOptions($resolver);
        $this->configureNormalizers($resolver);

        $this->setAllowedTypes($resolver);
        $this->setAllowedValues($resolver);
    }

    /**
     * Configure the default options for an Action.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureDefaultOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // the default action title is a translation key using the translation pattern and the action name
                'title' => $this->getDefaultTitle(),
                // the default field mapping configuration: id=23 in request, getId() in the entity
                'fields' => [
                    'id' => []
                ],
                // by default, only administrator can access the admin
                'permissions' => [
                    'ROLE_ADMIN'
                ],
                // all export are activated
                'export' => [
                    'json',
                    'html',
                    'csv',
                    'xls'
                ],
                // no order
                'order' => [],
                // route will be generated if empty string or null or false is provided
                'route' => '',
                // route parameters will be used when using the route (for links...)
                'route_parameters' => [],
                // icon in the menu
                'icon' => '',
                // entities loading strategy
                'load_strategy' => null,
                // pager interface, only null or pagerfanta are allowed
                'pager' => 'pagerfanta',
                // default criteria used to load entities
                'criteria' => [],
                'filters' => [],
                'menus' => [],
                'batch' => false,
            ]);
    }

    /**
     * Define the allowed values.
     *
     * @param OptionsResolver $resolver
     */
    protected function setAllowedValues(OptionsResolver $resolver)
    {
        $resolver
            ->setAllowedValues('load_strategy', [
                AdminInterface::LOAD_STRATEGY_NONE,
                AdminInterface::LOAD_STRATEGY_UNIQUE,
                AdminInterface::LOAD_STRATEGY_MULTIPLE,
                null
            ])
            ->setAllowedValues('pager', [
                'pagerfanta',
                false
            ]);
    }

    /**
     * Define the allowed types.
     *
     * @param OptionsResolver $resolver
     */
    protected function setAllowedTypes(OptionsResolver $resolver)
    {
        $resolver
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('fields', 'array')
            ->setAllowedTypes('order', 'array')
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedTypes('icon', 'string')
            ->setAllowedTypes('criteria', 'array')
            ->setAllowedTypes('filters', 'array')
            ->setAllowedTypes('menus', [
                'array',
                'boolean',
            ])
        ;
    }

    /**
     * Configure the normalizers.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureNormalizers(OptionsResolver $resolver)
    {
        $resolver
            ->setNormalizer('fields', $this->getFieldsNormalizer())
            ->setNormalizer('order', $this->getOrderNormalizer())
            ->setNormalizer('route', $this->getRouteNormalizer())
            ->setNormalizer('load_strategy', $this->getLoadStrategyNormalizer())
            ->setNormalizer('criteria', $this->getCriteriaNormalizer())
            ->setNormalizer('menus', $this->getMenuNormalizer())
            ->setNormalizer('batch', $this->getBatchNormalizer())
        ;
    }

    /**
     * Return the field normalizer. It will transform null configuration into array to allow field type guessing
     * working.
     *
     * @return Closure
     */
    protected function getFieldsNormalizer()
    {
        return function(Options $options, $fields) {
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
    protected function getOrderNormalizer()
    {
        return function(Options $options, $order) {
            foreach ($order as $field => $sort) {

                if (!is_string($sort) || !is_string($field) || !in_array(strtolower($sort), ['asc', 'desc'])) {
                    throw new ConfigurationException(
                        'Order value should be an array of string (["field" => $key]), got '.gettype($sort),
                        $this->actionName,
                        $this->admin
                    );
                }
            }

            return $order;
        };
    }

    /**
     * Return the route normalizer. If an empty value or null or false, it will generate the route using the Admin.
     *
     * @return Closure
     */
    protected function getRouteNormalizer()
    {
        return function(Options $options, $value) {
            if (!$value) {
                // if no route was provided, it should be linked to an Admin
                if (!$this->admin) {
                    throw new InvalidOptionsException('No route was provided for action : '.$this->actionName);
                }

                // generate default route from admin
                return $this
                    ->admin
                    ->generateRouteName($this->actionName);
            }

            return $value;
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
        return function(Options $options, $value) {
            if (!$value) {
                if ($this->actionName == 'create') {
                    $value = AdminInterface::LOAD_STRATEGY_NONE;
                } else if ($this->actionName == 'list') {
                    $value = AdminInterface::LOAD_STRATEGY_MULTIPLE;
                } else {
                    $value = AdminInterface::LOAD_STRATEGY_UNIQUE;
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
    protected function getMenuNormalizer()
    {
        return function(Options $options, $menus) {
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
    protected function getCriteriaNormalizer()
    {
        return function(Options $options, $value) {
            if (!$value) {
                $idActions = [
                    'edit',
                    'delete'
                ];

                if (in_array($this->actionName, $idActions)) {
                    $value = [
                        'id'
                    ];
                }
            }

            return $value;
        };
    }

    /**
     * Return the batch normalizer. If null value is provided, it will add the delete batch action if the delete
     * actions is allowed .
     *
     * @return Closure
     */
    protected function getBatchNormalizer()
    {
        return function(Options $options, $batch) {
            // if batch is not activated, no more checks should be done
            if ($batch === false) {
                return $batch;
            }
            // for list actions, we add a default configuration
            if ($batch === null) {
                // delete action should be allowed in order to be place in batch actions
                $allowedActions = array_keys($this
                    ->admin
                    ->getConfiguration()
                    ->getParameter('actions'));

                if ($this->actionName == 'list' && in_array('delete', $allowedActions)) {
                    $pattern = $this
                        ->admin
                        ->getConfiguration()
                        ->getParameter('translation_pattern');

                    $batch = [
                        'items' => [
                            'delete' => [
                                'admin' => $this->admin->getName(),
                                'action' => 'delete',
                                'text' => $this->getTranslationKey($pattern, 'delete', $this->admin->getName())
                            ]
                        ]
                    ];
                } else {
                    return $batch;
                }
            }
            $resolver = new OptionsResolver();
            $configuration = new MenuConfiguration();
            $configuration->configureOptions($resolver);
            $batch = $resolver->resolve($batch);

            return $batch;
        };
    }

    /**
     * Return the default title using the configured translation pattern.
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        if ($this->admin) {
            // by default, the action title is action name using the configured translation pattern
            $translationPattern = $this
                ->admin
                ->getConfiguration()
                ->getParameter('translation_pattern');

            $actionTitle = $this->getTranslationKey(
                $translationPattern,
                $this->actionName,
                $this->admin->getName()
            );
        } else {
            // no admin was provided, we camelize the action name
            $actionTitle = Container::camelize($this->actionName);
        }

        return $actionTitle;
    }
}
