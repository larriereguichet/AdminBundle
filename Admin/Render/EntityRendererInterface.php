<?php

namespace BlueBear\AdminBundle\Admin\Render;


interface EntityRendererInterface extends RendererInterface
{
    public function setEntity($entity);
}
