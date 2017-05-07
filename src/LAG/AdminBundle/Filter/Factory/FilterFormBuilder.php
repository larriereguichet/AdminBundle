<?php

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Handle the creation of Filters.
 */
class FilterFormBuilder
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    
    /**
     * FilterFormBuilder constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }
    
    /**
     * Create a filter form using the given filters configuration.
     *
     * @param ActionConfiguration $configuration
     *
     * @return FormInterface|null
     */
    public function build(ActionConfiguration $configuration)
    {
        if (true !== $this->isFilterFormRequired($configuration)) {
           return null;
        }
        // retrieve the filters from the Action configuration
        $filters = $configuration->getParameter('filters');
        
        // create the filter form
        $builder = $this
            ->formFactory
            ->createBuilder(FormType::class, null, [
                'csrf_protection' => false,
            ])
            ->setMethod('get')
        ;
    
        foreach ($filters as $field => $options) {
            $type = $this->convertShortFormType($options['type']);
            $options = $this->mergeDefaultFormOptions($options['options']);
            
            $builder->add($field, $type, $options);
        }
    
        return $builder->getForm();
    }
    
    /**
     * Return true if the filter form require to be built.
     *
     * @param ActionConfiguration $configuration
     *
     * @return bool
     */
    public function isFilterFormRequired(ActionConfiguration $configuration)
    {
        $filters = $configuration->getParameter('filters');
    
        return 0 < count($filters);
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
        ];
    
        if (array_key_exists($type, $mapping)) {
            $type = $mapping[$type];
        }
    
        return $type;
    }
    
    /**
     * Merge default form options with the configured ones.
     *
     * @param array|null $formOptions
     *
     * @return array
     */
    private function mergeDefaultFormOptions(array $formOptions = null)
    {
        if (null === $formOptions) {
            $formOptions = [];
        }
    
        return array_merge([
            'required' => false,
        ], $formOptions);
    }
}
