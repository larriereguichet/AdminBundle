<?php

namespace LAG\AdminBundle\Configuration\Behavior;

use LAG\AdminBundle\Utils\TranslationUtils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait TranslationConfigurationTrait
{
    abstract public function get($name);

    abstract public function getName(): string;

    protected function configureTranslation(OptionsResolver $resolver, string $pattern = 'lag.{admin}.{key}', string $catalog = 'messages')
    {
        $resolver
            ->setDefaults([
                'translation' => function (OptionsResolver $subResolver) use ($pattern, $catalog) {
                    $subResolver
                        ->setDefaults([
                            'enabled' => true,
                            'pattern' => $pattern,
                            'catalog' => $catalog,
                        ])
                    ;
                },
            ])
            ->setAllowedTypes('translation', 'array')
            ->setNormalizer('translation', function (Options $options, $value) {
                if (!array_key_exists('enabled', $value)) {
                    throw new InvalidOptionsException('Admin translation enabled parameter should be defined');
                }

                if (!is_bool($value['enabled'])) {
                    throw new InvalidOptionsException('Admin translation enabled parameter should be a boolean');
                }

                if (!array_key_exists('pattern', $value)) {
                    $value['pattern'] = '{admin}.{key}';
                }

                if ($value['enabled'] && false === strstr($value['pattern'], '{key}')) {
                    throw new InvalidOptionsException('Admin translation pattern should contains the {key} placeholder, given "'.$value['pattern'].'"');
                }

                return $value;
            })
        ;
    }

    public function isTranslationEnabled(): bool
    {
        return true === $this->get('translation')['enabled'];
    }

    public function getTranslationPattern(): string
    {
        return $this->get('translation')['pattern'];
    }

    public function getTranslationCatalog(): string
    {
        return $this->get('translation')['catalog'];
    }

    public function getTranslationKey(string $text): string
    {
        if ($this->isTranslationEnabled()) {
            $text = TranslationUtils::getTranslationKey(
                $this->getTranslationPattern(),
                $this->getName(),
                $text
            );
        } else {
            $text = ucfirst($text);
        }

        return $text;
    }
}
