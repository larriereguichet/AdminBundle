<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Field\Traits\TwigAwareTrait;

class BooleanField extends AbstractField implements TwigAwareFieldInterface
{
    use TwigAwareTrait;

    public function isSortable(): bool
    {
        return true;
    }

    public function render($value = null): string
    {
        return $this->twig->render('@LAGAdmin/Field/boolean.html.twig', [
            'value' => $value,
        ]);
    }
}
