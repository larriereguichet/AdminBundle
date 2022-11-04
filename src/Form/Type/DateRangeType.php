<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'lag_admin.form.start_date',
                'translation_domain' => $this->applicationConfiguration->get('translation_domain'),
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'lag_admin.form.end_date',
                'translation_domain' => $this->applicationConfiguration->get('translation_domain'),
                'html5' => true,
                'widget' => 'single_text',
            ])
        ;
    }
}
