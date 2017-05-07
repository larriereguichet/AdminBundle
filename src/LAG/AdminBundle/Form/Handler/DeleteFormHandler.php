<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DeleteFormHandler implements FormHandlerInterface
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
                    ->generate($admin->generateRouteName('list'));
                
                return new RedirectResponse($url);
            }
            return new RedirectResponse('/');
        }
        $url = $this
            ->router
            ->generate($admin->generateRouteName('delete'))
        ;
        
        return new RedirectResponse($url);
    }
}
