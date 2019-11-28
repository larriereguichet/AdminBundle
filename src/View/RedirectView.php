<?php

namespace LAG\AdminBundle\View;

class RedirectView extends View
{
    private $url;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }
}
