<?php

namespace LAG\AdminBundle\Action\Responder;

use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

trait ResponderTrait
{
    /**
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     * @param       $template
     * @param array $context
     *
     * @return Response
     */
    public function render($template, array $context = [])
    {
        $content =  $this
            ->twig
            ->render($template, $context)
        ;
    
        return new Response($content);
    }
}
