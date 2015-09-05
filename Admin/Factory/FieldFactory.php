<?php

namespace BlueBear\AdminBundle\Admin\Factory;

use BlueBear\AdminBundle\Admin\Field;
use BlueBear\BaseBundle\Behavior\StringUtilsTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldFactory
{
    use StringUtilsTrait;

    protected $applicationConfiguration;

    public function __construct(array $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function create($fieldName, array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'length' => null,
            'format' => $this->applicationConfiguration['date_format'],
            'type' => 'string',
        ]);
        $configuration = $resolver->resolve($configuration);
        $field = new Field();
        $field->setName($fieldName);
        $field->setTitle($this->inflectString($fieldName));
        $field->setLength($configuration['length']);
        $field->setFormat($configuration['format']);

        return $field;
    }
}
