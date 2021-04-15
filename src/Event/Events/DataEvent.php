<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Exception\Filter\FilterMissingException;
use LAG\AdminBundle\Filter\FilterInterface;
use Symfony\Component\Form\FormInterface;

class DataEvent extends AbstractEvent
{
    /**
     * @var mixed
     */
    private $data;
    private ?FormInterface $form = null;
    private array $filters = [];
    private array $orderBy = [];

    public function addFilter(FilterInterface $filter): self
    {
        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    public function hasFilter(string $name): bool
    {
        return \array_key_exists($name, $this->filters);
    }

    public function removeFilter(string $name): self
    {
        if (!$this->hasFilter($name)) {
            throw new FilterMissingException('The filter "'.$name.'" does not exists');
        }
        unset($this->filters[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilterForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function removeFilterForm(): self
    {
        $this->form = null;

        return $this;
    }

    public function getFilterForm(): ?FormInterface
    {
        return $this->form;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function addOrderBy(string $field, string $order = 'asc'): self
    {
        $this->orderBy[$field] = $order;

        return $this;
    }

    public function removeOrderBy(string $field): self
    {
        unset($this->orderBy[$field]);

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }
}
