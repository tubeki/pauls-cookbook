<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function getAverageForRecipeId(int $recipeId): float
    {
        return (float) $this->createQueryBuilder('rt')
            ->select('COALESCE(AVG(rt.score), 0)')
            ->andWhere('rt.recipe = :rid')->setParameter('rid', $recipeId)
            ->getQuery()->getSingleScalarResult();
    }
}
