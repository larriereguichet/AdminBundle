<?php

namespace LAG\AdminBundle\Grid\Registry;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use LAG\AdminBundle\Exception\Grid\InvalidGridConfigurationException;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Grid\GridInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GridRegistry implements GridRegistryInterface
{
    /** @var GridInterface[] */
    private array $grids = [];

    public function __construct(
        private ValidatorInterface $validator,
        array $gridsConfiguration = [],
    ) {
        $this->initialize($gridsConfiguration);
    }

    public function get(string $name): GridInterface
    {
        return $this->grids[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->grids);
    }

    public function all(): iterable
    {
        return $this->grids;
    }

    private function initialize(array $gridsConfiguration): void
    {
        foreach ($gridsConfiguration as $gridName => $gridConfiguration) {
            $gridConfiguration['name'] = $gridName;
            $grid = (new MapperBuilder())
                ->mapper()
                ->map(Grid::class, Source::array($gridConfiguration ?? [])->camelCaseKeys())
            ;
            $errors = $this->validator->validate($grid, [new Valid()]);

            if ($errors->count() > 0) {
                throw new InvalidGridConfigurationException($gridName, $errors);
            }
            $this->grids[$gridName] = $grid;
        }
    }
}
