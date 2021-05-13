<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\TinyMce;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class TinyMceType extends AbstractType
{
    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'attr' => [],
                'tinymce_options' => [],
            ])
            ->setAllowedTypes('tinymce_options', 'array')
            ->setNormalizer('tinymce_options', function (Options $options, $value) {
                $attr = $options->offsetGet('attr');

                if (\array_key_exists('id', $attr)) {
                    $value['selector'] = $attr['id'];
                } else {
                    $value['selector'] = 'textarea#'.uniqid('tinymce');
                }
                // Do not use a nested options resolver to allow the user to define only some options and not the
                // whole set
                return array_merge($this->getTinyMceDefaultConfiguration(), $value);
            })
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['id'] = u($options['tinymce_options']['selector'])
            ->replace('textarea#', '')
            ->replace('#', '')
            ->toString()
        ;
        $view->vars['attr']['data-controller'] = 'tinymce';
        $view->vars['attr']['data-options'] = json_encode($options['tinymce_options']);
    }

    private function getTinyMceDefaultConfiguration(): array
    {
        return [
            'branding' => false,
            'language' => 'fr_FR',
            'selector' => uniqid('tinymce'),
            'toolbar1' => 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter '
                .'alignright alignjustify | bullist numlist outdent indent | link image toolbar2: print preview '
                .'media | forecolor backcolor emoticons code',
            'image_advtab' => true,
            'relative_urls' => false,
            'convert_urls' => false,
            'theme' => 'silver',
            'skin' => 'oxide',
            'imagetools_toolbar' => 'rotateleft rotateright | flipv fliph | editimage imageoptions',
            //'content_css' => $this->packages->getUrl('build/cms.tinymce.content.css'),
            //'content_css' => $this->packages->getUrl('/bundles/jkmedia/assets/media-editor.css'),
            'content_css' => '',
            'body_class' => 'mceForceColors container',
            'browser_spellcheck' => true,
            'plugins' => [
                'advlist',
                'anchor',
                'autolink',
                'charmap',
                'code',
                'colorpicker',
                'emoticons',
                'fullscreen',
                'directionality',
                'hr',
                'image',
                'insertdatetime',
                'imagetools',
                'media',
                'nonbreaking',
                'link',
                'lists',
                'pagebreak',
                'print',
                'paste',
                'preview',
                'save',
                'searchreplace',
                'table',
                'textpattern',
                'template',
                'textcolor',
                'wordcount',
                'visualblocks',
                'visualchars',
            ],
            'height' => 400,
        ];
    }
}
