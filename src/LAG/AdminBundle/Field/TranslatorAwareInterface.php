<?php

namespace LAG\AdminBundle\Field;

use Symfony\Component\Translation\TranslatorInterface;

interface TranslatorAwareInterfaceOLD
{
    /**
     * Defines translator.
     *
     * @param TranslatorInterface $translator
     * @return void
     */
    public function setTranslator(TranslatorInterface $translator);
}
