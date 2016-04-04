<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\EntityFieldInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

class Link extends StringField implements EntityFieldInterface
{
    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var Object
     */
    protected $entity;

    /**
     * Link target.
     *
     * @var string
     */
    protected $target;

    /**
     * If an url is provided we use it instead of route.
     *
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;

    /**
     * Font awesome icon name
     *
     * @var string
     */
    protected $icon;

    /**
     * Link text
     *
     * @var
     */
    protected $text;

    /**
     * Render link template filled with configured options
     *
     * @param mixed $value
     * @return string
     */
    public function render($value)
    {
        $text = $this->text ?: parent::render($value);
        $parameters = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->parameters as $parameterName => $fieldName) {
            if (!$fieldName) {
                $fieldName = $parameterName;
            }
            $parameters[$parameterName] = $accessor->getValue($this->entity, $fieldName);
        }
        $this->twig->getFunction('path');
        $render = $this->twig->render($this->template, [
            'text' => $text,
            'route' => $this->route,
            'parameters' => $parameters,
            'target' => $this->target,
            'url' => $this->url,
            'title' => $this->title,
            'icon' => $this->icon,
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
        $resolver->setDefaults([
            'length' => $this->configuration->getParameter('string_length'),
            'replace' => $this->configuration->getParameter('string_length_truncate'),
            'template' => 'LAGAdminBundle:Render:link.html.twig',
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
    }

    /**
     * Set resolved options.
     *
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->length = $options['length'];
        $this->replace = $options['replace'];
        $this->template = $options['template'];
        $this->title = $options['title'];
        $this->icon = $options['icon'];
        $this->route = $options['route'];
        $this->parameters = $options['parameters'];
        $this->target = $options['target'];
        $this->url = $options['url'];
        $this->text = $options['text'];
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
     * Define Twig engine.
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
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
