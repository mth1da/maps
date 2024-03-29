<?php

namespace App\Repository;

use App\Entity\Publication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 *
 * @method Publication|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publication|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publication[]    findAll()
 * @method Publication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    public function save(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Publication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(Publication $publication, bool $flush = false): void
    {
        $this->getEntityManager()->persist($publication);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findAllByDescendingOrder(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPublicationsByUserIdDescendingOrder(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'u')
            ->join('p.publi_user', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $userId)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPublicationById(int $idPubli)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :idPubli')
            ->setParameter('idPubli', $idPubli)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
