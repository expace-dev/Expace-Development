<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Devis>
 *
 * @method Devis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Devis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Devis[]    findAll()
 * @method Devis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Devis::class);
    }

    public function save(Devis $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Devis $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDevis($page, $limit = 15) {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\Devis', 'u')
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

    public function findDevisClient($page, $limit = 15, $user = '') {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\Devis', 'u')
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


//    /**
//     * @return Devis[] Returns an array of Devis objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Devis
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
