<?php

namespace LAG\AdminBundle\Application\Configuration;

use LAG\AdminBundle\Configuration\Configuration;
use LAG\AdminBundle\Configuration\ConfigurationInterface;
use LAG\AdminBundle\Field\Field;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application configuration class. Allow easy configuration manipulation within an Admin.
 */
class ApplicationConfiguration extends Configuration implements ConfigurationInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * ApplicationConfiguration constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        // call parent constructor
        parent::__construct();

        // kernel is required to validate resources location
        $this->kernel = $kernel;
    }

    /**
     * Configure configuration allowed parameters.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // enable or disable extra configuration listener
        $resolver->setDefault('enable_extra_configuration', true);
        $resolver->setAllowedTypes('enable_extra_configuration', 'boolean');

        // application title
        $resolver->setDefault('title', 'AdminBundle application');
        $resolver->setAllowedTypes('title', 'string');

        // application description
        $resolver->setDefault('description', '');
        $resolver->setAllowedTypes('description', 'string');

        // application locale (en by default)
        $resolver->setDefault('locale', 'en');
        $resolver->setAllowedTypes('locale', 'string');

        // main base template
        $resolver->setDefault('base_template', 'LAGAdminBundle::admin.layout.html.twig');
        $resolver->setAllowedTypes('base_template', 'string');
        $resolver->setNormalizer('base_template', function (Options $options, $value) {
            // resource must exists
             $this
                ->kernel
                ->locateResource($value);

            return $value;
        });

        // form block template
        $resolver->setDefault('block_template', 'LAGAdminBundle:Form:fields.html.twig');
        $resolver->setAllowedTypes('block_template', 'string');
        $resolver->setNormalizer('block_template', function (Options $options, $value) {
            // resource must exists
            $this
                ->kernel
                ->locateResource($value);

            return $value;
        });

        // use bootstrap or not
        $resolver->setDefault('bootstrap', true);
        $resolver->setAllowedTypes('bootstrap', 'boolean');

        // general date format
        $resolver->setDefault('date_format', 'Y/m/d');
        $resolver->setAllowedTypes('date_format', 'string');

        // string length before truncation (0 means no truncation)
        $resolver->setDefault('string_length', 0);
        $resolver->setAllowedTypes('string_length', 'integer');

        $resolver->setDefault('string_length_truncate', '...');
        $resolver->setAllowedTypes('string_length_truncate', 'string');

        // routing configuration (route name pattern and url name pattern)
        $resolver->setDefault('routing', [
            'url_pattern' => '/{admin}/{action}',
            'name_pattern' => 'lag.admin.{admin}',
        ]);
        $resolver->setAllowedTypes('routing', 'array');
        $resolver->setNormalizer('routing', function (Options $options, $value) {

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

            return $value;
        });

        // translation configuration
        $resolver->setDefault('translation', [
            'enabled' => true,
            'pattern' => 'lag.admin.{key}'
        ]);
        $resolver->setAllowedTypes('translation', 'array');
        $resolver->setNormalizer('translation', function (Options $options, $value) {

            if (!is_bool($value['enabled'])) {
                throw new InvalidOptionsException('Admin translation enabled parameter should be a boolean');
            }

            if (strstr($value['pattern'], '{key}') === false) {
                throw new InvalidOptionsException('Admin translation pattern should contains the {key} placeholder');
            }

            return $value;
        });

        // maximum number of elements displayed
        $resolver->setDefault('max_per_page', 25);
        $resolver->setAllowedTypes('max_per_page', 'integer');

        // admin field type mapping
        $defaultMapping = [
            Field::TYPE_STRING => 'LAG\AdminBundle\Field\Field\StringField',
            Field::TYPE_ARRAY => 'LAG\AdminBundle\Field\Field\ArrayField',
            Field::TYPE_LINK => 'LAG\AdminBundle\Field\Field\Link',
            Field::TYPE_DATE => 'LAG\AdminBundle\Field\Field\Date',
            Field::TYPE_COUNT => 'LAG\AdminBundle\Field\Field\Count',
            Field::TYPE_ACTION => 'LAG\AdminBundle\Field\Field\Action',
            Field::TYPE_COLLECTION => 'LAG\AdminBundle\Field\Field\Collection',
            Field::TYPE_BOOLEAN => 'LAG\AdminBundle\Field\Field\Boolean',
        ];

        $resolver->setDefault('fields_mapping', $defaultMapping);
        $resolver->setAllowedTypes('fields_mapping', 'array');
        $resolver->setNormalizer('fields_mapping', function (Options $options, $value) use ($defaultMapping) {
            // merge with default mapping to allow override
            $value = array_merge($defaultMapping, $value);

            return $value;
        });
    }
}
