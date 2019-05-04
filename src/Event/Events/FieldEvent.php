<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Field\FieldInterface;
use Symfony\Component\EventDispatcher\Event;

class FieldEvent extends Event
{
    /**
     * @var string
     */
    private $adminName;

    /**
     * @var string
     */
    private $actionName;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var FieldInterface|null
     */
    private $field;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(
        string $adminName,
        string $actionName,
        string $fieldName,
        string $entityClass,
        string $type = null,
        FieldInterface $field = null
    ) {
        $this->adminName = $adminName;
        $this->actionName = $actionName;
        $this->fieldName = $fieldName;
        $this->type = $type;
        $this->field = $field;
        $this->entityClass = $entityClass;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getAdminName(): string
    {
        return $this->adminName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return FieldInterface|null
     */
    public function getField(): ?FieldInterface
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
