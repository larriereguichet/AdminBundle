<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

interface TimestampedResourceInterface
{
    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt): self;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $createdAt): self;
}
