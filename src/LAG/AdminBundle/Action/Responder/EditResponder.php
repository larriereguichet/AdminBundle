<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditResponder extends AbstractResponder
{
    /**
     * @param ActionConfiguration $configuration
     * @param AdminInterface      $admin
     * @param FormInterface       $form
     * @param Request             $request
     *
     * @return Response
     */
    public function respond(
        ActionConfiguration $configuration,
        AdminInterface $admin,
        FormInterface $form,
        Request $request
    ) {
        $template = $configuration->getParameter('template');
        
        // if the form is submitted and validated, the user should be redirected
        if ($form->isSubmitted() && $form->isValid()) {
            $submitButton = $request
                ->request
                ->get('submit')
            ;
            
            // if the save button is pressed, the user will stay on the edit view
            if ('save' === $submitButton) {
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
