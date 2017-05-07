<?php

namespace LAG\AdminBundle\Field\Field;

use LAG\AdminBundle\Field\AbstractField;
use LAG\AdminBundle\Field\TwigAwareInterface;
use Twig_Environment;

class Boolean extends AbstractField implements TwigAwareInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;
    
    public function render($value)
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        
        return $this
            ->twig
            ->render('@LAGAdmin/Field/boolean.html.twig', [
                'value' => $value,
            ])
        ;
    }
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getType()
    {
        return AbstractField::TYPE_BOOLEAN;
    }
    
    /**
     * Define twig environment.
     *
     * @param Twig_Environment $twig
     *
     * @return void
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
