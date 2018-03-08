<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\Configuration\MappedConfiguration;
use LAG\AdminBundle\Field\TranslatorAwareFieldInterface;
use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class Mapped extends AbstractField implements TranslatorAwareFieldInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * Render value of the field.
     *
     * @param mixed $value Value to render
     *
     * @return mixed
     */
    public function render($value)
    {
        $mapping = $this->options['mapping'];
    
        if (!array_key_exists($value, $mapping)) {
            throw new LogicException('Value "'.$value.' " not found in mapping '.implode(',', $mapping));
        }
    
        return $this
            ->translator
            ->trans($mapping[$value])
        ;
    }
    
    /**
     * Configure options resolver.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('mapping')
            ->setAllowedTypes('mapping', 'array')
        ;
    }
    
    /**
     * Return field type.
     *
     * @return string
     */
    public function getType()
    {
        return AbstractField::TYPE_MAPPED;
    }
    
    /**
     * Defines translator.
     *
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Return the Field's configuration class.
     *
     * @return string
     */
    public function getConfigurationClass()
    {
        return MappedConfiguration::class;
    }
}
