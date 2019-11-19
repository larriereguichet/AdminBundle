<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Field\Traits\TranslatorTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringField extends AbstractField implements TranslatorAwareFieldInterface
{
    use TranslatorTrait;

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
        $resolver->setDefaults([
            'length' => $actionConfiguration->get('string_length'),
            'replace' => $actionConfiguration->get('string_length_truncate'),
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
}
