<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

/**
 * Ease Admin configuration manipulation.
 */
class AdminConfiguration extends Configuration
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('entity')
            ->setAllowedTypes('entity', 'string')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string')

            ->setDefault('actions', [
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => [],
            ])
            ->setAllowedTypes('actions', 'array')
            ->setNormalizer('actions', $this->getActionNormalizer())

            ->setDefault('controller', AdminAction::class)
            ->setAllowedTypes('controller', 'string')

            ->setDefault('batch', [])
            ->setAllowedTypes('batch', 'array')

            ->setDefault('admin_class', Admin::class)
            ->setAllowedTypes('admin_class', 'string')
            ->setDefault('action_class', Action::class)
            ->setAllowedTypes('action_class', 'string')

            ->setDefault('routes_pattern', 'lag_admin.{admin}.{action}')
            ->setAllowedTypes('routes_pattern', 'string')
            ->setNormalizer('routes_pattern', $this->getRoutesPatternNormalizer())

            ->setDefault('pager', 'pagerfanta')
            ->setAllowedValues('pager', ['pagerfanta', false])
            ->setDefault('max_per_page', 25)
            ->setAllowedTypes('max_per_page', 'integer')
            ->setDefault('page_parameter', 'page')
            ->setAllowedTypes('page_parameter', 'string')

            ->setDefault('permissions', 'ROLE_ADMIN')
            ->setAllowedTypes('permissions', 'string')

            ->setDefault('date_format', 'Y-m-d')
            ->setAllowedTypes('date_format', 'string')

            ->setDefault('data_provider', 'doctrine')
            ->setAllowedTypes('data_provider', 'string')
            ->setDefault('data_persister', 'doctrine')
            ->setAllowedTypes('data_persister', 'string')

            ->setDefault('create_template', '@LAGAdmin/crud/create.html.twig')
            ->setAllowedTypes('create_template', 'string')
            ->setDefault('edit_template', '@LAGAdmin/crud/edit.html.twig')
            ->setAllowedTypes('edit_template', 'string')
            ->setDefault('list_template', '@LAGAdmin/crud/list.html.twig')
            ->setAllowedTypes('list_template', 'string')
            ->setDefault('delete_template', '@LAGAdmin/crud/delete.html.twig')
            ->setAllowedTypes('delete_template', 'string')

            ->setDefault('menus', [])
            ->setAllowedTypes('menus', 'array')
        ;
    }

    public function getName(): string
    {
        return $this->getString('name');
    }

    public function getAdminClass(): string
    {
        return $this->getString('admin_class');
    }

    public function getActionClass(): string
    {
        return $this->getString('action_class');
    }

    public function getActions(): array
    {
        return $this->get('actions');
    }

    public function hasAction(string $actionName): bool
    {
        return \array_key_exists($actionName, $this->getActions());
    }

    public function getAction(string $actionName): array
    {
        return $this->getActions()[$actionName];
    }

    public function getEntityClass(): string
    {
        return $this->getString('entity');
    }

    public function getController(): string
    {
        return $this->getString('controller');
    }

    public function getBatch(): array
    {
        return $this->get('batch');
    }

    public function getRoutesPattern(): string
    {
        return $this->getString('routes_pattern');
    }

    public function isPaginationEnabled(): bool
    {
        $pager = $this->get('pager');

        return !($pager === false);
    }

    public function getPager(): string
    {
        if (!$this->isPaginationEnabled()) {
            throw new Exception(sprintf('The pagination is not enabled for the admin "%s"', $this->getString('name')));
        }

        return $this->get('pager');
    }

    public function getMaxPerPage(): int
    {
        return $this->getInt('max_per_page');
    }

    public function getPageParameter(): string
    {
        return $this->get('page_parameter');
    }

    public function getPermissions(): array
    {
        $roles = explode(',', $this->get('permissions'));

        foreach ($roles as $index => $role) {
            $roles[$index] = trim($role);
        }

        return $roles;
    }

    public function getDateFormat(): string
    {
        return $this->getString('date_format');
    }

    public function getActionRouteParameters(string $actionName): array
    {
        $actionConfiguration = $this->getAction($actionName);

        if (empty($actionConfiguration['route_parameters'])) {
            return [];
        }

        return $actionConfiguration['route_parameters'];
    }

    public function getDataProvider(): string
    {
        return $this->getString('data_provider');
    }

    public function getDataPersister(): string
    {
        return $this->getString('data_persister');
    }

    public function getCreateTemplate(): string
    {
        return $this->getString('create_template');
    }

    public function getEditTemplate(): string
    {
        return $this->getString('edit_template');
    }

    public function getListTemplate(): string
    {
        return $this->getString('list_template');
    }

    public function getDeleteTemplate(): string
    {
        return $this->getString('delete_template');
    }

    public function getMenus(): array
    {
        return $this->get('menus');
    }

    private function getActionNormalizer(): Closure
    {
        return function (Options $options, $actions) {
            $normalizedActions = [];
//            $addBatchAction = false;

            foreach ($actions as $name => $action) {
                // action configuration is an array by default
                if (null === $action) {
                    $action = [];
                }

                if (!\array_key_exists('route_parameters', $action)) {
                    if ($name === 'edit' || $name === 'delete') {
                        $action['route_parameters'] = ['id' => null];
                    }
                }
                $normalizedActions[$name] = $action;

                // in list action, if no batch was configured or disabled, we add a batch action
//                if ('list' == $name && (!\array_key_exists('batch', $action) || null === $action['batch'])) {
//                    $addBatchAction = true;
//                }
            }

            // add empty default batch action
//            if ($addBatchAction) {
//             TODO enable mass action
//            $normalizedActions['batch'] = [];
//            }

            return $normalizedActions;
        };
    }

    private function getRoutesPatternNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (!u($value)->containsAny('{action}')) {
                throw new InvalidOptionsException(sprintf('The "%s" parameters in admin "%s" should contains the "%s" parameters', 'routes_pattern', $options->offsetGet('name'), '{action}'));
            }

            if (!u($value)->containsAny('{admin}')) {
                throw new InvalidOptionsException(sprintf('The "%s" parameters in admin "%s" should contains the "%s" parameters', 'routes_pattern', $options->offsetGet('name'), '{admin}'));
            }

            return $value;
        };
    }
}
