<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PackagingMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PackagingMaterial>
 *
 * @method PackagingMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackagingMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackagingMaterial[]    findAll()
 * @method PackagingMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackagingMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackagingMaterial::class);
    }

    public function add(PackagingMaterial $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PackagingMaterial $packagingMaterial): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete('App:PackagingMaterial', 'pm')
            ->where('pm.id = :id')
            ->setParameter('id', $packagingMaterial->getId())
            ->getQuery()
            ->execute();
        $this->getEntityManager()->flush();
    }
}
