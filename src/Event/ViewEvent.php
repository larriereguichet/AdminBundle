<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\EventDispatcher\Event;

class ViewEvent extends Event
{
    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * ViewEvent constructor.
     *
     * @param AdminInterface $admin
     */
    public function __construct(AdminInterface $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return ViewInterface
     */
    public function getView(): ViewInterface
    {
        return $this->view;
    }

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }
}
