<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\TwigAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanField extends AbstractField implements TwigAwareFieldInterface
{
    use TwigAwareTrait;

    public function isSortable(): bool
    {
        return true;
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver->setDefaults([
            'template' => '@LAGAdmin/Field/boolean.html.twig',
        ]);
    }

    public function render($value = null): string
    {
        return $this->twig->render($this->options['template'], [
            'value' => $value,
        ]);
    }
}
