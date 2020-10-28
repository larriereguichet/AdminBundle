<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldConfiguration extends Configuration
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'type' => null,
                'options' => [],
            ])
            ->setAllowedTypes('type', ['string', 'null'])
            ->setNormalizer('type', function (Options $options, $value) {
                if ($value === null) {
                    $value = 'auto';
                }

                return $value;
            })

            ->setAllowedTypes('options', ['array', 'null'])
            ->setNormalizer('options', function (Options $options, $values) {
                if ($values === null) {
                    return [];
                }

                return $values;
            })
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
