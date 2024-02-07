<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\Grid\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Form\FormView;

interface GridBuilderInterface
{
    public function buildView(
        string $gridName,
        OperationInterface $operation,
        iterable $data,
        array $options = [],
    ): GridView;
}
