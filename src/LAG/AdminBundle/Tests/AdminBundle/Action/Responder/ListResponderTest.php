<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Responder\ListResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminBundle\Admin\AdminTest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

class ListResponderTest extends AdminTest
{
    public function testRespondWithSave()
    {
        $routing = $this->getMockWithoutConstructor(RouterInterface::class);
       
        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        
        $form = $this->getMockWithoutConstructor(FormInterface::class);
    
        $filterForm = $this->getMockWithoutConstructor(FormInterface::class);
        $filterForm
            ->expects($this->atLeastOnce())
            ->method('createView')
        ;
        
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
        
        $responder = new ListResponder($routing, $twig);
        
        $response = $responder->respond(
            $configuration,
            $admin,
            $form,
            $filterForm
        );
        
        $this->assertInstanceOf(Response::class, $response);
    }
}
