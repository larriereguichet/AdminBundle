<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationExtension extends AbstractExtension
{
    private TranslationHelperInterface $translationHelper;

    public function __construct(TranslationHelperInterface $translationHelper)
    {
        $this->translationHelper = $translationHelper;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('admin_trans', [$this, 'translate']),
            new TwigFilter('admin_ui_trans', [$this, 'translateUI']),
        ];
    }

    public function translate(
        string $id,
        array $parameters = [],
        string $domain = null,
        string $locale = null,
        string $pattern = null,
        string $adminName = null
    ): string {
        return $this->translationHelper->transWithPattern($id, $parameters, $domain, $locale, $pattern, $adminName);
    }

    public function translateUI(string $id, array $parameters = []): string
    {
        return $this->translationHelper->transWithPattern($id, $parameters, null, null, null, 'ui');
    }
}
