<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByNews(News $news): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.news = :news')
            ->setParameter('news', $news)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

?>