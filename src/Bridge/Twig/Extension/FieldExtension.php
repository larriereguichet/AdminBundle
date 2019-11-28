<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\View\ViewInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    /**
     * @var FieldRendererInterface
     */
    private $renderer;

    public function __construct(FieldRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_field', [$this, 'renderField']),
            new TwigFunction('admin_field_header', [$this, 'renderFieldHeader']),
        ];
    }

    public function renderField(FieldInterface $field, $entity): string
    {
        return $this->renderer->render($field, $entity);
    }

    /**
     * Return the field header label.
     *
     * @return string
     */
    public function renderFieldHeader(ViewInterface $admin, FieldInterface $field)
    {
        return $this->renderer->renderHeader($admin, $field);
    }
}
