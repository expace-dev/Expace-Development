<?php

namespace App\Repository;

use App\Entity\Factures;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Factures>
 *
 * @method Factures|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factures|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factures[]    findAll()
 * @method Factures[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factures::class);
    }

    public function save(Factures $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Factures $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findFactures($page, $limit = 15) {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\Factures', 'u')
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();
        
        
        if (empty($data)) {
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;
        //dd($data);

        return $result;

    }

    public function findFacturesClient($page, $limit = 15, $user = '') {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\Factures', 'u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $user)
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();
        
        
        if (empty($data)) {
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;
        //dd($data);

        return $result;

    }

    public function amountFacturesMensuel($year, $month) {

        return $this->createQueryBuilder('p')
                    ->select('SUM(p.amount)')
                    ->Where('YEAR(p.createdAt) = :year')
                    ->setParameter('year', $year)
                    ->andWhere('MONTH(p.createdAt) = :month')
                    ->setParameter('month', $month)
                    ->getQuery()
                    ->getSingleScalarResult(); 
    
    }

    public function amountFacturesMensuelClient($year, $month, $user) {

        return $this->createQueryBuilder('p')
                    ->select('SUM(p.amount)')
                    ->Where('YEAR(p.createdAt) = :year')
                    ->setParameter('year', $year)
                    ->andWhere('MONTH(p.createdAt) = :month')
                    ->setParameter('month', $month)
                    ->andWhere('p.client = :val')
                    ->setParameter('val', $user)
                    ->getQuery()
                    ->getSingleScalarResult(); 
    
    }


//    /**
//     * @return Factures[] Returns an array of Factures objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Factures
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
