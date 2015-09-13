<?php

namespace BlueBear\AdminBundle\Admin\Render;


use BlueBear\AdminBundle\Admin\Configuration\ApplicationConfiguration;

interface RendererInterface
{
    public function __construct(array $options = [], ApplicationConfiguration $configuration);

    public function render($value);
}
