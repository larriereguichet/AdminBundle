<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Field;
use BlueBear\AdminBundle\Admin\Render\RendererInterface;
use BlueBear\AdminBundle\Admin\Render\TwigRendererInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldRendererFactory
{
    /**
     * @var array
     */
    protected $renderMapping;

    protected $twig;

    public function __construct(array $renderMapping = [], \Twig_Environment $twig)
    {
        // TODO allow thoses values in configuration
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            Field::TYPE_STRING => 'BlueBear\AdminBundle\Admin\Render\StringRenderer',
            Field::TYPE_ARRAY => 'BlueBear\AdminBundle\Admin\Render\ArrayRenderer',
            Field::TYPE_LINK => 'BlueBear\AdminBundle\Admin\Render\LinkRenderer',
            Field::TYPE_DATE => 'BlueBear\AdminBundle\Admin\Render\DateRenderer',
        ]);
        $this->renderMapping = $resolver->resolve($renderMapping);
        $this->twig = $twig;
    }

    /**
     * @param $fieldType
     * @param array $fieldOptions
     * @return RendererInterface
     */
    public function create($fieldType, $fieldOptions = [])
    {
        if (array_key_exists($fieldType, $this->renderMapping)) {
            $class = $this->renderMapping[$fieldType];
            $renderer = new $class($fieldOptions);

            if (in_array(TwigRendererInterface::class, class_implements($renderer))) {
                /** @var TwigRendererInterface $renderer */
                $renderer->setTwig($this->twig);
            }
        } else {
            throw new InvalidConfigurationException('Invalid field type "' . $fieldType . '"for renderer');
        }
        return $renderer;
    }
}
