<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\ActionCollectionField;
use LAG\AdminBundle\Field\ActionField;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Field\BooleanField;
use LAG\AdminBundle\Field\CollectionField;
use LAG\AdminBundle\Field\CountField;
use LAG\AdminBundle\Field\DateField;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Field\MappedField;
use LAG\AdminBundle\Field\StringField;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application configuration class. Allow easy configuration manipulation within an Admin.
 */
class ApplicationConfiguration extends Configuration
{
    /**
     * Configure configuration allowed parameters.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // enable or disable extra configuration listener
                'enable_extra_configuration' => true,
                'enable_security' => true,
                'enable_menus' => true,
                'enable_homepage' => true,
                'translation' => false,
                'translation_pattern' => false,
                'title' => 'AdminBundle application',
                'description' => '',
                'locale' => 'en',
                // Main base template as bundles are not loaded when reading the configuration, the kernel
                // locateResources will always failed. So we must not check resource existence here.
                'base_template' => '@LAGAdmin/base.html.twig',
                'block_template' => '@LAGAdmin/Form/fields.html.twig',
                'menu_template' => '@LAGAdmin/Menu/menu.html.twig',
                'list_template' => '@LAGAdmin/CRUD/list.html.twig',
                'edit_template' => '@LAGAdmin/CRUD/edit.html.twig',
                'create_template' => '@LAGAdmin/CRUD/create.html.twig',
                'delete_template' => '@LAGAdmin/CRUD/delete.html.twig',
                'homepage_template' => '@LAGAdmin/Pages/home.html.twig',
                'homepage_route' => 'lag.admin.homepage',
                'routing_url_pattern' => '/{admin}/{action}',
                'routing_name_pattern' => 'lag.admin.{admin}.{action}',
                'bootstrap' => true,
                'date_format' => 'Y/m/d',
                'pager' => 'pagerfanta',
                // string length before truncation (0 means no truncation)
                'string_length' => 200,
                'string_length_truncate' => '...',
                'max_per_page' => 20,
                'admin_class' => Admin::class,
                'action_class' => Action::class,
                'permissions' => 'ROLE_ADMIN',
                'page_parameter' => 'page',
            ])
            ->setAllowedTypes('enable_extra_configuration', 'boolean')
            ->setAllowedTypes('enable_security', 'boolean')
            ->setAllowedTypes('enable_menus', 'boolean')
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('description', 'string')
            ->setAllowedTypes('locale', 'string')
            ->setAllowedTypes('base_template', 'string')
            ->setAllowedTypes('block_template', 'string')
            ->setAllowedTypes('bootstrap', 'boolean')
            ->setAllowedTypes('date_format', 'string')
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setAllowedTypes('max_per_page', 'integer')
            ->setAllowedTypes('admin_class', 'string')
            ->setAllowedTypes('routing_name_pattern', 'string')
            ->setAllowedTypes('routing_url_pattern', 'string')
            ->setAllowedTypes('page_parameter', 'string')
            ->setNormalizer('routing_name_pattern', function (Options $options, $value) {
                if (false === strstr($value, '{admin}')) {
                    throw new InvalidOptionsException(
                        'Admin routing configuration pattern name should contains the {admin} placeholder'
                    );
                }
                if (false === strstr($value, '{action}')) {
                    throw new InvalidOptionsException(
                        'Admin routing configuration pattern name should contains the {action} placeholder'
                    );
                }

                return $value;
            })
            ->setNormalizer('routing_url_pattern', function (Options $options, $value) {
                if (false === strstr($value, '{admin}')) {
                    throw new InvalidOptionsException('Admin routing configuration url pattern should contains {admin} placeholder');
                }

                if (false === strstr($value, '{action}')) {
                    throw new InvalidOptionsException('Admin routing configuration url pattern should contains the {action} placeholder');
                }

                return $value;
            })
        ;
        // admin field type mapping
        $this->setFieldsOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function setTranslationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation', [
            'enabled' => true,
            'pattern' => 'lag.admin.{key}',
        ]);
        $resolver->setAllowedTypes('translation', 'array');
        $resolver->setNormalizer('translation', function (Options $options, $value) {
            if (!array_key_exists('enabled', $value)) {
                throw new InvalidOptionsException('Admin translation enabled parameter should be defined');
            }

            if (!is_bool($value['enabled'])) {
                throw new InvalidOptionsException('Admin translation enabled parameter should be a boolean');
            }

            if (!array_key_exists('pattern', $value)) {
                $value['pattern'] = '{admin}.{key}';
            }

            if ($value['enabled'] && false === strstr($value['pattern'], '{key}')) {
                throw new InvalidOptionsException('Admin translation pattern should contains the {key} placeholder');
            }

            return $value;
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function setFieldsOptions(OptionsResolver $resolver)
    {
        $defaultMapping = [
            AbstractField::TYPE_STRING => StringField::class,
            AbstractField::TYPE_INTEGER => StringField::class,
            AbstractField::TYPE_ARRAY => ArrayField::class,
            AbstractField::TYPE_ACTION => ActionField::class,
            AbstractField::TYPE_COLLECTION => CollectionField::class,
            AbstractField::TYPE_BOOLEAN => BooleanField::class,
            AbstractField::TYPE_MAPPED => MappedField::class,
            AbstractField::TYPE_ACTION_COLLECTION => ActionCollectionField::class,
            AbstractField::TYPE_LINK => LinkField::class,
            AbstractField::TYPE_DATE => DateField::class,
            AbstractField::TYPE_COUNT => CountField::class,
        ];

        $resolver->setDefault('fields_mapping', $defaultMapping);
        $resolver->setAllowedTypes('fields_mapping', 'array');
        $resolver->setNormalizer('fields_mapping', function (Options $options, $value) use ($defaultMapping) {
            // Merge with default mapping to allow override
            $value = array_merge($defaultMapping, $value);

            return $value;
        });

        // Fields templates mapping
        $defaultMapping = [
            AbstractField::TYPE_LINK => 'LAGAdminBundle:Render:link.html.twig',
        ];

        $resolver->setDefault('fields_template_mapping', $defaultMapping);
        $resolver->setAllowedTypes('fields_template_mapping', 'array');
        $resolver->setNormalizer('fields_template_mapping', function (Options $options, $value) use ($defaultMapping) {
            // Merge with default mapping to allow override
            $value = array_merge($defaultMapping, $value);

            return $value;
        });
    }
}
