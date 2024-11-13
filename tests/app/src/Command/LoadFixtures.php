<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Command;

use LAG\AdminBundle\Tests\Application\Factory\BookFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'fixtures:load')]
final class LoadFixtures extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Load Fixtures');

        BookFactory::createMany(100);

        $io->success('Fixtures loaded');

        return self::SUCCESS;
    }
}