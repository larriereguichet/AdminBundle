<?php

namespace LAG\AdminBundle\Application\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Field\Action;
use LAG\AdminBundle\Field\Field\ActionCollection;
use LAG\AdminBundle\Field\Field\ArrayField;
use LAG\AdminBundle\Field\Field\Boolean;
use LAG\AdminBundle\Field\Field\Collection;
use LAG\AdminBundle\Field\Field\Count;
use LAG\AdminBundle\Field\Field\Date;
use LAG\AdminBundle\Field\Field\Link;
use LAG\AdminBundle\Field\Field\Mapped;
use LAG\AdminBundle\Field\Field\StringField;
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
                'title' => 'AdminBundle application',
                'description' => '',
                'locale' => 'en',
                // main base template
                // as bundles are not loaded when reading the configuration, the kernel locateResources will always failed.
                // So we must not check resource existence here.
                'base_template' => 'LAGAdminBundle::admin.layout.html.twig',
                'block_template' => 'LAGAdminBundle:Form:fields.html.twig',
                'bootstrap' => true,
                'date_format' => 'Y/m/d',
                // string length before truncation (0 means no truncation)
                'string_length' => 0,
                'string_length_truncate' =>  '...',
                'max_per_page' => 25,
                'admin_class' => Admin::class,
            ])
            ->setAllowedTypes('enable_extra_configuration', 'boolean')
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
        ;

        // routing configuration (route name pattern and url name pattern)
        $this->setRoutingOptions($resolver);

        // translation configuration
        $this->setTranslationOptions($resolver);

        // admin field type mapping
        $this->setFieldsOptions($resolver);
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function setRoutingOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('routing', [
            'url_pattern' => '/{admin}/{action}',
            'name_pattern' => 'lag.admin.{admin}.{action}',
        ]);
        $resolver->setAllowedTypes('routing', 'array');
        $resolver->setNormalizer('routing', function(Options $options, $value) {

            if (!array_key_exists('url_pattern', $value)) {
                $value['url_pattern'] = '/{admin}/{action}';
            }
            if (!array_key_exists('name_pattern', $value)) {
                $value['name_pattern'] = 'lag.admin.{admin}.{action}';
            }

            // url pattern should contain {admin} and {action} token
            $urlPattern = $value['url_pattern'];

            if (strstr($urlPattern, '{admin}') === false) {
                throw new InvalidOptionsException('Admin routing configuration url pattern should contains {admin} placeholder');
            }
            if (strstr($urlPattern, '{action}') === false) {
                throw new InvalidOptionsException('Admin routing configuration url pattern should contains the {action} placeholder');
            }

            // name pattern should contain {admin} token
            $namePattern = $value['name_pattern'];

            if (strstr($namePattern, '{admin}') === false) {
                throw new InvalidOptionsException('Admin routing configuration pattern name should contains the {admin} placeholder');
            }
            if (strstr($namePattern, '{action}') === false) {
                throw new InvalidOptionsException('Admin routing configuration pattern name should contains the {action} placeholder');
            }

            return $value;
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function setTranslationOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('translation', [
            'enabled' => true,
            'pattern' => 'lag.admin.{key}'
        ]);
        $resolver->setAllowedTypes('translation', 'array');
        $resolver->setNormalizer('translation', function(Options $options, $value) {

            if (!array_key_exists('enabled', $value)) {
                throw new InvalidOptionsException('Admin translation enabled parameter should be defined');
            }

            if (!is_bool($value['enabled'])) {
                throw new InvalidOptionsException('Admin translation enabled parameter should be a boolean');
            }

            if (!array_key_exists('pattern', $value)) {
                $value['pattern'] = '{admin}.{key}';
            }

            if ($value['enabled'] && strstr($value['pattern'], '{key}') === false) {
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
            AbstractField::TYPE_ARRAY => ArrayField::class,
            AbstractField::TYPE_LINK => Link::class,
            AbstractField::TYPE_DATE => Date::class,
            AbstractField::TYPE_COUNT => Count::class,
            AbstractField::TYPE_ACTION => Action::class,
            AbstractField::TYPE_COLLECTION => Collection::class,
            AbstractField::TYPE_BOOLEAN => Boolean::class,
            AbstractField::TYPE_MAPPED => Mapped::class,
            AbstractField::TYPE_ACTION_COLLECTION => ActionCollection::class,
        ];

        $resolver->setDefault('fields_mapping', $defaultMapping);
        $resolver->setAllowedTypes('fields_mapping', 'array');
        $resolver->setNormalizer('fields_mapping', function(Options $options, $value) use ($defaultMapping) {
            // merge with default mapping to allow override
            $value = array_merge($defaultMapping, $value);

            return $value;
        });

        // fields templates mapping
        $defaultMapping = [
            AbstractField::TYPE_LINK => 'LAGAdminBundle:Render:link.html.twig',
        ];

        $resolver->setDefault('fields_template_mapping', $defaultMapping);
        $resolver->setAllowedTypes('fields_template_mapping', 'array');
        $resolver->setNormalizer('fields_template_mapping', function(Options $options, $value) use ($defaultMapping) {
            // merge with default mapping to allow override
            $value = array_merge($defaultMapping, $value);

            return $value;
        });
    }
}
