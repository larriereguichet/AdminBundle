<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DateRangeType extends AbstractType
{
    private ApplicationConfiguration $applicationConfiguration;

    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO labels
        $builder
            ->add('startDate', DateType::class, [
                'label' => TranslationHelper::getTranslationKey($this->applicationConfiguration->getTranslationPattern(), 'ui', 'start_date'),
                'translation_domain' => $this->applicationConfiguration->getTranslationDomain(),
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => TranslationHelper::getTranslationKey($this->applicationConfiguration->getTranslationPattern(), 'ui', 'end_date'),
                'translation_domain' => $this->applicationConfiguration->getTranslationDomain(),
                'html5' => true,
                'widget' => 'single_text',
            ])
        ;
    }
}
