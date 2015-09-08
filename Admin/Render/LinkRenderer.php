<?php

namespace BlueBear\AdminBundle\Admin\Render;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig_Environment;

class LinkRenderer extends StringRenderer implements RendererInterface
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

    public function __construct(array $options = [])
    {
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
        $options = $resolver->resolve($options);
        $this->route = $options['route'];
        $this->parameters = $options['parameters'];
        $this->length = $options['length'];
        $this->template = $options['template'];
    }

    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render($value)
    {
        $text = parent::render($value);
        $render = $this->twig->render($this->template, [
            'text' => $text
        ]);
        return $render;
    }
}
