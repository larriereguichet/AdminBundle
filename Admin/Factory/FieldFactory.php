<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Field;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldFactory
{
    use StringUtilsTrait;

    /**
     * @var FieldRendererFactory
     */
    protected $fieldRendererFactory;

    public function __construct(FieldRendererFactory $fieldRendererFactory)
    {
        $this->fieldRendererFactory = $fieldRendererFactory;
    }

    public function create($fieldName, array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'type' => 'string',
            'options' => []
        ]);
        $resolver->setAllowedValues('type', [
            Field::TYPE_STRING,
            Field::TYPE_ARRAY,
            Field::TYPE_LINK,
            Field::TYPE_DATE,
        ]);
        //$this->applicationConfiguration['date_format']
        $configuration = $resolver->resolve($configuration);
        $renderer = $this->fieldRendererFactory->create($configuration['type'], $configuration['options']);
        $field = new Field($fieldName, $renderer);

        return $field;
    }
}
