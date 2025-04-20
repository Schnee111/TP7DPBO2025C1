<?php
require_once 'class/Recipe.php';
require_once 'class/Category.php';
require_once 'class/Ingredient.php';
require_once 'class/RecipeIngredient.php';

$recipe = new Recipe();
$category = new Category();
$ingredient = new Ingredient();
$recipeIngredient = new RecipeIngredient();

// Handle recipe ingredient relationships
if (isset($_POST['add_ingredient'])) {
    $recipeIngredient->recipe_id = $_POST['recipe_id'];
    $recipeIngredient->ingredient_id = $_POST['ingredient_id'];
    $recipeIngredient->quantity = $_POST['quantity'];
    $recipeIngredient->create();
}

if (isset($_GET['remove_ingredient'])) {
    $parts = explode('-', $_GET['remove_ingredient']);
    if (count($parts) == 2) {
        $recipeIngredient->recipe_id = $parts[0];
        $recipeIngredient->ingredient_id = $parts[1];
        $recipeIngredient->delete();
    }
}

// Get search parameter if exists
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <?php include 'view/header.php'; ?>
    
    <div class="container mt-4">
        <main>
            <div class="jumbotron">
                <h1 class="display-4">Welcome to Recipe Management System</h1>
                <p class="lead">Organize, create, and discover delicious recipes!</p>
                <hr class="my-4">
                <p>This system allows you to manage your recipes, ingredients, and categories with ease.</p>
                <nav class="nav nav-pills">
                    <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'recipes') ? 'active bg-primary' : ''; ?>" href="?page=recipes">Recipes</a>
                    <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'ingredients') ? 'active bg-primary' : ''; ?>" href="?page=ingredients">Ingredients</a>
                    <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'categories') ? 'active bg-primary' : ''; ?>" href="?page=categories">Categories</a>
                </nav>
            </div>

            <?php
            // Display search form
           

            // Include the appropriate page based on the 'page' parameter
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                if ($page == 'recipes') {
                    include 'view/recipes.php';
                } elseif ($page == 'ingredients') {
                    include 'view/ingredients.php';
                } elseif ($page == 'categories') {
                    include 'view/categories.php';
                }
            } else {
                // Default page - show latest recipes
                $result = $recipe->read($search);
                $recipes = $result->fetchAll();
                
                // Get all categories
                $categoryResult = $category->read();
                $categories = $categoryResult->fetchAll();
                ?>
                <div class="row">
                    <div class="col-md-8">
                        <h2>Latest Recipes</h2>
                        <div class="row">
                            <?php foreach(array_slice($recipes, 0, 6) as $r): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $r['title']; ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?php 
                                                if (isset($r['category_name'])) {
                                                    echo $r['category_name'];
                                                } else {
                                                    $tempCategory = new Category();
                                                    $tempCategory->category_id = $r['category_id'];
                                                    $tempCategory->read_single();
                                                    echo $tempCategory->name;
                                                }
                                                ?>
                                            </h6>
                                            <p class="card-text">
                                                Prep: <?php echo $r['prep_time']; ?> min | 
                                                Cook: <?php echo $r['cook_time']; ?> min | 
                                                Serves: <?php echo $r['servings']; ?>
                                            </p>
                                            <a href="?page=recipes&id=<?php echo $r['recipe_id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="?page=recipes" class="btn btn-outline-primary">View All Recipes</a>
                    </div>
                    
                    <div class="col-md-4">
                        <h2>Categories</h2>
                        <ul class="list-group">
                            <?php foreach($categories as $cat): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="?page=recipes&category=<?php echo $cat['category_id']; ?>">
                                        <?php echo $cat['name']; ?>
                                    </a>
                                    <?php 
                                    $category->category_id = $cat['category_id'];
                                    $recipeCount = $category->getRecipes()->rowCount();
                                    ?>
                                    <span class="badge badge-primary badge-pill"><?php echo $recipeCount; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-3">
                            <a href="?page=categories" class="btn btn-outline-primary">Manage Categories</a>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </main>
    </div>
    
    <?php include 'view/footer.php'; ?>
</body>
</html>