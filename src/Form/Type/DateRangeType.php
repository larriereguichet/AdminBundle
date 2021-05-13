<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DateRangeType extends AbstractType
{
    private ApplicationConfiguration $appConfig;

    public function __construct(ApplicationConfiguration $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => TranslationHelper::getTranslationKey($this->appConfig->getTranslationPattern(), 'ui', 'start_date'),
                'translation_domain' => $this->appConfig->getTranslationCatalog(),
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => TranslationHelper::getTranslationKey($this->appConfig->getTranslationPattern(), 'ui', 'end_date'),
                'translation_domain' => $this->appConfig->getTranslationCatalog(),
                'html5' => true,
                'widget' => 'single_text',
            ])
        ;
    }
}
