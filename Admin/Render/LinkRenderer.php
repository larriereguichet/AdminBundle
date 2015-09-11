<?php

namespace BlueBear\AdminBundle\Admin\Render;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

class LinkRenderer extends StringRenderer implements TwigRendererInterface, EntityRendererInterface
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
     * Initialize a link renderer
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        // default configuration
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'length' => null,
            'template' => 'BlueBearAdminBundle:Render:link.html.twig'
        ]);
        $resolver->setRequired([
            'route',
            'parameters',
        ]);
        $resolver->setAllowedTypes('route', 'string');
        $resolver->setAllowedTypes('parameters', 'array');
        $resolver->setAllowedTypes('length', 'integer');
        // resolve options
        $options = $resolver->resolve($options);
        $this->route = $options['route'];
        $this->parameters = $options['parameters'];
        $this->length = $options['length'];
        $this->template = $options['template'];
    }

    /**
     * Define Twig engine
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Define entity. It will be use to fill parameters with properties values
     *
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Render a <a> tag with its route and parameters
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        $text = parent::render($value);
        $parameters = [];
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->parameters as $parameterName => $fieldName) {
            if (!$fieldName) {
                $fieldName = $parameterName;
            }
            $parameters[$parameterName] = $accessor->getValue($this->entity, $fieldName);
        }
        $render = $this->twig->render($this->template, [
            'text' => $text,
            'route' => $this->route,
            'parameters' => $parameters
        ]);
        return $render;
    }
}
