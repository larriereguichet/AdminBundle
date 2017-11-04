<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Routing\RouteNameGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

/**
 * Create a response from the form data.
 */
class EditFormHandler
{
    /**
     * @var Twig_Environment
     */
    private $twig;
    
    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * EditFormHandler constructor.
     *
     * @param Twig_Environment $twig
     * @param RouterInterface  $router
     * @param RequestStack     $requestStack
     */
    public function __construct(Twig_Environment $twig, RouterInterface $router, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }
    
    /**
     * Save the entity if the form is valid, and redirect to the list action if
     * required
     *
     * @param FormInterface $form
     * @param AdminInterface $admin
     *
     * @return RedirectResponse|Response
     */
    public function handle(FormInterface $form, AdminInterface $admin)
    {
        $template = $admin
            ->getView()
            ->getConfiguration()
            ->getParameter('template')
        ;
        
        if ($form->isValid()) {
            // if the form is valid, we save the entity
            $admin->save();
    
            if ($this->shouldRedirect($admin)) {
                $generator = new RouteNameGenerator();
                
                // if the redirect input is clicked and the list action exists, we redirect to the list action
                $url = $this
                    ->router
                    ->generate($generator->generate('list', $admin->getName(), $admin->getConfiguration()));
                
                return new RedirectResponse($url);
            }
        }
        // display the form after validation or not
        $content = $this
            ->twig
            ->render($template, [
                'admin' => $admin,
                'form' => $form->createView()
            ])
        ;
    
        return new Response($content);
    }
    
    /**
     * Return true if the user should be redirected to the list action.
     *
     * @param AdminInterface $admin
     *
     * @return bool
     */
    private function shouldRedirect(AdminInterface $admin)
    {
        $submit = $this
            ->requestStack
            ->getCurrentRequest()
            ->get('submit')
        ;
        
        if ('save-and-redirect' !== $submit) {
            return false;
        }
        $actions = $admin
            ->getConfiguration()
            ->getParameter('actions')
        ;
    
        if (!key_exists('list', $actions)) {
            return false;
        }
        
        return true;
    }
}
