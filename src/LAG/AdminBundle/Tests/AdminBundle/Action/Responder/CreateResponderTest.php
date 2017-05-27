<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Responder;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Responder\CreateResponder;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Test\TestBundle\Entity\TestEntity;
use Twig_Environment;

class CreateResponderTest extends AdminTestBase
{
    public function testRespondWithSave()
    {
        $routing = $this->getMockWithoutConstructor(RouterInterface::class);
        $routing
            ->expects($this->atLeastOnce())
            ->method('generate')
            ->with('edit_route')
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
            ->with('edit')
            ->willReturn('edit_route')
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getUniqueEntity')
            ->willReturn(new TestEntity())
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
        
        $responder = new CreateResponder($routing, $twig);
        
        $response = $responder->respond(
            $configuration,
            $admin,
            $form,
            'save'
        );
    
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
    
    public function testRespondWithSubmit()
    {
        $routing = $this->getMockWithoutConstructor(RouterInterface::class);
        $routing
            ->expects($this->atLeastOnce())
            ->method('generate')
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
        
        $request = new Request([], [
            'submit' => 'submit_and_save',
        ]);
        
        $responder = new CreateResponder($routing, $twig);
        
        $response = $responder->respond(
            $configuration,
            $admin,
            $form,
            $request
        );
        
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
    
    public function testRespondWithNotSubmittedForm()
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
            ->willReturn(new Response('content'))
        ;
        
        $request = new Request([], [
            'submit' => 'save',
        ]);
        
        $responder = new CreateResponder($routing, $twig);
        
        $response = $responder->respond(
            $configuration,
            $admin,
            $form,
            $request
        );
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertStringEndsWith('content', $response->getContent());
    }
}
