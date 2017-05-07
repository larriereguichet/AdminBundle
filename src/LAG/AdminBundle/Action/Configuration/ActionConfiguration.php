<?php

namespace LAG\AdminBundle\Action\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Form\Type\DeleteType;
use LAG\AdminBundle\Form\Type\ListType;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Menu\Configuration\MenuConfiguration;
use Symfony\Component\DependencyInjection\Container;
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
    private $actionName;
    
    /**
     * Related Admin (optional)
     *
     * @var AdminInterface
     */
    private $admin = null;
    
    /**
     * ActionConfiguration constructor.
     *
     * @param string              $actionName
     * @param AdminInterface|null $admin
     */
    public function __construct($actionName, AdminInterface $admin)
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
    private function configureDefaultOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // the default action title is a translation key using the translation pattern and the action name
                'title' => $this->getDefaultTitle(),
                'service' => $this->getDefaultServiceId(),
                'fields' => [
                    'id' => [],
                ],
                // by default, only administrator can access the admin
                'permissions' => [
                    'ROLE_ADMIN',
                ],
                // all export are activated
                'export' => [
                    'json',
                    'html',
                    'csv',
                    'xls',
                ],
                // no order
                'order' => [],
                // route will be generated if empty string or null or false is provided
                'route' => '',
                // route parameters will be used when using the route (for links...)
                'route_parameters' => [],
                'route_path' => $this->getDefaultRoutePath(),
                'route_defaults' => $this->getDefaultRouteDefaults(),
                'route_requirements' => $this->getDefaultRouteRequirements(),
                // icon in the menu
                'icon' => '',
                // entities loading strategy
                'load_strategy' => null,
                // pager interface, only null or pagerfanta are allowed
                'pager' => 'pagerfanta',
                'max_per_page' => $this->admin->getConfiguration()->getParameter('max_per_page'),
                // default criteria used to load entities
                'criteria' => [],
                // filters, should be an array of string (field name => filter options)
                'filters' => [],
                'menus' => [],
                'batch' => false,
                // form configuration
                'form_handler' => $this->getDefaultFormHandler(),
                'form' => $this->getDefaultForm(),
                'form_options' => $this->getDefaultFormOptions(),
                // twig template
                'template' => $this->getDefaultTemplate(),
                'sortable' => $this->getDefaultSortable(),
            ]);
    }
    
    /**
     * Define the allowed values.
     *
     * @param OptionsResolver $resolver
     */
    private function setAllowedValues(OptionsResolver $resolver)
    {
        $resolver
            ->setAllowedValues('load_strategy', [
                AdminInterface::LOAD_STRATEGY_NONE,
                AdminInterface::LOAD_STRATEGY_UNIQUE,
                AdminInterface::LOAD_STRATEGY_MULTIPLE,
                null,
            ])
            ->setAllowedValues('pager', [
                'pagerfanta',
                false,
            ])
        ;
    }
    
    /**
     * Define the allowed types.
     *
     * @param OptionsResolver $resolver
     */
    private function setAllowedTypes(OptionsResolver $resolver)
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
            ->setAllowedTypes('route_path', 'string')
            ->setAllowedTypes('route_defaults', 'array')
            ->setAllowedTypes('route_requirements', 'array')
            ->setAllowedTypes('form', 'string')
            ->setAllowedTypes('form_options', 'array')
        ;
    }
    
    /**
     * Configure the normalizers.
     *
     * @param OptionsResolver $resolver
     */
    private function configureNormalizers(OptionsResolver $resolver)
    {
        $resolver
            ->setNormalizer('fields', $this->getFieldsNormalizer())
            ->setNormalizer('order', $this->getOrderNormalizer())
            ->setNormalizer('route', $this->getRouteNormalizer())
            ->setNormalizer('load_strategy', $this->getLoadStrategyNormalizer())
            ->setNormalizer('criteria', $this->getCriteriaNormalizer())
            ->setNormalizer('menus', $this->getMenuNormalizer())
            ->setNormalizer('batch', $this->getBatchNormalizer())
            ->setNormalizer('filters', $this->getFiltersNormalizer())
        ;
    }
    
    /**
     * Return the field normalizer. It will transform null configuration into array to allow field type guessing
     * working.
     *
     * @return Closure
     */
    private function getFieldsNormalizer()
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
    private function getOrderNormalizer()
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
    private function getRouteNormalizer()
    {
        return function(Options $options, $value) {
            if (!$value) {
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
    private function getLoadStrategyNormalizer()
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
    private function getMenuNormalizer()
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
    private function getCriteriaNormalizer()
    {
        return function(Options $options, $value) {
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
     * Return the batch normalizer. If null value is provided, it will add the delete batch action if the delete
     * actions is allowed .
     *
     * @return Closure
     */
    private function getBatchNormalizer()
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
                        ->getParameter('translation_pattern')
                    ;
                    
                    $batch = [
                        'items' => [
                            'delete' => [
                                'admin' => $this->admin->getName(),
                                'action' => 'delete',
                                'text' => $this->getTranslationKey($pattern, 'delete', $this->admin->getName()),
                            ],
                        ],
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
     * Return the filters normalizer.
     *
     * @return Closure
     */
    private function getFiltersNormalizer()
    {
        return function(Options $options, $filters) {
            if (!is_array($filters)) {
                return null;
            }
            $normalizedData = [];
            
            foreach ($filters as $filter => $filterOptions) {
                
                // the filter name should be a string
                if (!is_string($filter)) {
                    throw new ConfigurationException(
                        'Invalid filter name "'.$filter.'"',
                        $this->actionName,
                        $this->admin->getName())
                    ;
                }
                
                // normalize string notation
                // transform "name" => 'string' into "name" => ['type' => 'string']
                if (is_string($filterOptions)) {
                    $filterOptions = [
                        'type' => $filterOptions,
                    ];
                }
                
                // set the normalized data
                $normalizedData[$filter] = $filterOptions;
            }
            
            return $normalizedData;
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
            ->admin
            ->getConfiguration()
            ->getParameter('translation_pattern')
        ;
        
        if ($this->admin && $translationPattern) {
            // by default, the action title is action name using the configured translation pattern
            
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
    
    /**
     * Return the default route path according to the action name.
     *
     * @return string
     */
    private function getDefaultRoutePath()
    {
        $pattern = $this
            ->admin
            ->getConfiguration()
            ->getParameter('routing_url_pattern')
        ;
        
        $path = str_replace('{admin}', $this->admin->getName(), $pattern);
        $path = str_replace('{action}', $this->actionName, $path);
        
        if (in_array($this->actionName, ['edit', 'delete'])) {
            $path .= '/{id}';
        }
        
        return $path;
    }
    
    /**
     * Return the defaults route parameters according to a mapping based on the action name.
     *
     * @return array
     */
    private function getDefaultRouteDefaults()
    {
        $mapping = [
            'list' => [
                '_controller' => LAGAdminBundle::SERVICE_ID_LIST_ACTION,
                '_admin' => $this->admin->getName(),
                '_action' => $this->actionName,
            ],
            'create' => [
                '_controller' => LAGAdminBundle::SERVICE_ID_CREATE_ACTION,
                '_admin' => $this->admin->getName(),
                '_action' => $this->actionName,
            ],
            'edit' => [
                '_controller' => LAGAdminBundle::SERVICE_ID_EDIT_ACTION,
                '_admin' => $this->admin->getName(),
                '_action' => $this->actionName,
            ],
            'delete' => [
                '_controller' => LAGAdminBundle::SERVICE_ID_DELETE_ACTION,
                '_admin' => $this->admin->getName(),
                '_action' => $this->actionName,
            ],
        ];
        $defaults = [];
        
        if (array_key_exists($this->actionName, $mapping)) {
            $defaults = $mapping[$this->actionName];
        }
        
        return $defaults;
    }
    
    /**
     * Return the default route requirements according to the action name.
     *
     * @return array
     */
    private function getDefaultRouteRequirements()
    {
        $mapping = [
            'edit' => [
                'id' => '\d+',
            ],
            'delete' => [
                'id' => '\d+',
            ],
        ];
        $requirements = [];
        
        if (array_key_exists($this->actionName, $mapping)) {
            $requirements = $mapping[$this->actionName];
        }
        
        return $requirements;
    }
    
    /**
     * Return the default service id according to the action name.
     *
     * @return string|null
     */
    private function getDefaultServiceId()
    {
        $mapping = LAGAdminBundle::getDefaultActionServiceMapping();
        
        if (!array_key_exists($this->actionName, $mapping)) {
            return null;
        }
        
        return $mapping[$this->actionName];
    }
    
    /**
     * Return the default form according to the action name.
     *
     * @return string|null
     */
    private function getDefaultForm()
    {
        $mapping = [
            'list' => ListType::class,
            'delete' => DeleteType::class,
        ];
        
        if (!array_key_exists($this->actionName, $mapping)) {
            // try to get an admin globally configured form
            $adminForm = $this
                ->admin
                ->getConfiguration()
                ->getParameter('form')
            ;
            
            if (null !== $adminForm) {
                return $adminForm;
            }
            
            return null;
        }
        
        return $mapping[$this->actionName];
    }
    
    /**
     * Return a default form handler service id, or null, according to to the action name.
     *
     * @return mixed|null
     */
    private function getDefaultFormHandler()
    {
        $mapping = [
            'edit' => LAGAdminBundle::SERVICE_ID_EDIT_FORM_HANDLER,
            'list' => LAGAdminBundle::SERVICE_ID_LIST_FORM_HANDLER,
        ];
        
        if (!array_key_exists($this->actionName, $mapping)) {
            return null;
        }
        
        return $mapping[$this->actionName];
    }
    
    private function getDefaultFormOptions()
    {
        $mapping = [
            'list' => [
                'actions' => [
                    'lag.admin.delete' => 'delete',
                ]
            ],
        ];
    
        if (!$this->isActionInMapping($mapping)) {
            return [];
        }
    
        return $mapping[$this->actionName];
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
    
    private function getDefaultSortable()
    {
        $mapping = [
            'list' => true,
        ];
    
        if (!$this->isActionInMapping($mapping)) {
            return false;
        }
    
        return $mapping[$this->actionName];
    }
}
