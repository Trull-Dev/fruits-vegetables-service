<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param Item $item
     * @return void
     */
    public function save(Item $item): void
    {
        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Item $item
     * @return void
     */
    public function remove(Item $item): void
    {
        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $filters
     * @return array
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('i');

        if (isset($filters['name'])) {
            $qb->andWhere('i.name = :name')
                ->setParameter('name', $filters['name']);
        }

        if (isset($filters['minGrams'])) {
            $qb->andWhere('i.quantityInGrams >= :minGrams')
                ->setParameter('minGrams', $filters['minGrams']);
        }

        if (isset($filters['maxGrams'])) {
            $qb->andWhere('i.quantityInGrams <= :maxGrams')
                ->setParameter('maxGrams', $filters['maxGrams']);
        }

        return $qb->getQuery()->getResult();
    }

}
