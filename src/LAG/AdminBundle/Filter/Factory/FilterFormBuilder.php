<?php

namespace LAG\AdminBundle\Filter\Factory;

use Exception;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Handle the creation of Filters.
 */
class FilterFormBuilderOLD
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * FilterFormBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param TranslatorInterface  $translator
     */
    public function __construct(FormFactoryInterface $formFactory, TranslatorInterface $translator)
    {
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }
    
    /**
     * Create a filter form using the given filters configuration.
     *
     * @param ActionConfiguration $configuration
     *
     * @return FormInterface|null
     *
     * @throws Exception
     */
    public function build(ActionConfiguration $configuration)
    {
        $filters = $configuration->getParameter('filters');

        if (0 === count($filters)) {
            return null;
        }
        $builder = $this
            ->formFactory
            ->createBuilder(FormType::class, null, [
                // TODO use the _csrf protection
                'csrf_protection' => false,
            ])
            ->setMethod('get')
        ;
    
        foreach ($filters as $field => $options) {
            $type = $this->convertShortFormType($options['type']);
            $options = $this->mergeDefaultFormOptions($field, $options['options']);
            
            $builder->add($field, $type, $options);
        }
    
        return $builder->getForm();
    }
    
    /**
     * Convert a shortcut type into its class type.
     *
     * @param $type
     *
     * @return string
     */
    private function convertShortFormType($type)
    {
        $mapping = [
            'choice' => ChoiceType::class,
            'string' => TextType::class,
            'entity' => EntityType::class,
        ];
    
        if (array_key_exists($type, $mapping)) {
            $type = $mapping[$type];
        }
    
        return $type;
    }

    /**
     * Merge default form options with the configured ones.
     *
     * @param string     $field
     * @param array|null $formOptions
     *
     * @return array
     *
     * @throws \TypeError
     */
    private function mergeDefaultFormOptions($field, array $formOptions = null)
    {
        if (null === $formOptions) {
            $formOptions = [];
        }
        $accessor = PropertyAccess::createPropertyAccessor();
        $defaultCssClass = 'form-control form-control-sm mb-2 mr-sm-2 mb-sm-0';
    
        // merge default css class with user's ones
        if ($accessor->isReadable($formOptions, '[attr][class]')) {
            $value = $accessor->getValue($formOptions, '[attr][class]');
            $accessor->setValue($formOptions, '[attr][class]', $defaultCssClass.' '.$value);
        } else {
            $accessor->setValue($formOptions, '[attr][class]', $defaultCssClass);
        }
    
        // provide a default placeholder
        if (!$accessor->isReadable($formOptions, 'attr[placeholder]')) {
            $placeholder = $this
                ->translator
                ->trans('lag.admin.search_by', [
                    '%field%' => $field,
                ])
            ;
            $accessor->setValue($formOptions, '[attr][placeholder]', $placeholder);
        }
        
        // filters inputs should be optional by default
        if (!$accessor->isReadable($formOptions, 'required')) {
            $accessor->setValue($formOptions, '[required]', false);
        }
        
        return $formOptions;
    }
}
