<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Routing\UrlGenerator;

use LAG\AdminBundle\Routing\Mapper\ParametersMapper;
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
        $parameters = $this->parametersMapper->mapObjectToRouteParameters($data, $routeParameters);

        self::assertEquals($expectedParameters, $parameters);
    }

    public static function parameters(): iterable
    {
        $obj = new \stdClass();
        $obj->myProp = 'hello';

        yield [$obj, [], []];
        yield [null, ['my_param' => 'my_value'], []];
        yield [['my_param' => 'my_value'], ['my_param' => null], ['my_param' => 'my_value']];
        yield [$obj, ['myProp' => 'myProp'], ['myProp' => 'hello']];
        yield [$obj, ['myProp'], ['myProp' => 'hello']];
        yield [['key' => 'value'], ['key'], ['key' => 'value']];
    }

    protected function setUp(): void
    {
        $this->parametersMapper = new ParametersMapper();
    }
}
