<?php

namespace LAG\AdminBundle\Translation\Helper;

use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationHelper implements TranslationHelperInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the translation pattern with keys "{admin}" and "{key}" replaced by their values.
     */
    public static function getTranslationKey(
        string $translationPattern,
        string $adminName,
        string $key
    ): string {
        $u = new UnicodeString($key);
        $u = $u->snake();
        $translationPattern = str_replace('{key}', $u->toString(), $translationPattern);
        $translationPattern = str_replace('{admin}', $adminName, $translationPattern);

        return $translationPattern;
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function transWithPattern(string $message, string $pattern, string $adminName, string $catalog): string
    {
        $key = self::getTranslationKey(
            $pattern,
            $adminName,
            $message
        );

        return $this->trans($key, [], $catalog);
    }
}
