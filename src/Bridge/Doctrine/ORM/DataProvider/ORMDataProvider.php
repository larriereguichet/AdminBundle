<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Action\Show;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Metadata\Action;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ORMDataProvider implements DataProviderInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function provide(Admin $admin, Action $action, array $uriVariables = [], array $context = []): mixed
    {
        $manager = $this->registry->getManagerForClass($admin->getDataClass());
        $repository = $manager->getRepository($admin->getDataClass());

        if ($action instanceof Show) {
            $itemPerPage = $context['itemPerPage'] ?? $admin->getItemPerPage();
            $page = $context['page'] ?? 1;

            if ($repository instanceof EntityRepository) {
                $data = $repository->createQueryBuilder('o');

                if ($admin->isPaginated()) {
                    $adapter = new QueryAdapter($data);
                    $pager = new Pagerfanta($adapter);
                    $pager->setMaxPerPage($itemPerPage);
                    $pager->setCurrentPage($page);

                    return $pager;
                }
            }
            $start = ($page - 1) * $itemPerPage;

            $data = $repository->findBy(
                $context['filters'] ?? [],
                $admin->getOrder(),
                $itemPerPage,
                $start,
            );

            return new ArrayCollection($data);
        }

        return $repository->findOneBy($uriVariables);
    }
}
