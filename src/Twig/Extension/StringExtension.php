<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class StringExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $translationDomain,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('pluralize', [$this, 'pluralize']),
            new TwigFilter('admin_trans', [$this, 'translate']),
        ];
    }

    public function pluralize(string $singular): string
    {
        return (new EnglishInflector())->pluralize($singular)[0];
    }

    public function translate(string $message, array $parameters = []): string
    {
        return $this->translator->trans($message, $parameters, $this->translationDomain);
    }
}
