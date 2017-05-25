<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EditResponder extends AbstractResponder
{
    /**
     * Display the edit form and redirect if required when the form is submitted.
     *
     * @param ActionConfiguration $configuration
     * @param AdminInterface      $admin
     * @param FormInterface       $form
     * @param                     $submitButtonName
     *
     * @return Response|RedirectResponse
     */
    public function respond(
        ActionConfiguration $configuration,
        AdminInterface $admin,
        FormInterface $form,
        $submitButtonName
    ) {
        $template = $configuration->getParameter('template');
        
        // if the form is submitted and validated, the user should be redirected
        if ($form->isSubmitted() && $form->isValid()) {
            // if the save button is pressed, the user will stay on the edit view
            if ('save' === $submitButtonName) {
                $url = $this
                    ->router
                    ->generate($admin->generateRouteName('edit'), [
                        'id' => $admin->getUniqueEntity()->getId(),
                    ])
                ;
                
                return new RedirectResponse($url);
            } else {
                // otherwise the user will be redirected to the list view
                $url = $this
                    ->router
                    ->generate($admin->generateRouteName('list'))
                ;
                
                return new RedirectResponse($url);
            }
        }
        
        return $this->render($template, [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }
}
