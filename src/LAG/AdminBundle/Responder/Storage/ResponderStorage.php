<?php

namespace LAG\AdminBundle\Responder\Storage;

use LAG\AdminBundle\Responder\ResponderInterface;

class ResponderStorage
{
    private $responders = [];
    
    public function add($serviceId, ResponderInterface $responder)
    {
        $this->responders[$serviceId] = $responder;
    }
    
    public function get($serviceId)
    {
        if (!$this->has($serviceId)) {
            throw new \Exception('Invalid responder service id "'.$serviceId.'"');
        }
    
        return $this->responders[$serviceId];
    }
    
    public function has($serviceId)
    {
        return key_exists($serviceId, $this->responders);
    }
}
