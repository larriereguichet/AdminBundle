<?php

namespace LAG\AdminBundle\File\Provider;

use Doctrine\Common\Collections\Collection;

interface FileProviderInterface
{
    public function getFiles(string $mappingName = 'lag_admin_images'): Collection;
}
