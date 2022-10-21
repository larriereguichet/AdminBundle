<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

// TODO remove
class TranslationExtension extends AbstractExtension
{
    public function __construct(
        private TranslatorInterface $translator,
        private ApplicationConfiguration $configuration,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('admin_trans', [$this, 'translate']),
            new TwigFilter('admin_trans_key', [$this, 'translateKey']),
            new TwigFilter('admin_ui_trans', [$this, 'translateUI']),
        ];
    }

    public function translate(string $id, array $parameters = []): string
    {
        return $this->translator->trans($id, $parameters, $this->configuration->get('translation_domain'));
    }
}
