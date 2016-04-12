<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\EntityFieldInterface;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Field\TwigFieldInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Link extends StringField implements EntityFieldInterface, TwigFieldInterface
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
            'route' => $this->options->get('route'),
            'parameters' => $this->options->get('parameters'),
            'target' => $this->options->get('target'),
            'url' => $this->options->get('url'),
            'title' => $this->options->get('title'),
            'icon' => $this->options->get('icon'),
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
            'length' => $this->applicationConfiguration->getParameter('string_length'),
            'replace' => $this->applicationConfiguration->getParameter('string_length_truncate'),
            'template' => $this->applicationConfiguration->getParameter('fields_template_mapping')[Field::TYPE_LINK],
            'title' => '',
            'icon' => '',
            'target' => '_self',
            'route' => '',
            'parameters' => [],
            'url' => '',
            'text' => '',
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
            if ($value === null && $options->offsetGet('url') === null) {
                throw new InvalidOptionsException('You must set either an url or a route');
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
