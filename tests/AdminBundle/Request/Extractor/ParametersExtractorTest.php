<?php

namespace LAG\AdminBundle\Tests\Request\Extractor;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Request\Extractor\ParametersExtractor;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ParametersExtractorTest extends TestCase
{
    private ParametersExtractor $extractor;

    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(Request $request, bool $supports): void
    {
        $this->assertEquals($supports, $this->extractor->supports($request));
    }

    public function supportsDataProvider(): array
    {
        return [
            [new Request([], [], ['_route_params' => [
                '_admin' => 'my_admin,',
                '_action' => 'my_action,',
            ]]), true],
            [new Request([], [], ['_route_params' => [
                '_admin' => 'my_admin,',
            ]]), false],
            [new Request([], [], ['_route_params' => [
                '_action' => 'my_action,',
            ]]), false],
            [new Request([], [], ['_route_params' => [
                '_admin' => null,
                '_action' => 'my_action,',
            ]]), false],
            [new Request([], [], ['_route_params' => null]), false],
        ];
    }

    public function testGetAdminName(): void
    {
        $adminName = $this->extractor->getAdminName(new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_admin',
                '_action' => 'my_action',
            ],
        ]));

        $this->assertEquals('my_admin', $adminName);
    }

    public function testGetAdminNameWithException(): void
    {
        $this->expectException(Exception::class);
        $this->extractor->getAdminName(new Request([], [], [
            '_route_params' => [],
        ]));
    }

    public function testGetActionName(): void
    {
        $actionName = $this->extractor->getActionName(new Request([], [], [
            '_route_params' => [
                '_admin' => 'my_admin',
                '_action' => 'my_action',
            ],
        ]));

        $this->assertEquals('my_action', $actionName);
    }

    public function testGetActionNameWithException(): void
    {
        $this->expectException(Exception::class);
        $this->extractor->getActionName(new Request([], [], [
            '_route_params' => [],
        ]));
    }

    protected function setUp(): void
    {
        $this->extractor = new ParametersExtractor();
    }
}
