<?php

declare(strict_types=1);

use LAG\AdminBundle\Resource\Metadata\Grid;

return static function (): iterable {
    yield new Grid(
        name: 'my_grid',
    );
};
