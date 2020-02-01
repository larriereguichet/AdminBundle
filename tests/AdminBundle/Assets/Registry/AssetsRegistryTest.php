<?php

namespace LAG\AdminBundle\Tests\Assets\Registry;

use LAG\AdminBundle\Assets\Registry\ScriptRegistry;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\AdminTestBase;
use Twig\Environment;

class AssetsRegistryTest extends AdminTestBase
{
    public function testRegistry()
    {
        $environment = $this->createMock(Environment::class);
        $registry = new ScriptRegistry($environment);

        $this->assertExceptionRaised(Exception::class, function () use ($registry) {
            $registry->unregister('footer', 'test.js');
        });

        $this->assertFalse($registry->hasLocation('head'));
        $this->assertFalse($registry->hasScript('head', 'script.js'));

        $registry->register('footer', 'test.js', null, [
            'my_context' => true,
        ]);
        $this->assertTrue($registry->hasLocation('footer'));
        $this->assertTrue($registry->hasScript('footer', 'test.js'));

        $registry->register('head', 'script.js');
        $this->assertTrue($registry->hasLocation('head'));
        $this->assertTrue($registry->hasScript('head', 'script.js'));

        $registry->unregister('head', 'script.js');
        $this->assertTrue($registry->hasLocation('head'));
        $this->assertFalse($registry->hasScript('head', 'script.js'));

        $environment
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with(ScriptRegistry::DEFAULT_TEMPLATE, [
                'my_context' => true,
                'script' => 'test.js'
            ])
            ->willReturn('script value')
        ;
        $content = $registry->dump('footer');

        $this->assertEquals('script value', $content);
    }
}
