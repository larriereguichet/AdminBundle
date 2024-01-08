<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\Grid\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Form\FormView;

interface GridViewBuilderInterface
{
    public function build(
        string $gridName,
        OperationInterface $operation,
        iterable $data,
        ?FormView $form = null,
        array $options = [],
    ): GridView;
}
