<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\Image;

use LAG\AdminBundle\Image\ImageInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ImageType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class);

        // An entry the administrator adds and then leaves alone submits an image carrying neither an
        // uploaded file nor a stored path. Nothing downstream can save that: the uploader skips it for
        // want of a file, and the path column is not nullable, so the save dies at flush time on a
        // database error naming a column the administrator never saw. Failing here instead turns it
        // into an error on the field that is actually missing a value.
        // The message is translated here rather than left to the theme: a FormError added by hand carries
        // no domain, and the form_errors block prints error.message as is, so a bare key would reach the
        // administrator unchanged.
        $translator = $this->translator;

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($translator): void {
            $image = $event->getData();

            if (!$image instanceof ImageInterface || $image->hasFile() || $image->getPath() !== null) {
                return;
            }

            $event->getForm()->get('file')->addError(
                new FormError($translator->trans('lag_admin.image.file_required', [], 'admin')),
            );
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['image_filter'] = $options['image_filter'];
        $view->vars['image_full_filter'] = $options['image_full_filter'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => ImageInterface::class])

            ->define('image_filter')
            ->default('lag_admin_thumbnail')
            ->allowedTypes('string')

            ->define('image_full_filter')
            ->default('lag_admin_full')
            ->allowedTypes('string')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'lag_admin_image';
    }
}
