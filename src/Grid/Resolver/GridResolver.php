<?php

namespace LAG\AdminBundle\Grid\Resolver;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Resolver\ClassResolverInterface;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolverInterface;
use Symfony\Component\Finder\Finder;

final readonly class GridResolver implements GridResolverInterface
{
    public function __construct(
        private ClassResolverInterface $classResolver,
        private PhpFileResolverInterface $fileResolver,
    ) {
    }

    public function resolveGrids(array $directories): iterable
    {
        foreach ($directories as $directory) {
            $finder = new Finder();
            $finder->files()
                ->in($directory)
                ->name('*.php')
                ->sortByName(true)
            ;

            foreach ($finder as $fileInfo) {
                if (!$fileInfo->isReadable()) {
                    continue;
                }
                if ($fileInfo->isFile()) {
                    $class = $this->classResolver->resolveClass($fileInfo->getRealPath());

                    if ($class !== null) {
                        $attributes = $class->getAttributes(Grid::class);

                        foreach ($attributes as $attribute) {
                            yield $attribute->newInstance();
                        }

                        continue;
                    }
                    $grids = $this->fileResolver->resolveFile($fileInfo->getRealPath());

                    if (is_iterable($grids)) {
                        foreach ($grids as $grid) {
                            if (!$grid instanceof Grid) {
                                throw new Exception(sprintf(
                                    'The file "%s" should return an iterable of "%s", got "%s"',
                                    $fileInfo->getRealPath(),
                                    Grid::class,
                                    get_debug_type($grid),
                                ));
                            }

                            yield $grid;
                        }
                    }
                }
            }
        }
    }
}
