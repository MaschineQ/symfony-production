<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Expedition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expedition>
 *
 * @method Expedition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expedition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expedition[]    findAll()
 * @method Expedition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpeditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expedition::class);
    }

    public function save(Expedition $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Expedition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
