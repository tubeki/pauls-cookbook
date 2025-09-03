<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Step;
use App\Entity\Comment;
use App\Entity\Rating;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // --- Users ---
        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));

        $user = (new User())
            ->setEmail('user@example.com')
            ->setFirstName('Regular')
            ->setLastName('User');
        $user->setPassword($this->hasher->hashPassword($user, 'test'));

        $manager->persist($admin);
        $manager->persist($user);

        // --- Helper to create a recipe with 4 ingredients & 4 steps ---
        $makeRecipe = function (string $title, string $desc) use ($manager): Recipe {
            $recipe = (new Recipe())
                ->setTitle($title)
                ->setDescription($desc);

            // Ingredients (4)
            for ($i = 1; $i <= 4; $i++) {
                $ing = (new Ingredient())
                    ->setName("Ingredient $i for $title")
                    ->setRecipe($recipe);
                $recipe->addIngredient($ing);
                $manager->persist($ing);
            }

            // Steps (4)
            for ($i = 1; $i <= 4; $i++) {
                $step = (new Step())
                    ->setPosition($i)
                    ->setInstruction("Step $i instruction for $title")
                    ->setRecipe($recipe);
                $recipe->addStep($step);
                $manager->persist($step);
            }

            $manager->persist($recipe);
            return $recipe;
        };

        // --- 3 recipes ---
        $r1 = $makeRecipe('Pasta Aglio e Olio', 'Simple garlic & oil pasta.');
        $r2 = $makeRecipe('Tomato Soup', 'Comforting tomato soup.');
        $r3 = $makeRecipe('Pancakes', 'Fluffy breakfast pancakes.');

        // --- Comments & ratings on 2 recipes (r1, r2) ---
        $comment = function (Recipe $r, User $author, string $body) use ($manager) {
            $c = (new Comment())
                ->setBody($body)
                ->setRecipe($r)
                ->setAuthor($author);
            $manager->persist($c);
        };
        $rate = function (Recipe $r, User $who, int $score) use ($manager) {
            $ra = (new Rating())
                ->setScore($score)
                ->setRecipe($r)
                ->setUser($who);
            $manager->persist($ra);
        };

        // r1: 2 comments, 2 ratings (one from each user)
        $comment($r1, $admin, 'Great and quick!');
        $comment($r1, $user, 'Loved the simplicity.');
        $rate($r1, $admin, 5);
        $rate($r1, $user, 4);

        // r2: 2 comments, 2 ratings (one from each user)
        $comment($r2, $admin, 'Nice weeknight soup.');
        $comment($r2, $user, 'Perfect with grilled cheese.');
        $rate($r2, $admin, 4);
        $rate($r2, $user, 5);

        $manager->flush();
    }
}
