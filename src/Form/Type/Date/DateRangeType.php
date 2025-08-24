<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Date;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

// TODO keep ?
final class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'lag_admin.form.start_date',
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'lag_admin.form.end_date',
                'html5' => true,
                'widget' => 'single_text',
            ])
        ;
    }
}
