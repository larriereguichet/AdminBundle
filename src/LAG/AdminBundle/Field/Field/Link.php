<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Link extends StringField implements EntityAwareInterface, TwigAwareInterface
{
    /**
     * @var Object
     */
    protected $entity;

    /**
     * Render link template filled with configured options.
     *
     * @param mixed $value
     * @return string
     */
    public function render($value)
    {
        $text = $this->options->get('text') ?: parent::render($value);
        $parameters = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->options->get('parameters') as $parameterName => $fieldName) {
            if (!$fieldName) {
                $fieldName = $parameterName;
            }
            $parameters[$parameterName] = $accessor->getValue($this->entity, $fieldName);
        }

        $render = $this->twig->render($this->options->get('template'), [
            'text' => $text,
            'parameters' => $parameters,
            'options' => $this->options,
        ]);

        return $render;
    }

    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
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
                    'You must set either an url or a route for the property "'.$this->name.'"'
                );
            }

            return $value;
        });
        $resolver->setNormalizer('admin', function(Options $options, $value) {
            // if a Admin is defined, an Action should be defined too
            if ($value && !$options->offsetGet('action')) {
                throw new InvalidOptionsException(
                    'An Action should be provided if an Admin is provided for property "'.$this->name.'"'
                );
            }

            return $value;
        });
    }

    /**
     * Define field type.
     *
     * @return string
     */
    public function getType()
    {
        return 'link';
    }

    /**
     * Define entity. It will be use to fill parameters with properties values.
     *
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
