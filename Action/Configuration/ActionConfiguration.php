<?php

namespace LAG\AdminBundle\Action\Configuration;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\Configuration;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
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
     * Related Admin (optional)
     *
     * @var AdminInterface
     */
    protected $admin = null;

    /**
     * ActionConfiguration constructor.
     *
     * @param $actionName
     * @param AdminInterface $admin
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
        // action title, default to action's name
        $resolver
            ->setDefault('title', Container::camelize($this->actionName))
            ->setAllowedTypes('title', 'string');

        // displayed fields for this action
        $resolver
            ->setDefault('fields', [
                'id' => []
            ])
            ->setAllowedTypes('fields', 'array')
            ->setNormalizer('fields', function(Options $options, $fields) {
                $normalizedFields = [];

                foreach ($fields as $name => $field) {

                    if ($field === null) {
                        $field = [];
                    }

                    $normalizedFields[$name] = $field;
                }

                return $normalizedFields;
            })
        ;

        // action permissions. By default, only admin are allowed
        $resolver
            ->setDefault('permissions', [
                'ROLE_ADMIN'
            ]);

        // by default, all exports type are allowed
        $resolver
            ->setDefault('export', [
                'json',
                'html',
                'csv',
                'xls'
            ]);

        // entity will be retrived with this order. It should be an array of field/order mapping
        $resolver
            ->setDefault('order', [])
            ->setAllowedTypes('order', 'array');

        // the action route should be a string
        $resolver
            ->setDefault('route', '')
            ->setAllowedTypes('route', 'string')
            ->setNormalizer('route', function (Options $options, $value) {
                if (!$value) {
                    // if no route was provided, it should be linked to an Admin
                    if (!$this->admin) {
                        throw new InvalidOptionsException('No route was provided for action : ' . $this->actionName);
                    }

                    // generate default route from admin
                    return $this
                        ->admin
                        ->generateRouteName($this->actionName);
                }

                return $value;
            });

        // action parameters should be an array
        $resolver
            ->setDefault('route_parameters', [])
            ->setAllowedTypes('route_parameters', 'array');

        // font awesome icons
        $resolver
            ->setDefault('icon', '')
            ->setAllowedTypes('icon', 'string');

        // load strategy : determine which method should be called in the data provider
        $resolver
            ->setDefault('load_strategy', AdminInterface::LOAD_STRATEGY_UNIQUE)
            ->addAllowedValues('load_strategy', AdminInterface::LOAD_STRATEGY_NONE)
            ->addAllowedValues('load_strategy', AdminInterface::LOAD_STRATEGY_UNIQUE)
            ->addAllowedValues('load_strategy', AdminInterface::LOAD_STRATEGY_MULTIPLE)
            ->setNormalizer('load_strategy', function (Options $options, $value) {

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
            });

        // pagination configuration
        $resolver
            ->setDefault('pager', 'pagerfanta')
            ->addAllowedValues('pager', 'pagerfanta')
            ->addAllowedValues('pager', false)
        ;

        // criteria used to find entity in the data provider
        $resolver
            ->setDefault('criteria', [])
            ->setNormalizer('criteria', function (Options $options, $value) {

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
            })
        ;

        // filters
        $resolver->setDefault('filters', []);

        // menus
        $resolver->setDefault('menu', false);
//        $resolver->setNormalizer('menu', function(Options $options, $value) {
//
//            // if menu is enabled, either user provided an existing action name or a route, or list action is choosen
//            // by default, for each item
//            if ($value && is_array($value)) {
//
//                foreach ($value as $menu) {
//                    if (!array_key_exists('route', $menu)) {
//
//                        if (!array_key_exists('action', $menu))
//
//                    }
//                }
//
//            }
//        });
    }
}
