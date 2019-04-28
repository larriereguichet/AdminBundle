<?php

namespace LAG\AdminBundle\Field;

use Symfony\Contracts\Translation\TranslatorInterface;

interface TranslatorAwareFieldInterface extends FieldInterface
{
    /**
     * Defines the translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator);
}
