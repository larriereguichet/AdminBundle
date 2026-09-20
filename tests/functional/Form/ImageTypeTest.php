<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional\Form;

use LAG\AdminBundle\Entity\Image;
use LAG\AdminBundle\Form\Type\Image\ImageType;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * An image row left untouched by the administrator used to reach the database as a null path on a column
 * that does not accept one, which failed the whole save with an error naming the column rather than the
 * field.
 */
final class ImageTypeTest extends KernelTestCase
{
    #[Test]
    public function itRejectsAnImageWithNeitherFileNorPath(): void
    {
        $form = $this->createForm(new Image());
        $form->submit(['file' => null]);

        self::assertFalse($form->isValid());
        self::assertCount(1, $form->get('file')->getErrors());
    }

    #[Test]
    public function itAcceptsAnImageThatAlreadyHasAPath(): void
    {
        $image = new Image();
        $image->setPath('uploads/existing.png');

        $form = $this->createForm($image);
        $form->submit(['file' => null]);

        // Editing a row without replacing its file is the common case and must stay valid.
        self::assertTrue($form->isValid());
    }

    #[Test]
    public function itAcceptsAnImageWithAnUploadedFile(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'lag_admin_image');
        file_put_contents($path, 'content');

        $form = $this->createForm(new Image());
        $form->submit(['file' => new UploadedFile($path, 'image.png', 'image/png', null, true)]);

        self::assertTrue($form->isValid());

        unlink($path);
    }

    private function createForm(Image $image): \Symfony\Component\Form\FormInterface
    {
        self::bootKernel();
        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');

        return $formFactory->create(ImageType::class, $image, ['csrf_protection' => false]);
    }
}
