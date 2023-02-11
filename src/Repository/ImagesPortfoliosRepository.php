<?php

namespace App\Repository;

use App\Entity\ImagesPortfolios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImagesPortfolios>
 *
 * @method ImagesPortfolios|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagesPortfolios|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagesPortfolios[]    findAll()
 * @method ImagesPortfolios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagesPortfoliosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagesPortfolios::class);
    }

    public function save(ImagesPortfolios $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImagesPortfolios $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImagesPortfolios[] Returns an array of ImagesPortfolios objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ImagesPortfolios
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
