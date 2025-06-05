<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllWithLatestNews(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.news', 'n')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

?>