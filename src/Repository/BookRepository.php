<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Retourne un QueryBuilder filtré — passé au paginateur KNP.
     */
    public function createFilteredQueryBuilder(?string $query, ?int $categoryId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.categories', 'c')
            ->addSelect('a', 'c')
            ->orderBy('b.title', 'ASC');

        if ($query) {
            $qb->andWhere(
                $qb->expr()->orX(
                    'LOWER(b.title) LIKE LOWER(:q)',
                    'LOWER(a.firstName) LIKE LOWER(:q)',
                    'LOWER(a.lastName) LIKE LOWER(:q)',
                    'LOWER(c.name) LIKE LOWER(:q)'
                )
            )->setParameter('q', '%' . $query . '%');
        }

        if ($categoryId) {
            $qb->andWhere('c.id = :cat')->setParameter('cat', $categoryId);
        }

        return $qb;
    }

    public function search(string $query): array
    {
        return $this->createFilteredQueryBuilder($query, null)->getQuery()->getResult();
    }

    public function findByFilters(?string $title, ?int $authorId, ?int $categoryId): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->leftJoin('b.categories', 'c');

        if ($title) {
            $qb->andWhere('LOWER(b.title) LIKE LOWER(:title)')->setParameter('title', '%' . $title . '%');
        }
        if ($authorId) {
            $qb->andWhere('a.id = :author')->setParameter('author', $authorId);
        }
        if ($categoryId) {
            $qb->andWhere('c.id = :category')->setParameter('category', $categoryId);
        }

        return $qb->orderBy('b.title', 'ASC')->getQuery()->getResult();
    }
}
