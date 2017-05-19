<?php

namespace LAG\AdminBundle\Action\Responder;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

abstract class AbstractResponder implements ResponderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     * AbstractResponder constructor.
     *
     * @param RouterInterface  $router
     * @param Twig_Environment $twig
     */
    public function __construct(RouterInterface $router, Twig_Environment $twig)
    {
        $this->router = $router;
        $this->twig = $twig;
    }
    
    /**
     * Create a Response with a Twig rendered content.
     *
     * @param string $template
     * @param array  $context
     *
     * @return Response
     */
    protected function render($template, array $context = [])
    {
        $content =  $this
            ->twig
            ->render($template, $context)
        ;
        
        return new Response($content);
    }
}
