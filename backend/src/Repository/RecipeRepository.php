<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /** @return array<int, array{id:int,name:string,averageRating:float,commentsCount:int}> */
    public function findSummaries(): array
    {
        $rows = $this->createQueryBuilder('r')
            ->select('r AS recipe')
            ->addSelect('COALESCE(AVG(rt.score), 0) AS avgRating')   // <- adjust rating field
            ->addSelect('COUNT(DISTINCT c.id) AS commentsCount')
            ->leftJoin('r.ratings', 'rt')
            ->leftJoin('r.comments', 'c')
            ->groupBy('r.id')
            ->orderBy('avgRating', 'DESC')
            ->getQuery()->getResult();

        return array_map(function ($row) {
            /** @var Recipe $recipe */
            $recipe = $row['recipe'];
            return [
                'id'            => $recipe->getId(),
                'name'          => $recipe->getTitle(),
                'averageRating' => (float) $row['avgRating'],
                'commentsCount' => (int) $row['commentsCount'],
            ];
        }, $rows);
    }

    /** @return Recipe|null */
    public function findOneWithRelations(int $id): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.ingredients', 'i')->addSelect('i')
            ->leftJoin('r.steps', 's')->addSelect('s')
            ->leftJoin('r.comments', 'c')->addSelect('c')
            ->leftJoin('r.ratings', 'rt')->addSelect('rt')
            ->leftJoin('r.author', 'u')->addSelect('u') // <- adjust relation if different
            ->where('r.id = :id')->setParameter('id', $id)
            ->addOrderBy('s.position', 'ASC')           // <- or s.id
            ->getQuery()->getOneOrNullResult();
    }
}
