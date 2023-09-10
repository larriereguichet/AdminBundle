<?php

namespace LAG\AdminBundle\Bridge\VichUploader\File;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\File\Provider\FileProviderInterface;

class MappingFileProvider implements FileProviderInterface
{

    public function getFiles(string $mappingName = 'lag_admin_images'): Collection
    {

    }
}
