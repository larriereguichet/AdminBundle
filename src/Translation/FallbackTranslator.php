<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Translation;

use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\String\u;

class FallbackTranslator implements TranslatorInterface
{
    public function __construct(
        private TranslatorInterface $decorated,
    ) {
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $translatedMessage = $this->decorated->trans($id, $parameters, $domain, $locale);

        if ($translatedMessage === $id && u($id)->startsWith('lag_admin.')) {
            $translatedMessage = $this->decorated->trans($id, $parameters, 'admin', $locale);
        }

        return $translatedMessage;
    }

    public function getLocale(): string
    {
        return $this->decorated->getLocale();
    }
}
