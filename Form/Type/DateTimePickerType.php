<?php

namespace BlueBear\AdminBundle\Form\Type;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimePickerType extends AbstractType
{
    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'datepicker'
            ],
            'widget' => 'single_text',
            'format' => $this->configuration->getDateFormat()
        ]);
    }

    public function getParent()
    {
        return 'datetime';
    }

    public function getName()
    {
        return 'datetime_picker';
    }

    /**
     * @param ApplicationConfiguration $configuration
     */
    public function setConfiguration(ApplicationConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }
}
