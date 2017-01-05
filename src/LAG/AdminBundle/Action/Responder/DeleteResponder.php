<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Routing\RouteNameGenerator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteResponder extends AbstractResponder
{
    /**
     * Display the delete form (to ensure not deleting entities just with an url call) and redirect to the list Action
     * when the form is submitted.
     *
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
            $generator = new RouteNameGenerator();
    
            $url = $this
                ->router
                ->generate($generator->generate('list', $admin->getName(), $admin->getConfiguration()))
            ;

            return new RedirectResponse($url);
        }
        
        return $this->render($template, [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }
}
