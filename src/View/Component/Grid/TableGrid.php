<?php

namespace LAG\AdminBundle\View\Component\Grid;

use LAG\AdminBundle\Grid\View\Row;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;
use Symfony\UX\TwigComponent\ComponentAttributes;

#[AsTwigComponent(
    name: 'lag_admin:grid:table',
    template: '@LAGAdmin/components/grids/table.html.twig',
    exposePublicProps: true,
)]
class TableGrid
{
    public ComponentAttributes $headerRowAttributes;
    public ComponentAttributes $headerAttributes;
    public iterable $data;
    public iterable $headers;
    public iterable $rows = [];
    public bool $displayHeaders = true;

    #[PreMount]
    public function validate(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver
            ->define('headers')
            ->allowedTypes(Row::class.'[]')
            ->define('rows')
            ->allowedTypes(Row::class.'[]')
        ;

        return $data;
    }

    public function mount(
        Row $headers,
        iterable $rows,
        array $headerRowAttributes = [],
        array $headerAttributes = [],
    ): void {
        dump($headers);
        $this->headerRowAttributes = new ComponentAttributes($headerRowAttributes);
        $this->headerAttributes = new ComponentAttributes($headerAttributes);
        $this->headers = $headers->cells;
        $this->rows = $rows;
    }
}
