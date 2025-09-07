<?php
// src/Controller/RecipeController.php
namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    public function __construct(
        private RecipeRepository $recipes,
    ) {}

    /**
     * List all the recipes and the summaries
     *
     * @return JsonResponse
     */
    #[Route('', name: 'api_recipes_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $recipes = $this->recipes->findAll();

        // compact list
        return $this->json(
            $recipes,
            200,
            [],
            ['groups' => ['recipe:list']]
        );
    }

    /**
     * One recipe with full details
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $recipe = $this->recipes->find($id);

        return $this->json(
            $recipe,
            200,
            [],
            ['groups' => ['recipe:detail']]
        );
    }
}