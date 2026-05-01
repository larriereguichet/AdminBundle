<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use Symfony\Component\Form\FormView;

final class Filters
{
    use TranslatableComponent;

    public FormView $form;
    public bool $show = false;
}
