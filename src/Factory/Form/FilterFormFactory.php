<?php

namespace LAG\AdminBundle\Factory\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Form\Type\Select2\Select2Type;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FilterFormFactory implements FilterFormFactoryInterface
{
    private FormFactoryInterface $formFactory;
    private TranslationHelper $helper;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        FormFactoryInterface $formFactory,
        TranslationHelper $helper,
        ApplicationConfiguration $appConfig
    ) {
        $this->formFactory = $formFactory;
        $this->helper = $helper;
        $this->appConfig = $appConfig;
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
                'label' => $this->helper->transWithPattern(
                    $name,
                    [],
                    $this->appConfig->getTranslationCatalog(),
                    null,
                    $this->appConfig->getTranslationPattern(),
                    $configuration->getName()
                ),
            ];

            if (DateType::class === $formType) {
                $formOptions['html5'] = true;
                $formOptions['widget'] = 'single_text';
            }

            if (Select2Type::class === $formType || ChoiceType::class === $formType) {
                $formOptions['choice_translation_domain'] = $this->appConfig->getTranslationCatalog();
            }
            $formOptions = array_merge($formOptions, $filter['options']);
            $form->add($name, $formType, $formOptions);
        }

        return $form;
    }
}
