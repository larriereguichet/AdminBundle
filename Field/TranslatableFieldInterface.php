<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\Translation\TranslatorInterface;

interface TranslatableFieldInterface
{
    /**
     * Defines translator.
     *
     * @param TranslatorInterface $translator
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator);
}
