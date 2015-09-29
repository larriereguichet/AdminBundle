<?php

namespace BlueBear\AdminBundle\Form\Type;

use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
            'format' => $this->configuration->getDateFormat(),
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // dd/MM/Y HH:mm => dd/mm/yyyy hh:ii
        // convert Symfony date format into javascript datepicker date format
        $jsDateFormat = str_replace('Y', 'yyyy', $this->configuration->getDateFormat());
        $jsDateFormat = str_replace('mm', 'ii', $jsDateFormat);
        $jsDateFormat = str_replace('MM', 'mm', $jsDateFormat);
        $jsDateFormat = str_replace('HH', 'hh', $jsDateFormat);

        $view->vars['javascript_date_format'] = $jsDateFormat;
        $view->vars['javascript_language'] = substr($this->configuration->getLocale(), 0, 2);
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
