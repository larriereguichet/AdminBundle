<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Factory\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Form\Type\Select2\Select2Type;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use function Symfony\Component\String\u;

class FilterFormFactory implements FilterFormFactoryInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private ApplicationConfiguration $applicationConfiguration
    ) {
    }

    public function create(AdminInterface $admin): FormInterface
    {
        $form = $this
            ->formFactory
            ->createNamed('filter', FormType::class, null, [
                'label' => false,
            ])
        ;
        $configuration = $admin->getConfiguration();
        $filters = $admin->getAction()->getConfiguration()->getFilters();

        foreach ($filters as $name => $filter) {
            // The short is used to avoid passing the full form type class in the configuration
            $formType = FormUtils::convertShortFormType($filter['type']);
            $formOptions = [
                // Filters are optional
                'required' => false,
                // The label translation key should use the admin translation pattern to have predictable translation
                // keys
                'label' => sprintf('lag_admin.%s.form_label', u($admin->getName())->title()),
            ];

            if (DateType::class === $formType) {
                $formOptions['html5'] = true;
                $formOptions['widget'] = 'single_text';
            }

            if (Select2Type::class === $formType || ChoiceType::class === $formType) {
                $formOptions['choice_translation_domain'] = $this->applicationConfiguration->getTranslationDomain();
            }
            $formOptions = array_merge($formOptions, $filter['options']);
            $form->add($name, $formType, $formOptions);
        }

        return $form;
    }
}
