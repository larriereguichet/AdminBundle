<?php

namespace LAG\AdminBundle\Field\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
}
