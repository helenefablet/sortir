<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */

    public function findByParticipants($id)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p')
            ->join('s.participants','p' )
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByParticipantsNotIn($id)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p')
            ->join('s.participants','p' )
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByDateEntre($value1, $value2)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateLimiteInscription BETWEEN :val1 AND :val2')
            ->setParameter('val1', $value1->format('D-M-Y'))
            ->setParameter('val2', $value2->format('D-M-Y'))
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
