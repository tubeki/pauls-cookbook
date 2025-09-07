<?php

namespace App\Repository;

use App\Entity\Step;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Step>
 */
class StepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }

    /** @return Step[] */
    public function findOrderedByRecipeId(int $recipeId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.recipe = :rid')->setParameter('rid', $recipeId)
            ->orderBy('s.position', 'ASC') // <- or s.id
            ->getQuery()->getResult();
    }
}
