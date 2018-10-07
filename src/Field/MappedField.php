<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\TranslatorTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappedField extends AbstractField implements TranslatorAwareFieldInterface
{
    use TranslatorTrait;

    private $translationEnabled = false;

    public function isSortable(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver
            ->setRequired('map')
            ->setAllowedTypes('map', 'array')
        ;
        $this->translationEnabled = $actionConfiguration->getAdminConfiguration()->getParameter('translation');
    }

    public function render($value = null): string
    {
        if (key_exists($value, $this->options['map'])) {
            $value = $this->options['map'][$value];
        }

        if ($this->translationEnabled) {
            $value = $this->translator->trans($value);
        }

        return (string)$value;
    }
}
