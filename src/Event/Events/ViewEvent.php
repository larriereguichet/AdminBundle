<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Exception\View\MissingViewException;
use LAG\AdminBundle\View\ViewInterface;

class ViewEvent extends AbstractEvent
{
    private ViewInterface $view;

    public function setView(ViewInterface $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): ViewInterface
    {
        if (!isset($this->view)) {
            throw new MissingViewException('The view is not set');
        }

        return $this->view;
    }
}
