<?php

namespace LAG\AdminBundle\Field\Render;

use LAG\AdminBundle\Field\View\View;
use LAG\AdminBundle\View\ViewInterface;

interface FieldRendererInterface
{
    /**
     * Render a field view using Twig or a the view data for a text View. The data transformer will be called before
     * rendering data.
     *
     * @param      $data
     */
    public function render(View $field, $data): string;

    /**
     * Render a field header using Twig.
     */
    public function renderHeader(ViewInterface $admin, View $field): string;
}
