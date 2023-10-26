<?php

namespace LAG\AdminBundle\Grid;

use LAG\AdminBundle\Validation\Constraint\TemplateValid;
use Symfony\Component\Validator\Constraints as Assert;

class Grid implements GridInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1)]
        private string $name,
        #[TemplateValid]
        private string $template,
        /** @var array<string, string> $templateMapping */
        private array $templateMapping = [],
        /** @var array<string, string> $templateMapping */
        private array $options = [],
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getTemplateMapping(): array
    {
        return $this->templateMapping;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
