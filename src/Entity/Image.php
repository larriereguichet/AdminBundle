<?php

namespace LAG\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as VichFile;

#[ORM\Entity]
#[ORM\Table(name: 'lag_admin_images')]
#[Vich\Uploadable]
class Image
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private string $uuid;

    #[ORM\Embedded(class: VichFile::class)]
    protected ?VichFile $file = null;

    #[Vich\UploadableField(
        mapping: 'lag_admin_images',
        fileNameProperty: 'file.name',
        size: 'file.size'
    )]
    protected ?File $imageFile = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4()->toRfc4122();
        $this->file = new VichFile();
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    public function __toString(): string
    {
        return $this->file?->getName() ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getFile(): ?VichFile
    {
        return $this->file;
    }

    public function setFile(?VichFile $file): void
    {
        $this->file = $file;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
}
