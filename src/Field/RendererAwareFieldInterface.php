<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Field\Render\FieldRendererInterface;

interface RendererAwareFieldInterface extends FieldInterface
{
    public function setRenderer(FieldRendererInterface $renderer);
}
