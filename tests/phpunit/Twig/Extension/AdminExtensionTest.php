<?php

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\AdminExtension;
use PHPUnit\Framework\MockObject\MockObject;

class AdminExtensionTest extends TestCase
{
    private AdminExtension $adminExtension;
    private MockObject $configuration;
    private MockObject $security;

    public function testServiceExists(): void
    {
        $this->assertServiceExists(AdminExtension::class);
    }

    public function testGetFunctions(): void
    {
        foreach ($this->adminExtension->getFunctions() as $function) {
            $this->assertContains($function->getName(), [
                'admin_config',
                'admin_action_allowed',
                'admin_media_enabled',
                'admin_is_translation_enabled',
            ]);
            $this->assertTrue(method_exists($this->adminExtension, $function->getCallable()[1]));
        }
    }

    public function testGetParameter(): void
    {
        $this
            ->configuration
            ->expects($this->once())
            ->method('get')
            ->with('my_parameter')
            ->willReturn('my_value')
        ;

        $this->assertEquals('my_value', $this->adminExtension->getApplicationParameter('my_parameter'));
    }

    public function testIsAdminActionAllowed(): void
    {
        $this
            ->security
            ->expects($this->once())
            ->method('isActionAllowed')
            ->with('my_admin', 'my_action')
        ;
        $this->adminExtension->isAdminActionAllowed('my_admin', 'my_action');
    }

    public function testIsMediaBundleEnabled(): void
    {
        $this->assertEquals(true, $this->adminExtension->isMediaBundleEnabled());
    }

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(ApplicationConfiguration::class);
        $this->security = $this->createMock(SecurityHelper::class);
        $this->adminExtension = new AdminExtension(true, $this->configuration, $this->security);
    }
}
