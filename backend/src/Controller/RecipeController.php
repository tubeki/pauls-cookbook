<?php
// src/Controller/RecipeController.php
namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Step;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\StepRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    public function __construct(
        private RecipeRepository $recipes,
        private IngredientRepository $ingredients,
        private StepRepository $steps,
        private UserRepository $users,
    ) {}

    /**
     * List all the recipes and the summaries
     *
     * @return JsonResponse
     */
    #[Route('', name: 'api_recipes_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $all = $this->recipes->findAll();

            // Always 200 with an array (possibly empty), serialized with recipe:list
            return $this->json($all, Response::HTTP_OK, [], ['groups' => ['recipe:list']]);
        } catch (\Throwable $e) {
            // Unexpected failure → 500 with safe message
            return $this->json(
                ['error' => 'internal_error', 'message' => 'Unable to fetch recipes.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * One recipe with full details
     *
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        // Basic guard: ids are expected to be numeric; adjust if you use UUIDs
        if (!ctype_digit($id)) {
            return $this->json(
                ['error' => 'invalid_id', 'message' => 'Recipe id must be a numeric value.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $recipe = $this->recipes->find((int) $id);

            if (!$recipe) {
                // Not found → 404 with a minimal payload
                return $this->json(
                    ['error' => 'not_found', 'message' => 'Recipe not found.'],
                    Response::HTTP_NOT_FOUND
                );
            }

            // 200 with a single recipe, serialized with recipe:detail
            return $this->json($recipe, Response::HTTP_OK, [], ['groups' => ['recipe:detail']]);
        } catch (\Throwable $e) {
            // Unexpected failure → 500 with safe message
            return $this->json(
                ['error' => 'internal_error', 'message' => 'Unable to fetch this recipe.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('', name: 'api_recipes_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(
                ['error' => 'invalid_json', 'message' => 'Malformed JSON body.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Minimal validation
        $errors = [];
        if (empty($data['title']) || !is_string($data['title'])) {
            $errors['title'] = 'Title is required.';
        }
        if (!empty($errors)) {
            return $this->json(
                ['error' => 'validation_failed', 'details' => $errors],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $recipe = new Recipe();
            $this->applyRecipeData($recipe, $data);

            // TODO: temp assign this to the Regular User
            $user = $this->users->find(6); // HARDCODED FOR NOW

            $recipe->setAuthor($user);

            $this->recipes->save($recipe, true);

            return $this->json($recipe, Response::HTTP_CREATED, [], ['groups' => ['recipe:detail']]);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => 'internal_error', 'message' => 'Unable to create recipe.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'api_recipes_update', methods: ['PUT','PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        if (!ctype_digit($id)) {
            return $this->json(
                ['error' => 'invalid_id', 'message' => 'Recipe id must be numeric.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $recipe = $this->recipes->find((int) $id);
        if (!$recipe) {
            return $this->json(
                ['error' => 'not_found', 'message' => 'Recipe not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(
                ['error' => 'invalid_json', 'message' => 'Malformed JSON body.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // For PUT require title when missing on payload; for PATCH it’s optional
        if ($request->isMethod('PUT') && (empty($data['title']) || !is_string($data['title']))) {
            return $this->json(
                ['error' => 'validation_failed', 'details' => ['title' => 'Title is required for PUT.']],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $this->applyRecipeData($recipe, $data);

            $this->recipes->save($recipe, true);

            return $this->json($recipe, Response::HTTP_OK, [], ['groups' => ['recipe:detail']]);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => 'internal_error', 'message' => 'Unable to update recipe.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Adds the recipe data to from the request to the Recipe entity instance
     * Expected JSON shape:
     * {
     *   "title": "string",
     *   "description": "string|null",
     *   "ingredients": [{"name":"Flour"}, {"name":"Milk"}],
     *   "steps": [{"position":1,"instruction":"Mix"}, {"position":2,"instruction":"Bake"}]
     * }
     */
    private function applyRecipeData(Recipe $recipe, array $data): void
    {
        if (array_key_exists('title', $data) && is_string($data['title'])) {
            $recipe->setTitle(trim($data['title']));
        }
        if (array_key_exists('description', $data)) {
            $desc = $data['description'];
            $recipe->setDescription($desc === null ? null : (string) $desc);
        }

        // Ingredients: if provided, replace the collection
        if (array_key_exists('ingredients', $data)) {
            $this->resetIngredients($recipe, $data['ingredients'] ?? []);
        }

        // Steps: if provided, replace the collection
        if (array_key_exists('steps', $data)) {
            $this->resetSteps($recipe, $data['steps'] ?? []);
        }
    }

    /**
     * Removes all the Ingredients associated to the Recipe entity and recreates them based on the data from the request
     * If the "ingredients" key is not present in the data it does not update anything
     *
     * @param Recipe $recipe
     * @param array $items
     * @return void
     */
    private function resetIngredients(Recipe $recipe, array $items): void
    {
        // remove existing
        foreach ($recipe->getIngredients() as $existing) {
            $recipe->removeIngredient($existing);
        }

        // add new
        foreach ($items as $row) {
            if (!is_array($row) || empty($row['name'])) {
                // skip invalid item
                continue;
            }

            $ingredient = new Ingredient();

            $ingredient->setName(trim((string) $row['name']));

            $recipe->addIngredient($ingredient);

            $this->ingredients->save($ingredient);
        }
    }

    /**
     * Removes all the Steps associated to the Recipe entity and recreates them based on the data from the request
     * If the "ingredients" key is not present in the data it does not update anything
     *
     * @param Recipe $recipe
     * @param array $items
     * @return void
     */
    private function resetSteps(Recipe $recipe, array $items): void
    {
        foreach ($recipe->getSteps() as $existing) {
            $recipe->removeStep($existing);
        }

        // Normalize and sort by position if given
        $normalized = [];
        foreach ($items as $row) {
            if (!is_array($row) || empty($row['instruction'])) {
                continue;
            }
            $normalized[] = [
                'position' => isset($row['position']) && is_numeric($row['position']) ? (int) $row['position'] : null,
                'instruction' => trim((string) $row['instruction']),
            ];
        }

        usort($normalized, static function ($a, $b) {
            return ($a['position'] ?? PHP_INT_MAX) <=> ($b['position'] ?? PHP_INT_MAX);
        });

        $auto = 1;
        foreach ($normalized as $row) {
            $step = new Step();

            $step->setInstruction($row['instruction']);
            $step->setPosition($row['position'] ?? $auto++);

            $recipe->addStep($step);

            $this->steps->save($step);
        }
    }
}