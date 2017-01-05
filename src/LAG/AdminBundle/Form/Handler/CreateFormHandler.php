<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * Create a response from the form data.
 */
class CreateFormHandler implements FormHandlerInterface
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
     * EditFormHandler constructor.
     *
     * @param Twig_Environment $twig
     * @param Router $router
     */
    public function __construct(Twig_Environment $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
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
            ->getCurrentAction()
            ->getConfiguration()
            ->getParameter('template')
        ;
        
        if ($form->isValid()) {
            // if the form is valid, we save the entity
            $admin->create();
    
            /** @var ClickableInterface $input */
            $input = $form->get('save-and-redirect');
    
            if ($input->isClicked() && $admin->hasAction('list')) {
                // if the redirect input is clicked and the list action exists, we redirect to the list action
                $url = $this
                    ->router
                    ->generate($admin->generateRouteName('list'));
                
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
}
