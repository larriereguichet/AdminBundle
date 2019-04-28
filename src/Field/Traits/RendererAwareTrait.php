<?php

namespace LAG\AdminBundle\Field\Traits;

use LAG\AdminBundle\Field\Render\FieldRendererInterface;

trait RendererAwareTrait
{
    /**
     * @var FieldRendererInterface
     */
    protected $renderer;

    public function setRenderer(FieldRendererInterface $renderer): void
    {
        $this->renderer = $renderer;
    }
}
