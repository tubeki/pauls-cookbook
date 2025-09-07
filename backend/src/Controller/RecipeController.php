<?php
// src/Controller/RecipeController.php
namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Repository\RatingRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    public function __construct(
        private RecipeRepository $recipes,
        private RatingRepository $ratings,
        private CommentRepository $comments,
    ) {}

    // List: name + avg rating + comments count
    #[Route('', name: 'api_recipes_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json($this->recipes->findSummaries());
    }

    // One recipe with full details
    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $recipe = $this->recipes->findOneWithRelations($id);
        if (!$recipe) {
            return $this->json(['message' => 'Recipe not found'], 404);
        }

        // aggregate stats via dedicated repos
        $averageRating = $this->ratings->getAverageForRecipeId($id);
        $commentsCount = $this->comments->countForRecipeId($id);

        $data = [
            'id'          => $recipe->getId(),
            'name'        => $recipe->getTitle(),
            'description' => method_exists($recipe, 'getDescription') ? $recipe->getDescription() : null,
            'author'      => ($a = method_exists($recipe, 'getAuthor') ? $recipe->getAuthor() : null)
                ? [
                    'id'       => $a->getId(),
                    'email' => method_exists($a, 'getEmail') ? $a->getEmail() : null,
                ] : null,
            'ingredients' => array_map(fn($i) => [
                'id'       => $i->getId(),
                'name'     => method_exists($i, 'getName') ? $i->getName() : null
            ], method_exists($recipe, 'getIngredients') ? $recipe->getIngredients()->toArray() : []),
            'steps'       => array_map(fn($s) => [
                'id'       => $s->getId(),
                'position' => method_exists($s, 'getPosition') ? $s->getPosition() : null,
                'instruction'  => method_exists($s, 'getInstruction') ? $s->getInstruction() : null,
            ], method_exists($recipe, 'getSteps') ? $recipe->getSteps()->toArray() : []),
            'comments'    => array_map(fn($c) => [
                'id'        => $c->getId(),
                'message'   => method_exists($c, 'getBody') ? $c->getBody() : null,
                'createdAt' => method_exists($c, 'getCreatedAt') && $c->getCreatedAt()
                    ? $c->getCreatedAt()->format(DATE_ATOM) : null,
                'author'    => ($u = method_exists($c, 'getAuthor') ? $c->getAuthor() : null)
                    ? ['id' => $u->getId(),
                        'username' => method_exists($u, 'getEmail') ? $a->getEmail() : null]
                    : null,
            ], method_exists($recipe, 'getComments') ? $recipe->getComments()->toArray() : []),
            'ratings'     => array_map(fn($rt) => [
                'id'     => $rt->getId(),
                'score'  => method_exists($rt, 'getScore') ? $rt->getScore() : null,
                'userId' => ($u = method_exists($rt, 'getUser') ? $rt->getUser() : null)? $u->getId(): null,
            ], method_exists($recipe, 'getRatings') ? $recipe->getRatings()->toArray() : []),
            'averageRating' => (float) $averageRating,
            'commentsCount' => (int) $commentsCount,
        ];

        return $this->json($data);
    }
}