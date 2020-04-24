<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Event\AbstractEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\FilterInterface;
use Symfony\Component\Form\FormInterface;

class FilterEvent extends AbstractEvent
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var FormInterface[]
     */
    private $forms = [];

    /**
     * @throws Exception
     */
    public function addForm(FormInterface $form, string $identifier): void
    {
        if ($this->hasForm($identifier)) {
            throw new Exception('A form with the identifier "'.$identifier.'" was already added. Use removeForm() before adding the new form');
        }
        $this->forms[$identifier] = $form;
    }

    public function removeForm(string $identifier): void
    {
        if (!$this->hasForm($identifier)) {
            throw new  Exception('The form "'.$identifier.'" does not exists');
        }
        unset($this->forms[$identifier]);
    }

    public function hasForm(string $identifier): bool
    {
        return array_key_exists($identifier, $this->forms);
    }

    /**
     * @return FormInterface[]
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Add a filter and its value.
     */
    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * Remove all added filters.
     */
    public function clearFilters(): void
    {
        $this->filters = [];
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
