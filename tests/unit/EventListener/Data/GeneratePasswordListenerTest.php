<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\EventListener\Data\GeneratePasswordListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Tests\Unit\DataProviderTestTrait;
use LAG\AdminBundle\Tests\Unit\Fixtures\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class GeneratePasswordListenerTest extends TestCase
{
    use DataProviderTestTrait;

    private GeneratePasswordListener $listener;
    private MockObject $passwordHasher;

    #[Test]
    #[DataProvider('operations')]
    public function itHashesPassword(OperationInterface $operation): void
    {
        $data = new User();
        $data->setPlainPassword('Some password');
        $data->setPassword('Old password');

        $event = new DataEvent($data, $operation);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($data, 'Some password')
            ->willReturn('some_hashed_password')
        ;

        $this->listener->__invoke($event);

        $this->assertEquals('some_hashed_password', $data->getPassword());
    }

    #[Test]
    #[DataProvider('operations')]
    public function itDoesNotHashPasswordWithoutPlainPassword(OperationInterface $operation): void
    {
        $data = new User();
        $data->setPassword('Old password');

        $event = new DataEvent($data, $operation);

        $this->passwordHasher
            ->expects($this->never())
            ->method('hashPassword')
        ;

        $this->listener->__invoke($event);

        $this->assertEquals('Old password', $data->getPassword());
    }

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->listener = new GeneratePasswordListener($this->passwordHasher);
    }
}
