<?php

namespace LAG\AdminBundle\Translation\Helper;

use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Utils\TranslationUtils;
use LAG\Component\StringUtils\StringUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationHelper implements TranslationHelperInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    public function transWithPattern(AdminConfiguration $configuration, string $id): string
    {
        $key = TranslationUtils::getTranslationKey(
            $configuration->getTranslationPattern(),
            $configuration->getName(),
            StringUtils::underscore($id)
        );

        return $this->trans($key, [], $configuration->getTranslationCatalog());
    }
}
