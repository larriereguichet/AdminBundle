<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use BlueBear\AdminBundle\Admin\Field;
use BlueBear\AdminBundle\Admin\Render\RendererInterface;
use BlueBear\AdminBundle\Admin\Render\TwigRendererInterface;
use Exception;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig_Environment;

class FieldRendererFactory
{
    /**
     * @var array
     */
    protected $renderMapping;

    protected $twig;

    /**
     * @var ApplicationConfiguration
     */
    protected $application;

    public function __construct(array $renderMapping = [], Twig_Environment $twig, ApplicationConfiguration $application)
    {
        $resolver = new OptionsResolver();
        // TODO allow thoses values in configuration
        $resolver->setDefaults([
            Field::TYPE_STRING => 'BlueBear\AdminBundle\Admin\Render\StringRenderer',
            Field::TYPE_ARRAY => 'BlueBear\AdminBundle\Admin\Render\ArrayRenderer',
            Field::TYPE_LINK => 'BlueBear\AdminBundle\Admin\Render\LinkRenderer',
            Field::TYPE_DATE => 'BlueBear\AdminBundle\Admin\Render\DateRenderer',
        ]);
        $this->renderMapping = $resolver->resolve($renderMapping);
        $this->twig = $twig;
        $this->application = $application;
    }

    /**
     * @param $fieldType
     * @param array $fieldOptions
     * @return RendererInterface
     * @throws Exception
     */
    public function create($fieldType, $fieldOptions = [])
    {
        if (array_key_exists($fieldType, $this->renderMapping)) {
            $class = $this->renderMapping[$fieldType];
            $renderer = new $class($fieldOptions, $this->application);

            if (!in_array(RendererInterface::class, class_implements($renderer))) {
                throw new Exception("The render $class should implements RenderInterface");
            }
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
