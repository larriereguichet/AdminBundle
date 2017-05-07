<?php

namespace LAG\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class ListResponder implements ResponderInterface
{
    use ResponderTrait;
    
    /**
     * ListResponder constructor.
     *
     * @param Twig_Environment  $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * @param ActionConfiguration $configuration
     * @param AdminInterface      $admin
     * @param FormInterface       $form
     * @param FormInterface       $filterForm
     *
     * @return Response
     *
     */
    public function respond(
        ActionConfiguration $configuration,
        AdminInterface $admin,
        FormInterface $form,
        FormInterface $filterForm = null
    ) {
        $context = [
            'admin' => $admin,
            'form' => $form->createView(),
        ];
    
        if (null !== $filterForm) {
            $context['filterForm'] = $filterForm->createView();
        }
    
        return $this->render($configuration->getParameter('template'), $context);
    }
}
