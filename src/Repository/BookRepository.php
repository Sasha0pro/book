<?php

namespace App\Repository;

use App\Entity\Book;
use App\Utils\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findBookTwoAuthorAndN(int $page): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->where("b.title like '%н%'")
            ->groupBy('b')
            ->join('b.users','u')
            ->addGroupBy('u.id')
            ->having('count(u) > 1')
            ->orderBy('b.title', 'ASC');

        return (new Paginator($qb))->pagination($page);
    }

    public function getList(int $page): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC');

        return (new Paginator($qb))->pagination($page);
    }

    public function getByUser(int $page, $user): Paginator
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.users','u')
            ->where('u = :u')
            ->setParameter('u', $user)
            ->groupBy('b');

        return (new Paginator($qb))->pagination($page);
    }


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
