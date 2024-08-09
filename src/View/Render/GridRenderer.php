<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Operation;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

readonly class GridRenderer implements GridRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(GridView $grid, Operation $operation, array $options = []): string
    {
        $resolver = new OptionsResolver();

        if ($grid->type === 'card') {
            $resolver->define('columns')
                ->required()
                ->allowedTypes('integer')
                ->default(3)

                ->define('thumbnail')
                ->required()
                ->allowedTypes('string', 'null')
                ->default(null)
            ;
        }
        $grid->options = $resolver->resolve(array_merge_recursive($grid->options, $options));

        return $this->environment->render($grid->template, [
            'grid' => $grid,
            'operation' => $operation,
            'resource' => $operation->getResource(),
        ]);
    }
}
