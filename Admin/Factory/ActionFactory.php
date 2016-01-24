<?php

namespace LAG\AdminBundle\Admin\Factory;

use Exception;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionFactory
{
    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    public function __construct(
        FieldFactory $fieldFactory,
        FilterFactory $filterFactory,
        ApplicationConfiguration $configuration
    )
    {
        $this->fieldFactory = $fieldFactory;
        $this->filterFactory = $filterFactory;
        $this->configuration = $configuration;
    }

    /**
     * Create an Action from configuration values.
     *
     * @param string $actionName
     * @param array $actionConfiguration
     * @param AdminInterface $admin
     *
     * @return Action
     */
    public function create($actionName, array $actionConfiguration, AdminInterface $admin)
    {
        // resolving default options
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver, $actionName, $admin);
        $actionConfiguration = $resolver->resolve($actionConfiguration);

        // creating action object from configuration
        $action = $this->createActionFromConfiguration($actionConfiguration, $actionName);

        // creating actions linked to current action
        foreach ($actionConfiguration['actions'] as $customActionName => $customActionConfiguration) {
            // resolve configuration
            $customActionConfiguration = $resolver->resolve($customActionConfiguration);
            // create action
            $customAction = $this->createActionFromConfiguration($customActionConfiguration, $customActionName);
            // add to the main action
            $action->addAction($customAction);
        }

        // adding fields items to actions
        foreach ($actionConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            $field = $this
                ->fieldFactory
                ->create($fieldName, $fieldConfiguration);
            $action->addField($field);
        }

        // adding filters to the action
        foreach ($actionConfiguration['filters'] as $fieldName => $filterConfiguration) {
            $filter = $this
                ->filterFactory
                ->create($fieldName, $filterConfiguration);
            $action->addFilter($filter);
        }
        return $action;
    }

    /**
     * Create an action and its configuration object from configuration values
     *
     * @param array $actionConfiguration
     * @param $actionName
     * @return Action
     */
    protected function createActionFromConfiguration(array $actionConfiguration, $actionName)
    {
        $configuration = new ActionConfiguration($actionConfiguration);
        $action = new Action($actionName, $actionConfiguration, $configuration);

        return $action;
    }

    /**
     * Return action configuration resolver
     *
     * @param OptionsResolver $resolver
     * @param $actionName
     * @param Admin|null $admin
     */
    protected function configureOptionsResolver(OptionsResolver $resolver, $actionName, Admin $admin = null)
    {
        $defaultCriteria = [];

        if ($actionName == 'edit') {
            $defaultCriteria = [
                'id'
            ];
        } else if ($actionName == 'delete') {
            $defaultCriteria = [
                'id'
            ];
        }

        $resolver
            ->setDefaults([
                'title' => null,
                'fields' => [
                    'id' => [],
                ],
                'permissions' => ['ROLE_ADMIN'],
                'export' => [
                    'json',
                    'html',
                    'csv',
                    'xls'
                ],
                'order' => [],
                'actions' => [],
                'submit_actions' => [],
                'target' => '_self',
                'route' => '',
                'parameters' => [],
                'icon' => null,
                'filters' => [],
                'batch' => [],
                'load_strategy' => Admin::LOAD_STRATEGY_UNIQUE,
                'pager' => 'pagerfanta',
                'criteria' => $defaultCriteria
            ])
            ->setAllowedValues('pager', [
                null,
                'pagerfanta',
            ])
            ->setAllowedValues('load_strategy', [
                Admin::LOAD_STRATEGY_UNIQUE,
                Admin::LOAD_STRATEGY_MULTIPLE,
            ])
            ->setNormalizer('route', function (Options $options, $value) use ($admin, $actionName) {
                if (!$value) {
                    // if no route was provided, it should be linked to an Admin
                    if (!$admin) {
                        throw new Exception('No route was provided for action : ' . $actionName);
                    }
                    return $admin
                        ->generateRouteName($actionName);
                }
                return $value;
            })
            ->setNormalizer('title', function (Options $options, $value) use ($admin, $actionName) {
                if (!$value) {
                    $adminKey = '';
                    // if an Admin is linked to this action, we use its name in translation key
                    if ($admin) {
                        $adminKey = $admin->getName();
                    }
                    return $this->configuration->getTranslationKey($actionName, $adminKey);
                }
                return $value;
            })
            ->setNormalizer('batch', function (Options $options, $value) use ($admin, $actionName) {
                if ($value) {
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    foreach ($value as $key => $title) {
                        if (!$title) {
                            $adminKey = '';
                            // if an Admin is linked to this action, we use its name in translation key
                            if ($admin) {
                                $adminKey = $admin->getName();
                            }
                            $value[$key] = $this->configuration->getTranslationKey('batch.' . $key, $adminKey);
                        }
                    }
                }
                return $value;
            })
        ;
    }
}
