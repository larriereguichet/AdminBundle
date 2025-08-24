<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\View\Helper;

use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\Link;
use LAG\AdminBundle\View\Helper\RenderHelper;
use LAG\AdminBundle\View\Render\ActionRendererInterface;
use LAG\AdminBundle\View\Render\LinkRendererInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RenderHelperTest extends TestCase
{
    private RenderHelper $helper;
    private MockObject $linkRenderer;
    private MockObject $actionRenderer;

    #[Test]
    public function itRendersALink(): void
    {
        $link = new Link();
        $data = new \stdClass();

        $this->linkRenderer
            ->expects($this->once())
            ->method('render')
            ->with($link, $data, ['some_option' => 'some_value'])
            ->willReturn('<p>content</p>')
        ;

        $render = $this->helper->renderLink($link, $data, ['some_option' => 'some_value']);

        self::assertEquals('<p>content</p>', $render);
    }

    #[Test]
    public function itRendersAnAction(): void
    {
        $link = new Action();
        $data = new \stdClass();

        $this->actionRenderer
            ->expects($this->once())
            ->method('renderAction')
            ->with($link, $data)
            ->willReturn('<p>content</p>')
        ;

        $render = $this->helper->renderAction($link, $data);

        self::assertEquals('<p>content</p>', $render);
    }

    protected function setUp(): void
    {
        $this->linkRenderer = $this->createMock(LinkRendererInterface::class);
        $this->actionRenderer = $this->createMock(ActionRendererInterface::class);
        $this->helper = new RenderHelper(
            $this->linkRenderer,
            $this->actionRenderer,
        );
    }
}
