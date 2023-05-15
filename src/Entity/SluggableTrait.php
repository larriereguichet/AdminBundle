<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function Symfony\Component\String\u;

trait SluggableTrait
{
    #[ORM\Column(type: 'string')]
    protected ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function generateSlug(string $source): void
    {
        $this->slug = (new AsciiSlugger())->slug(u($source)->lower()->toString())->toString();
    }
}
