<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ExpeditionItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpeditionItem>
 *
 * @method ExpeditionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpeditionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpeditionItem[]    findAll()
 * @method ExpeditionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpeditionItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpeditionItem::class);
    }

    public function save(ExpeditionItem $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExpeditionItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
