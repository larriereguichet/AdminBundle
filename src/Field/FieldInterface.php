<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldInterface
{
    public function getName(): string;

    public function getOptions(): array;

    public function getOption(string $name);

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration);

    public function setOptions(array $options);

    public function isSortable(): bool;

    public function render($value = null): string;
}
