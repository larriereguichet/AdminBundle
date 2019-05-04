<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldConfiguration extends Configuration
{
    /**
     * @var array
     */
    private $allowedFields;

    public function __construct(array $allowedFields = [])
    {
        $this->allowedFields = $allowedFields;

        parent::__construct();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'type' => null,
                'options' => [],
            ])
            // Set allowed fields type from tagged services
            ->setAllowedValues('type', array_merge([null], $this->allowedFields))
            ->setAllowedTypes('type', [
                'string',
                'null',
            ])
            ->setAllowedTypes('options', 'array')
        ;
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function getOptions(): array
    {
        return $this->get('options');
    }
}
