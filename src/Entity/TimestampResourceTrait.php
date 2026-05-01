<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TimestampResourceTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?\DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
