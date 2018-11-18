<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class StringField extends AbstractField implements TranslatorAwareFieldInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver->setDefaults([
            'length' => $actionConfiguration->getParameter('string_length'),
            'replace' => $actionConfiguration->getParameter('string_length_truncate'),
            'translation' => true,
        ]);
    }

    public function isSortable(): bool
    {
        return true;
    }

    public function render($value = null): string
    {
        if ($this->options['translation']) {
            $value = $this
                ->translator
                ->trans($value)
            ;
        }
        $maximumStringLength = $this->options['length'];
        $replaceString = $this->options['replace'];

        // Truncate string if required
        if ($maximumStringLength && mb_strlen($value) > $maximumStringLength) {
            $value = mb_substr($value, 0, $maximumStringLength).$replaceString;
        }
        // #69 : strip tags to avoid to break the layout when content contains html
        $value = strip_tags($value);

        return $value;
    }

    /**
     * Defines the translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
