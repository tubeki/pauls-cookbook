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

    // Renamed "Regular User" -> Aoife Kelly
    $aoife = (new User())
        ->setEmail('aoife.kelly@example.com')
        ->setFirstName('Aoife')
        ->setLastName('Kelly');
    $aoife->setPassword($this->hasher->hashPassword($aoife, 'test'));

    // One more regular user
    $liam = (new User())
        ->setEmail('liam.osullivan@example.com')
        ->setFirstName('Liam')
        ->setLastName("O'Sullivan");
    $liam->setPassword($this->hasher->hashPassword($liam, 'test'));

    $manager->persist($admin);
    $manager->persist($aoife);
    $manager->persist($liam);

    // --- Helper: create a recipe with meaningful ingredients & paragraph steps ---
    $makeRecipe = function (string $title, string $desc, User $author, array $ingredients, array $steps) use ($manager): Recipe {
        $recipe = (new Recipe())
            ->setTitle($title)
            ->setDescription($desc)
            ->setAuthor($author);

        // Ingredients
        foreach ($ingredients as $name) {
            $ing = (new Ingredient())
                ->setName($name)
                ->setRecipe($recipe);
            $recipe->addIngredient($ing);
            $manager->persist($ing);
        }

        // Steps (paragraphs)
        $pos = 1;
        foreach ($steps as $paragraph) {
            $step = (new Step())
                ->setPosition($pos++)
                ->setInstruction($paragraph)
                ->setRecipe($recipe);
            $recipe->addStep($step);
            $manager->persist($step);
        }

        $manager->persist($recipe);
        return $recipe;
    };

    // --- 5 recipes ---
    $r1 = $makeRecipe(
        'Pasta Aglio e Olio',
        'Simple garlic & oil pasta with a touch of heat.',
        $admin,
        [
            '200 g spaghetti',
            '60 ml extra-virgin olive oil',
            '4 cloves garlic, thinly sliced',
            '1/2 tsp red pepper flakes',
            '1 tbsp chopped fresh parsley',
            '1/2 tsp fine salt'
        ],
        [
            'Bring a large pot of well-salted water to a rolling boil. Add the spaghetti and cook until just al dente, reserving a cup of the cooking water before draining.',
            'Meanwhile, warm the olive oil over gentle heat in a wide pan. Add the sliced garlic and cook slowly until fragrant and just turning pale gold—do not let it brown. Sprinkle in the chili flakes.',
            'Add a ladle of the starchy pasta water to the pan to stop the cooking, then tumble in the drained pasta. Toss constantly over medium heat until the oil emulsifies and clings to the strands.',
            'Finish with parsley and a touch more salt if needed. Toss again and serve immediately, optionally with lemon zest or grated Parmesan.'
        ]
    );

    $r2 = $makeRecipe(
        'Tomato Soup',
        'Comforting, smooth tomato soup perfect for weeknights.',
        $aoife,
        [
            '1 tbsp olive oil',
            '1 onion, finely chopped',
            '2 cloves garlic, minced',
            '800 g canned whole tomatoes',
            '500 ml vegetable stock',
            '1 tsp sugar',
            '60 ml cream (optional)',
            'Salt and black pepper'
        ],
        [
            'Heat the oil in a pot and soften the onion with a pinch of salt for 8–10 minutes, stirring occasionally, until sweet and translucent. Add the garlic and cook for 30 seconds more.',
            'Tip in the tomatoes and stock. Crush the tomatoes with a spoon and bring to a gentle simmer. Add the sugar to balance acidity and cook for 15–20 minutes.',
            'Blend the soup until completely smooth using a blender or stick blender. Return to the pot and stir in the cream, if using.',
            'Season generously with salt and pepper and warm through. Serve with a drizzle of olive oil and crusty bread or a grilled cheese.'
        ]
    );

    $r3 = $makeRecipe(
        'Pancakes',
        'Fluffy breakfast pancakes that brown beautifully.',
        $aoife,
        [
            '200 g plain flour',
            '2 tbsp caster sugar',
            '2 tsp baking powder',
            'Pinch of salt',
            '300 ml milk',
            '1 large egg',
            '30 g butter, melted (plus extra for the pan)'
        ],
        [
            'Whisk the flour, sugar, baking powder, and salt in a bowl. In a jug, whisk the milk, egg, and melted butter until smooth.',
            'Pour the wet ingredients into the dry and whisk just until combined; a few small lumps are fine. Rest the batter for 5–10 minutes to hydrate.',
            'Heat a lightly buttered non-stick pan over medium heat. Ladle in batter to form small rounds and cook until bubbles form and the edges look set.',
            'Flip and cook the second side until golden. Keep warm on a low oven and serve with butter, maple syrup, and fruit.'
        ]
    );

    $r4 = $makeRecipe(
        'Chicken Tikka Masala',
        'Tender marinated chicken in a creamy, spiced tomato sauce.',
        $liam,
        [
            '600 g chicken thighs, bite-size pieces',
            '150 g plain yogurt',
            '2 tbsp tikka masala paste',
            '1 tbsp lemon juice',
            '2 tbsp ghee or oil',
            '1 onion, finely chopped',
            '2 cloves garlic, minced',
            '2 cm piece ginger, grated',
            '400 g tomato passata',
            '150 ml cream',
            '1 tsp garam masala',
            'Salt'
        ],
        [
            'Combine yogurt, tikka paste, lemon juice, and a pinch of salt; coat the chicken and marinate at least 30 minutes (overnight is best).',
            'Sear the chicken in hot ghee in batches until lightly charred; remove to a plate. Soften the onion in the same pan, then add garlic and ginger until fragrant.',
            'Pour in the passata and simmer to thicken slightly. Return chicken to the pan and cook gently until cooked through.',
            'Stir in cream and garam masala, adjust salt, and simmer a minute more. Rest briefly, then serve with rice and warm naan.'
        ]
    );

    $r5 = $makeRecipe(
        'Vegetable Stir-Fry',
        'Crisp, colourful veg in a glossy soy-ginger sauce.',
        $liam,
        [
            '1 tbsp neutral oil',
            '1 red pepper, sliced',
            '150 g broccoli florets',
            '150 g sugar snap peas',
            '1 carrot, thinly sliced',
            '2 spring onions, sliced',
            '2 tbsp soy sauce',
            '1 tbsp oyster sauce',
            '1 tsp grated ginger',
            '1 tsp cornflour + 60 ml water'
        ],
        [
            'Heat a wok until smoking, then add the oil. Stir-fry pepper, broccoli, and carrot for 2–3 minutes until bright and just tender-crisp.',
            'Add the sugar snaps and spring onions and toss for 1 minute more. Stir in soy, oyster sauce, and ginger.',
            'Whisk the cornflour with water and pour around the edges of the wok, tossing until the sauce turns glossy and clings to the vegetables.',
            'Serve immediately over steamed rice; add chili oil or toasted sesame seeds if you like heat and nuttiness.'
        ]
    );

    // --- Comments & ratings (one rating per user per recipe) ---
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

    /**
     * r1: Pasta Aglio e Olio
     * Existing ratings: admin(5), aoife(4). Add liam + comments from all.
     */
    $comment($r1, $admin, 'Great and quick!');
    $comment($r1, $aoife, 'Loved the simplicity.');
    $comment($r1, $liam, 'Balanced heat and garlic—spot on.');
    $rate($r1, $liam, 5);

    /**
     * r2: Tomato Soup
     * Existing ratings: admin(4), aoife(5). Add liam + comments from all.
     */
    $comment($r2, $admin, 'Nice weeknight soup.');
    $comment($r2, $aoife, 'Perfect with grilled cheese.');
    $comment($r2, $liam, 'Silky texture and bright tomato flavour.');
    $rate($r2, $liam, 4);

    /**
     * r3: Pancakes — add 3 comments + 3 ratings.
     */
    $comment($r3, $admin, 'Consistently fluffy—great base recipe.');
    $comment($r3, $aoife, 'Kids loved these with blueberries.');
    $comment($r3, $liam, 'Resting the batter makes a difference!');
    $rate($r3, $admin, 4);
    $rate($r3, $aoife, 5);
    $rate($r3, $liam, 4);

    /**
     * r4: Chicken Tikka Masala — add 3 comments + 3 ratings.
     */
    $comment($r4, $admin, 'Nice char on the chicken and a rich sauce.');
    $comment($r4, $aoife, 'Creamy without being heavy—new favourite.');
    $comment($r4, $liam, 'Marinated overnight—depth of flavour was ace.');
    $rate($r4, $admin, 5);
    $rate($r4, $aoife, 4);
    $rate($r4, $liam, 5);

    /**
     * r5: Vegetable Stir-Fry — add 3 comments + 3 ratings.
     */
    $comment($r5, $admin, 'Crisp veg and glossy sauce—very fresh.');
    $comment($r5, $aoife, 'Added tofu cubes—worked perfectly.');
    $comment($r5, $liam, 'Quick dinner with pantry staples.');
    $rate($r5, $admin, 4);
    $rate($r5, $aoife, 4);
    $rate($r5, $liam, 3);
    $manager->flush();
    }
}

