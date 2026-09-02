<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Text;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TextareaType extends AbstractType
{
    /**
     * The Trix toolbar buttons enabled by default, named after their data-trix-attribute or, when they carry
     * none, their data-trix-action. Attachments are deliberately left out: with no upload endpoint wired,
     * Trix inlines the dropped files as data URIs into the stored HTML.
     *
     * @var list<string>
     */
    public const array DEFAULT_TOOLBAR = [
        'bold',
        'italic',
        'strike',
        'href',
        'heading1',
        'quote',
        'code',
        'bullet',
        'number',
        'decreaseNestingLevel',
        'increaseNestingLevel',
        'undo',
        'redo',
    ];

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('toolbar')
            ->allowedTypes('string[]', 'bool')
            ->default(self::DEFAULT_TOOLBAR)
        ;

        // Rich text is stored as HTML, so the submitted markup has to be sanitized before it reaches the entity.
        // Both options come from the form extension shipped with symfony/html-sanitizer, and the sanitizer itself
        // is declared by the bundle extension.
        $resolver->setDefaults([
            'sanitize_html' => true,
            'sanitizer' => 'lag_admin_rich_text',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['toolbar'] = json_encode($options['toolbar']);
    }

    public function getParent(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_textarea';
    }
}
