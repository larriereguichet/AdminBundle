<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Routing\UrlGenerator;

use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParametersMapperTest extends TestCase
{
    private ParametersMapper $parametersMapper;

    #[Test]
    #[DataProvider('parameters')]
    public function itMapParameters(mixed $data, array $routeParameters, array $expectedParameters): void
    {
        $parameters = $this->parametersMapper->map($data, $routeParameters);

        self::assertEquals($expectedParameters, $parameters);
    }

    public static function parameters(): iterable
    {
        $stdClass = new \stdClass();

        yield [$stdClass, [], []];
        yield [null, ['my_param' => 'my_value'], []];
        yield [['my_param' => 'my_value'], ['my_param' => null], ['my_param' => 'my_value']];
    }

    protected function setUp(): void
    {
        $this->parametersMapper = new ParametersMapper();
    }
}
