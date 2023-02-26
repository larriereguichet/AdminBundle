<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Entity;

interface TimestampableInterface
{
    public function getCreatedAt(): \DateTimeInterface;

    public function setCreatedAt(): self;

    public function getUpdatedAt(): \DateTimeInterface;

    public function setUpdatedAt(): self;
}
