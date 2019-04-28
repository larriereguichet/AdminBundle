<?php

namespace LAG\AdminBundle\Bridge\Twig\Extension;

use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Field\Render\FieldRendererInterface;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig_SimpleFunction;

class FieldExtension extends TwigExtension
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
            new Twig_SimpleFunction('admin_field', [$this, 'renderField']),
            new Twig_SimpleFunction('admin_field_header', [$this, 'renderFieldHeader']),
        ];
    }

    public function renderField(FieldInterface $field, $entity): string
    {
        return $this->renderer->render($field, $entity);
    }

    /**
     * Return the field header label.
     *
     * @param ViewInterface  $admin
     * @param FieldInterface $field
     *
     * @return string
     */
    public function renderFieldHeader(ViewInterface $admin, FieldInterface $field)
    {
        return $this->renderer->renderHeader($admin, $field);
    }
}
