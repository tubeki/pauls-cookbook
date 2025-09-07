<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingredient>
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    /** @return Ingredient[] */
    public function findByRecipeId(int $recipeId): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.recipe = :rid')->setParameter('rid', $recipeId)
            ->orderBy('i.id', 'ASC')->getQuery()->getResult();
    }
}
