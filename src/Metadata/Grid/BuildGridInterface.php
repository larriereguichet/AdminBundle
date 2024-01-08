<?php

namespace LAG\AdminBundle\Metadata\Grid;

interface BuildGridInterface extends GridInterface
{
    public function withName(string $name): self;

    public function withTranslationDomain(string $translationDomain): self;

    public function withAttributes(array $attributes): self;

    public function withRowAttributes(array $rowAttributes): self;

    public function withHeaderRowAttributes(array $headerRowAttributes): self;

    public function withHeaderAttributes(array $headerAttributes): self;
}
