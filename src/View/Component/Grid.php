<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\GridView;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\UX\TwigComponent\Attribute\PreMount;

final class Grid implements DynamicTemplateComponentInterface
{
    public GridView $grid;
    public mixed $data;

    #[PreMount]
    public function validate(array $data): void
    {
        if (!$data['grid'] instanceof GridView) {
            throw new UnexpectedTypeException($data['grid'], GridView::class);
        }

        if ($data['grid']->template === null) {
            throw new Exception('The grid view should have a template');
        }
    }

    public function getTemplate(): ?string
    {
        return $this->grid->template;
    }
}
