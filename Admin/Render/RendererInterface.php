<?php

namespace BlueBear\AdminBundle\Admin\Render;


interface RendererInterface
{
    public function __construct(array $options = []);

    public function render($value);
}
