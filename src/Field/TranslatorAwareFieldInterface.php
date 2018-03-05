<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\Translation\TranslatorInterface;

interface TranslatorAwareFieldInterface
{
    /**
     * Defines the translator.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator);
}
