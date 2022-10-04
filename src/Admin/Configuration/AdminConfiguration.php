<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Admin\Configuration\Normalizer\LinkNormalizer;
use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\ORMDataProvider;
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
        $this->configureMain($resolver);
        $this->configureActions($resolver);
        $this->configureRouting($resolver);
        $this->configureData($resolver);
    }

    protected function configureMain(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('name')
            ->setAllowedTypes('name', 'string')

            ->setRequired('data_class')
            ->setAllowedTypes('data_class', 'string')

            ->setDefault('title', null)
            ->setAllowedTypes('title', ['string', 'null'])
            ->addNormalizer('title', function (Options $options, $value) {
                if ($value === null) {
                    $value = u($options->offsetGet('name'))
                        ->camel()
                        ->title()
                        ->toString()
                    ;
                }

                return $value;
            })

            ->setDefault('group', null)
            ->setAllowedTypes('group', ['string', 'null'])


            ->setDefault('admin_class', Admin::class)
            ->setAllowedTypes('admin_class', 'string')
        ;
    }

    protected function configureActions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('actions', [
                'index' => [],
                'create' => [],
                'update' => [],
                'delete' => [],
            ])
            ->setAllowedTypes('actions', 'array')
            ->setNormalizer('actions', $this->getActionNormalizer())

            // Linked actions
            ->setDefault('collection_actions', [
                'create' => [],
            ])
            ->setAllowedTypes('collection_actions', 'array')
            ->setNormalizer('collection_actions', function (Options $options, $value) {
                return LinkNormalizer::normalizeAdminLinks($options, $value);
            })

            ->setDefault('item_actions', [
                'update' => [],
                'delete' => [],
            ])
            ->setAllowedTypes('item_actions', 'array')
            ->setNormalizer('item_actions', function (Options $options, $value) {
                return LinkNormalizer::normalizeAdminLinks($options, $value);
            })
        ;
    }

    protected function configureRouting(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('controller', AdminAction::class)
            ->setAllowedTypes('controller', 'string')

            ->setDefault('routes_pattern', 'lag_admin.{admin}.{action}')
            ->setAllowedTypes('routes_pattern', 'string')
            ->setNormalizer('routes_pattern', function (Options $options, $value) {
                if (!u($value)->containsAny('{action}')) {
                    throw new InvalidOptionsException(sprintf('The "%s" parameters in admin "%s" should contains the "%s" parameters', 'routes_pattern', $options->offsetGet('name'), '{action}'));
                }

                if (!u($value)->containsAny('{admin}')) {
                    throw new InvalidOptionsException(sprintf('The "%s" parameters in admin "%s" should contains the "%s" parameters', 'routes_pattern', $options->offsetGet('name'), '{admin}'));
                }

                return $value;
            })
        ;
    }

    protected function configureData(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('pagination', true)
            ->setAllowedValues('pagination', 'boolean')

            ->setDefault('max_per_page', 25)
            ->setAllowedTypes('max_per_page', 'integer')

            ->setDefault('page_parameter', 'page')
            ->setAllowedTypes('page_parameter', 'string')

            ->setDefault('permissions', 'ROLE_ADMIN')
            ->setAllowedTypes('permissions', 'string')

            ->setDefault('date_format', 'Y-m-d')
            ->setAllowedTypes('date_format', 'string')

            ->setDefault('data_provider', ORMDataProvider::class)
            ->setAllowedTypes('data_provider', 'string')

            ->setDefault('data_processor', ORMDataProcessor::class)
            ->setAllowedTypes('data_processor', 'string')
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

    public function getTitle(): string
    {
        return $this->getString('title');
    }

    public function getGroup(): ?string
    {
        return $this->get('group');
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

    public function getIndexActions(): array
    {
        return $this->get('index_actions');
    }

    public function getItemActions(): array
    {
        return $this->get('item_actions');
    }

    public function getEntityClass(): string
    {
        return $this->getString('entity');
    }

    public function getController(): string
    {
        return $this->getString('controller');
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

    public function getUpdateTemplate(): string
    {
        return $this->getString('update_template');
    }

    public function getListTemplate(): string
    {
        return $this->getString('list_template');
    }

    public function getDeleteTemplate(): string
    {
        return $this->getString('delete_template');
    }

    private function getActionNormalizer(): Closure
    {
        return function (Options $options, $actions) {
            $normalizedActions = [];

            foreach ($actions as $name => $action) {
                // action configuration is an array by default
                if (null === $action) {
                    $action = [];
                }

                if (!\array_key_exists('route_parameters', $action)) {
                    if ($name === 'update' || $name === 'delete') {
                        $action['route_parameters'] = ['id' => null];
                    }
                }
                $action['admin_name'] = $options->offsetGet('name');
                $normalizedActions[$name] = $action;

            }

            return $normalizedActions;
        };
    }
}
