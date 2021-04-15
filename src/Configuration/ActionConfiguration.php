<?php

namespace LAG\AdminBundle\Configuration;

use Closure;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Form\Type\AdminType;
use LAG\AdminBundle\Form\Type\DeleteType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class ActionConfiguration extends Configuration
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            // Main info
            ->setRequired('name')
            ->setAllowedTypes('name', 'string')
            ->setRequired('admin_name')
            ->setAllowedTypes('admin_name', 'string')
            ->setDefault('title', null)
            ->setAllowedTypes('title', ['string', 'null'])
            ->setNormalizer('title', $this->getTitleNormalizer())
            ->setDefault('icon', null)
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setDefault('action_class', Action::class)
            ->setAllowedTypes('action_class', 'string')
            ->setRequired('template')
            ->setAllowedTypes('template', 'string')

            // Routing
            ->setDefault('controller', AdminAction::class)
            ->setAllowedTypes('controller', 'string')
            ->setRequired('route')
            ->setAllowedTypes('route', 'string')
            ->setDefault('route_parameters', [])
            ->setAllowedTypes('route_parameters', 'array')
            ->setNormalizer('route_parameters', $this->getRouteParametersNormalizer())
            ->setDefault('path', null)
            ->setAllowedTypes('path', ['string', 'null'])
            ->setNormalizer('path', $this->getPathNormalizer())

            // Fields
            ->setRequired('fields')
            ->setAllowedTypes('fields', 'array')
            ->setNormalizer('fields', $this->getFieldsNormalizer())

            // Filter and orders
            ->setDefault('order', [])
            ->setAllowedTypes('order', 'array')
            ->setNormalizer('order', $this->getOrderNormalizer())
            ->setDefault('criteria', [])
            ->setAllowedTypes('criteria', 'array')
            ->setNormalizer('criteria', $this->getCriteriaNormalizer())
            ->setDefault('filters', [])
            ->setAllowedTypes('filters', 'array')
            ->setNormalizer('filters', $this->getFiltersNormalizer())

            // Security
            ->setDefault('permissions', ['ROLE_ADMIN'])
            ->setAllowedTypes('permissions', 'array')

            // Export
            ->setDefault('export', ['csv', 'xml', 'yaml'])
            ->setAllowedTypes('export', 'array')

            // Data
            ->setDefault('load_strategy', null)
            ->setAllowedValues('load_strategy', [
                null,
                AdminInterface::LOAD_STRATEGY_NONE,
                AdminInterface::LOAD_STRATEGY_UNIQUE,
                AdminInterface::LOAD_STRATEGY_MULTIPLE,
            ])
            ->setNormalizer('load_strategy', $this->getLoadStrategyNormalizer())
            ->setDefault('repository_method', null)
            ->setAllowedTypes('repository_method', ['string', 'null'])

            // Pagination
            ->setDefault('pager', 'pagerfanta')
            ->setAllowedValues('pager', ['pagerfanta', false])
            ->setDefault('max_per_page', 25)
            ->setAllowedTypes('max_per_page', 'integer')
            ->setDefault('page_parameter', 'page')
            ->setAllowedTypes('page_parameter', 'string')

            ->setDefault('date_format', 'Y-m-d')
            ->setAllowedTypes('date_format', 'string')

            // Form
            ->setDefault('form', null)
            ->setAllowedTypes('form', ['string', 'null', 'boolean'])
            ->setNormalizer('form', $this->getFormNormalizer())
            ->setDefault('form_options', [])
            ->setAllowedTypes('form_options', 'array')

            // Menus
            ->setDefault('menus', [])
            ->setAllowedTypes('menus', 'array')

            // Translation
            ->setDefault('translation', function (OptionsResolver $translationResolver) {
                $translationResolver
                    ->setDefault('enabled', true)
                    ->setAllowedTypes('enabled', 'boolean')
                    ->setDefault('pattern', 'admin.{admin}.{key}')
                    ->setAllowedTypes('pattern', 'string')
                    ->setDefault('catalog', 'admin')
                    ->setAllowedTypes('catalog', 'string')
                ;
            })
        ;
    }

    public function getName(): string
    {
        return $this->getString('name');
    }

    public function getAdminName(): string
    {
        return $this->getString('admin_name');
    }

    public function getTitle(): string
    {
        return $this->getString('title');
    }

    public function getIcon(): ?string
    {
        return $this->get('icon');
    }

    public function getActionClass(): string
    {
        return $this->getString('action_class');
    }

    public function getTemplate(): string
    {
        return $this->get('template');
    }

    public function getController(): string
    {
        return $this->getString('controller');
    }

    public function getRoute(): string
    {
        return $this->getString('route');
    }

    public function getRouteParameters(): array
    {
        return $this->get('route_parameters');
    }

    public function getPath(): string
    {
        return $this->getString('path');
    }

    public function getFields(): array
    {
        return $this->get('fields');
    }

    public function getOrder(): array
    {
        return $this->get('order');
    }

    public function getCriteria(): array
    {
        return $this->get('criteria');
    }

    public function getFilters(): array
    {
        return $this->get('filters');
    }

    public function getPermissions(): array
    {
        return $this->get('permissions');
    }

    public function getExport(): array
    {
        return $this->get('export');
    }

    public function getLoadStrategy(): string
    {
        return $this->getString('load_strategy');
    }

    public function isPaginationEnabled(): bool
    {
        $pager = $this->get('pager');

        if ($pager === false) {
            return false;
        }

        return true;
    }

    public function getPager(): string
    {
        if (!$this->isPaginationEnabled()) {
            throw new Exception('The pagination is not enabled');
        }

        return $this->getString('pager');
    }

    public function getMaxPerPage(): int
    {
        if (!$this->isPaginationEnabled()) {
            throw new Exception('The pagination is not enabled');
        }

        return $this->getInt('max_per_page');
    }

    public function getPageParameter(): string
    {
        return $this->getString('page_parameter');
    }

    public function getDateFormat(): string
    {
        return $this->getString('date_format');
    }

    public function getForm(): ?string
    {
        return $this->get('form');
    }

    public function getFormOptions(): array
    {
        return $this->get('form_options');
    }

    public function getMenus(): array
    {
        return $this->get('menus');
    }

    public function isTranslationEnabled(): bool
    {
        return $this->get('translation')['enabled'];
    }

    public function getTranslationPattern(): string
    {
        if (!$this->isTranslationEnabled()) {
            throw new Exception('The translation is not enabled');
        }

        return $this->get('translation')['pattern'];
    }

    public function getTranslationCatalog(): string
    {
        if (!$this->isTranslationEnabled()) {
            throw new Exception('The translation is not enabled');
        }

        return $this->get('translation')['catalog'];
    }

    public function getRepositoryMethod(): ?string
    {
        return $this->get('repository_method');
    }

    private function getTitleNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if ($value === null) {
                if ($options->offsetGet('translation')['enabled']) {
                    $value = u($options->offsetGet('translation')['pattern'])
                        ->replace('{admin}', $options->offsetGet('admin_name'))
                        ->replace('{key}', $options->offsetGet('name'))
                        ->toString()
                    ;
                } else {
                    $value = u($options->offsetGet('name'))->title()->toString();
                }
            }

            return $value;
        };
    }

    private function getPathNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if ($value !== null) {
                $path = u($value);

                if ($path->endsWith('/')) {
                    $path = $path->slice(0, -1);
                }

                return $path->toString();
            }
            $loadStrategy = $options->offsetGet('load_strategy');
            $path = u($options->offsetGet('admin_name'))
                ->snake()
                ->replace('_', '-')
            ;

            if (!$path->endsWith('s')) {
                $path = $path->append('s');

                if ($path->endsWith('ys')) {
                    $path = $path->before('ys')->append('ies');
                }
            }
            $snakeActionName = u($options->offsetGet('name'))
                ->snake()
                ->replace('_', '-')
            ;

            // Edit the the default action. It is not append to the path (ex: articles/{id} for edit,
            // articles/{id}/delete for delete)
            if ($loadStrategy === AdminInterface::LOAD_STRATEGY_UNIQUE) {
                $path = $path->append('/{id}');

                if ($options->offsetGet('name') !== 'edit') {
                    $path = $path
                        ->append('/')
                        ->append($snakeActionName)
                    ;
                }
            }

            if ($loadStrategy === AdminInterface::LOAD_STRATEGY_NONE) {
                $path = $path
                    ->append('/')
                    ->append($snakeActionName)
                ;
            }

            return $path->toString();
        };
    }

    /**
     * Return the field normalizer. It will transform null configuration into array to allow field type guessing
     * working.
     */
    private function getFieldsNormalizer(): Closure
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
     */
    private function getOrderNormalizer(): Closure
    {
        return function (Options $options, $order) {
            foreach ($order as $field => $sort) {
                if (!\is_string($sort) || !\is_string($field) || !\in_array(strtolower($sort), ['asc', 'desc'])) {
                    throw new Exception('Order value should be an array of string (["field" => $key]), got '.\gettype($sort));
                }
            }

            return $order;
        };
    }

    private function getLoadStrategyNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if ($value !== null) {
                return $value;
            }

            if ($options->offsetGet('name') == 'create') {
                $value = AdminInterface::LOAD_STRATEGY_NONE;
            } elseif ($options->offsetGet('name') == 'list') {
                $value = AdminInterface::LOAD_STRATEGY_MULTIPLE;
            } else {
                $value = AdminInterface::LOAD_STRATEGY_UNIQUE;
            }

            return $value;
        };
    }

    /**
     * Return the criteria normalizer. It will add the id parameters for the edit and delete actions if no value is
     * provided.
     */
    private function getCriteriaNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (!$value) {
                $idActions = [
                    'edit',
                    'delete',
                ];

                if (\in_array($options->offsetGet('name'), $idActions)) {
                    $value = [
                        'id',
                    ];
                }
            }

            return $value;
        };
    }

    /**
     * Return the filters normalizer.
     */
    private function getFiltersNormalizer(): Closure
    {
        return function (Options $options, $data) {
            $normalizedData = [];

            foreach ($data as $name => $field) {
                if (\is_string($field)) {
                    $field = [
                        'name' => $field,
                        'type' => TextType::class,
                        'options' => [],
                    ];
                } else {
                    $field['name'] = $name;
                }

                $filterConfiguration = new FilterConfiguration();
                $filterConfiguration->configure($field);

                $normalizedData[$field['name']] = $filterConfiguration->toArray();
            }

            return $normalizedData;
        };
    }

    private function getFormNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if ($value !== null && $value !== false) {
                return $value;
            }

            if ($value === false) {
                return null;
            }
            $mapping = [
                'create' => AdminType::class,
                'edit' => AdminType::class,
                'delete' => DeleteType::class,
            ];

            if (\array_key_exists($options->offsetGet('name'), $mapping)) {
                $value = $mapping[$options->offsetGet('name')];
            }

            return $value;
        };
    }

    private function getRouteParametersNormalizer(): Closure
    {
        return function (Options $options, $value) {
            if (\count($value) > 0) {
                return $value;
            }

            if ($options->offsetGet('name') === 'edit' || $options->offsetGet('name') === 'delete') {
                return ['id' => null];
            }

            return [];
        };
    }
}
