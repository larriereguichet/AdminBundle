<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\Field\View\FieldView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    private FieldRendererInterface $renderer;

    public function __construct(FieldRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_field', [$this, 'renderField', ['is_safe' => ['html']]]),
            new TwigFunction('admin_field_header', [$this, 'renderFieldHeader', ['is_safe' => ['html']]]),
        ];
    }

    /**
     * Render a field with the value of the given data.
     */
    public function renderField(FieldView $field, object $data): string
    {
        return $this->renderer->render($field, $data);
    }

    /**
     * Return the field header label.
     */
    public function renderFieldHeader(FieldView $field): string
    {
        return $this->renderer->renderHeader($field);
    }
}
