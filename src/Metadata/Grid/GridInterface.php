<?php

namespace LAG\AdminBundle\Metadata\Grid;

/** @deprecated  */
interface GridInterface
{
    public function getName(): ?string;

    public function getTemplate(): ?string;

    public function getType(): ?string;

    public function getAttributes(): array;

    public function getRowAttributes(): array;

    public function getHeaderRowAttributes(): array;

    public function getHeaderAttributes(): array;

    public function getFields(): array;

    public function getOptions(): array;
}
