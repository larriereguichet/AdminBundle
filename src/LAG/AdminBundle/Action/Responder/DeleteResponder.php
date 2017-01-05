<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class DeleteResponder implements ResponderInterface
{
    use ResponderTrait;
    
    /**
     * @var RouterInterface
     */
    private $router;
    
    /**
     * CreateResponder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    /**
     * @param ActionConfiguration $configuration
     * @param AdminInterface      $admin
     * @param FormInterface       $form
     *
     * @return Response
     */
    public function respond(
        ActionConfiguration $configuration,
        AdminInterface $admin,
        FormInterface $form
    ) {
        $template = $configuration->getParameter('template');
        
        if ($form->isSubmitted() && $form->isValid()) {
            $url = $this
                ->router
                ->generate($admin->generateRouteName('list'))
            ;

            return new RedirectResponse($url);
        }
        
        return $this->render($template, [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }
}
