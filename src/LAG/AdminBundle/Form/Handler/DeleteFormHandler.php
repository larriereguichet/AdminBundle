<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Routing\RouteNameGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteFormHandler
{
    /**
     * @var Router
     */
    private $router;
    
    /**
     * DeleteFormHandler constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * @param FormInterface $form
     * @param AdminInterface $admin
     *
     * @return RedirectResponse
     */
    public function handle(FormInterface $form, AdminInterface $admin)
    {
        $generator = new RouteNameGenerator();
        
        if ($form->isValid()) {
            // remove the current entity
            $admin->remove();
            
            // redirect to list if the action exist
            $allowedActions = $admin
                ->getConfiguration()
                ->getParameter('actions')
            ;
    
            if (array_key_exists('list', $allowedActions)) {
                $url = $this
                    ->router
                    ->generate($generator->generate('list', $admin->getName(), $admin->getConfiguration()))
                ;
                
                return new RedirectResponse($url);
            }
            return new RedirectResponse('/');
        }
        $url = $this
            ->router
            ->generate($generator->generate('delete', $admin->getName(), $admin->getConfiguration()))
        ;
        
        return new RedirectResponse($url);
    }
}
