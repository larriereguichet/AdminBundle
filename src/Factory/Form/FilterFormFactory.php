<?php

namespace LAG\AdminBundle\Factory\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Translation\Helper\TranslationHelper;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FilterFormFactory implements FilterFormFactoryInterface
{
    private FormFactoryInterface $formFactory;
    private TranslationHelper $helper;

    public function __construct(FormFactoryInterface $formFactory, TranslationHelper $helper)
    {
        $this->formFactory = $formFactory;
        $this->helper = $helper;
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
                    $configuration->getTranslationPattern(),
                    $configuration->getName(),
                    $configuration->getTranslationCatalog()
                ),
            ];

            if (DateType::class === $formType) {
                $formOptions['html5'] = true;
                $formOptions['widget'] = 'single_text';
            }
            $formOptions = array_merge($formOptions, $filter['options']);
            $form->add($name, $formType, $formOptions);
        }

        return $form;
    }
}
