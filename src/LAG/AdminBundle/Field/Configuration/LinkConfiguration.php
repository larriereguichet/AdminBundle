<?php

namespace LAG\AdminBundle\Field\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfigurationAwareInterface;
use LAG\AdminBundle\Field\AbstractField;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkConfiguration extends Configuration implements ApplicationConfigurationAwareInterface
{
    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;
    
    public function configureOptions(OptionsResolver $resolver)
    {
        // inherit parent's option
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'length' => $this
                ->applicationConfiguration
                ->getParameter('string_length'),
            'replace' => $this
                ->applicationConfiguration
                ->getParameter('string_length_truncate'),
            'template' => $this
                ->applicationConfiguration
                ->getParameter('fields_template_mapping')[AbstractField::TYPE_LINK],
            'title' => '',
            'icon' => '',
            'target' => '_self',
            'route' => '',
            'parameters' => [],
            'url' => '',
            'text' => '',
            'admin' => null,
            'action' => null,
        ]);
        $resolver->setAllowedTypes('route', 'string');
        $resolver->setAllowedTypes('parameters', 'array');
        $resolver->setAllowedTypes('length', 'integer');
        $resolver->setAllowedTypes('url', 'string');
        $resolver->setAllowedValues('target', [
            '_self',
            '_blank',
        ]);
        $resolver->setNormalizer('route', function(Options $options, $value) {
            // route or url should be defined
            if (!$value && !$options->offsetGet('url') && !$options->offsetGet('admin')) {
                throw new InvalidOptionsException(
                    'You must set either an url or a route for the property'
                );
            }

            return $value;
        });
        $resolver->setNormalizer('admin', function(Options $options, $value) {
            // if a Admin is defined, an Action should be defined too
            if ($value && !$options->offsetGet('action')) {
                throw new InvalidOptionsException(
                    'An Action should be provided if an Admin is provided'
                );
            }

            return $value;
        });
    }
    
    /**
     * Define the application configuration.
     *
     * @param ApplicationConfiguration $configuration
     */
    public function setApplicationConfiguration(ApplicationConfiguration $configuration)
    {
        $this->applicationConfiguration = $configuration;
    }
}
