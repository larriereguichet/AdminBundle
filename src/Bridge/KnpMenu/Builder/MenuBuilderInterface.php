<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\KnpMenu\Builder;

use Knp\Menu\ItemInterface;

interface MenuBuilderInterface
{
    public function build(array $options = []): ItemInterface;

    public function getName(): string;
}
