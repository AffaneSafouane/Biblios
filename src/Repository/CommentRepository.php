<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Book $book
     * @return QueryBuilder
     */
    public function findByBook(Book $book): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.book = :book')
            ->andWhere('c.status = :status')
            ->setParameter('book', $book)
            ->setParameter('status', "published")
            ->orderBy('c.publishedAt', 'DESC')
        ;
    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
