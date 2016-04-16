<?php

namespace LAG\AdminBundle\Form\Type;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType
{
    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'datepicker',
            ],
            'widget' => 'single_text',
            'format' => $this->configuration->getParameter('date_format'),
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
