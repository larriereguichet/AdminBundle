<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Responder\DeleteResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

class DeleteResponderTest extends AdminTestBase
{
    public function testRespond()
    {
        $routing = $this->getMockWithoutConstructor(RouterInterface::class);
        $routing
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->with('list_route')
            ->willReturn('http://test.fr')
        ;
    
        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['template', 'my_template.twig'],
            ])
        ;
    
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('generateRouteName')
            ->with('list')
            ->willReturn('list_route')
        ;
    
        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->expects($this->atLeastOnce())
            ->method('isValid')
            ->willReturn(true)
        ;
        $form
            ->expects($this->atLeastOnce())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
    
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
    
        $responder = new DeleteResponder($routing, $twig);
    
        $response = $responder->respond(
            $configuration,
            $admin,
            $form
        );
    
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
    
    public function testRespondWithoutSubmittedForm()
    {
        $routing = $this->getMockWithoutConstructor(RouterInterface::class);
        
        $configuration = $this->getMockWithoutConstructor(ActionConfiguration::class);
        $configuration
            ->expects($this->atLeastOnce())
            ->method('getParameter')
            ->willReturnMap([
                ['template', 'my_template.twig'],
            ])
        ;
        
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        
        $form = $this->getMockWithoutConstructor(FormInterface::class);
        $form
            ->expects($this->atLeastOnce())
            ->method('isSubmitted')
            ->willReturn(false)
        ;
        
        $twig = $this->getMockWithoutConstructor(Twig_Environment::class);
        $twig
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with('my_template.twig')
            ->willReturn('content')
        ;
        
        $responder = new DeleteResponder($routing, $twig);
        
        $response = $responder->respond(
            $configuration,
            $admin,
            $form
        );
        
        $this->assertInstanceOf(Response::class, $response);
    }
}
