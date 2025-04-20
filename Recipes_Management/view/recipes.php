<?php
require_once 'class/Recipe.php';
require_once 'class/Category.php';
require_once 'class/Ingredient.php';
require_once 'class/RecipeIngredient.php';

// Get search parameter if exists
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get category filter if exists
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create']) || isset($_POST['update'])) {
        // Set recipe properties
        $recipe->title = $_POST['title'];
        $recipe->instructions = $_POST['instructions'];
        $recipe->prep_time = $_POST['prep_time'];
        $recipe->cook_time = $_POST['cook_time'];
        $recipe->servings = $_POST['servings'];
        $recipe->difficulty = $_POST['difficulty'];
        $recipe->image_url = $_POST['image_url'];
        $recipe->category_id = $_POST['category_id'];
        
        if (isset($_POST['create'])) {
            // Create recipe
            if ($recipe->create()) {
                // Add ingredients to recipe
                if (isset($_POST['ingredient_ids']) && isset($_POST['quantities'])) {
                    foreach ($_POST['ingredient_ids'] as $key => $ingredientId) {
                        if (!empty($ingredientId) && isset($_POST['quantities'][$key]) && !empty($_POST['quantities'][$key])) {
                            $recipeIngredient->recipe_id = $recipe->recipe_id;
                            $recipeIngredient->ingredient_id = $ingredientId;
                            $recipeIngredient->quantity = $_POST['quantities'][$key];
                            $recipeIngredient->create();
                        }
                    }
                }
                echo '<div class="alert alert-success">Recipe created successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Unable to create recipe.</div>';
            }
        } else if (isset($_POST['update'])) {
            // Update recipe
            $recipe->recipe_id = $_POST['recipe_id'];
            if ($recipe->update()) {
                // Remove all ingredients and re-add them
                $recipeIngredient->recipe_id = $recipe->recipe_id;
                $recipeIngredient->deleteAllFromRecipe();
                
                // Add ingredients to recipe
                if (isset($_POST['ingredient_ids']) && isset($_POST['quantities'])) {
                    foreach ($_POST['ingredient_ids'] as $key => $ingredientId) {
                        if (!empty($ingredientId) && isset($_POST['quantities'][$key]) && !empty($_POST['quantities'][$key])) {
                            $recipeIngredient->recipe_id = $recipe->recipe_id;
                            $recipeIngredient->ingredient_id = $ingredientId;
                            $recipeIngredient->quantity = $_POST['quantities'][$key];
                            $recipeIngredient->create();
                        }
                    }
                }
                echo '<div class="alert alert-success">Recipe updated successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Unable to update recipe.</div>';
            }
        }
    } else if (isset($_POST['delete'])) {
        // Delete recipe
        $recipe->recipe_id = $_POST['recipe_id'];
        if ($recipe->delete()) {
            echo '<div class="alert alert-success">Recipe deleted successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to delete recipe.</div>';
        }
    }
}

// Check if viewing a single recipe
if (isset($_GET['id'])) {
    $recipe->recipe_id = $_GET['id'];
    if ($recipe->read_single()) {
        // Get ingredients for this recipe
        $ingredientsResult = $recipe->getIngredients();
        $recipeIngredients = $ingredientsResult->fetchAll();
        
        // Get all categories for form
        $categoriesResult = $category->read();
        $categories = $categoriesResult->fetchAll();
        
        // Get all ingredients for form
        $ingredientsListResult = $ingredient->read();
        $ingredientsList = $ingredientsListResult->fetchAll();
        
        // Display single recipe view
        ?>
        <div class="row">
            <div class="col-md-8">
                <h1><?php echo $recipe->title; ?></h1>
                <p class="text-muted">
                    <?php 
                    $category->category_id = $recipe->category_id;
                    $category->read_single();
                    echo $category->name; 
                    ?> | 
                    Difficulty: <?php echo $recipe->difficulty; ?> | 
                    Prep: <?php echo $recipe->prep_time; ?> min | 
                    Cook: <?php echo $recipe->cook_time; ?> min | 
                    Serves: <?php echo $recipe->servings; ?>
                </p>
                
                <h3>Ingredients</h3>
                <ul class="list-group mb-4">
                    <?php foreach($recipeIngredients as $ri): ?>
                        <li class="list-group-item">
                            <?php echo $ri['quantity'] . ' ' . $ri['unit'] . ' ' . $ri['name']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                
                <h3>Instructions</h3>
                <div class="card mb-4">
                    <div class="card-body">
                        <?php echo nl2br($recipe->instructions); ?>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editRecipeModal">
                        Edit Recipe
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteRecipeModal">
                        Delete Recipe
                    </button>
                    <a href="index.php?page=recipes" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <?php if (!empty($recipe->image_url)): ?>
                    <img src="<?php echo $recipe->image_url; ?>" class="img-fluid rounded mb-4" alt="<?php echo $recipe->title; ?>">
                <?php else: ?>
                    <div class="card bg-light mb-4">
                        <div class="card-body text-center">
                            <p class="text-muted">No image available</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Edit Recipe Modal -->
        <div class="modal fade" id="editRecipeModal" tabindex="-1" role="dialog" aria-labelledby="editRecipeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRecipeModalLabel">Edit Recipe</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="index.php?page=recipes">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipe->recipe_id; ?>">
                            
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $recipe->title; ?>" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="category_id">Category</label>
                                    <select class="form-control" id="category_id" name="category_id" required>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($cat['category_id'] == $recipe->category_id) ? 'selected' : ''; ?>>
                                                <?php echo $cat['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="difficulty">Difficulty</label>
                                    <select class="form-control" id="difficulty" name="difficulty" required>
                                        <option value="Easy" <?php echo ($recipe->difficulty == 'Easy') ? 'selected' : ''; ?>>Easy</option>
                                        <option value="Medium" <?php echo ($recipe->difficulty == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                        <option value="Hard" <?php echo ($recipe->difficulty == 'Hard') ? 'selected' : ''; ?>>Hard</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="prep_time">Prep Time (minutes)</label>
                                    <input type="number" class="form-control" id="prep_time" name="prep_time" value="<?php echo $recipe->prep_time; ?>" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="cook_time">Cook Time (minutes)</label>
                                    <input type="number" class="form-control" id="cook_time" name="cook_time" value="<?php echo $recipe->cook_time; ?>" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="servings">Servings</label>
                                    <input type="number" class="form-control" id="servings" name="servings" value="<?php echo $recipe->servings; ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="image_url">Image URL</label>
                                <input type="text" class="form-control" id="image_url" name="image_url" value="<?php echo $recipe->image_url; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Ingredients</label>
                                <div id="ingredients-container">
                                    <?php foreach($recipeIngredients as $index => $ri): ?>
                                        <div class="form-row mb-2 ingredient-row">
                                            <div class="col-md-6">
                                                <select class="form-control" name="ingredient_ids[]" required>
                                                    <?php foreach($ingredientsList as $ing): ?>
                                                        <option value="<?php echo $ing['ingredient_id']; ?>" <?php echo ($ing['ingredient_id'] == $ri['ingredient_id']) ? 'selected' : ''; ?>>
                                                            <?php echo $ing['name'] . ' (' . $ing['unit'] . ')'; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" step="0.01" class="form-control" name="quantities[]" placeholder="Quantity" value="<?php echo $ri['quantity']; ?>" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-secondary mt-2" id="add-ingredient">Add Ingredient</button>
                            </div>
                            
                            <div class="form-group">
                                <label for="instructions">Instructions</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="6" required><?php echo $recipe->instructions; ?></textarea>
                            </div>
                            
                            <button type="submit" name="update" class="btn btn-primary">Update Recipe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Recipe Modal -->
        <div class="modal fade" id="deleteRecipeModal" tabindex="-1" role="dialog" aria-labelledby="deleteRecipeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRecipeModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this recipe? This action cannot be undone.</p>
                        <form method="POST" action="index.php?page=recipes">
                            <input type="hidden" name="recipe_id" value="<?php echo $recipe->recipe_id; ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete Recipe</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
                // Add ingredient row
                $('#add-ingredient').click(function() {
                    var ingredientRow = `
                        <div class="form-row mb-2 ingredient-row">
                            <div class="col-md-6">
                                <select class="form-control" name="ingredient_ids[]" required>
                                    <?php foreach($ingredientsList as $ing): ?>
                                        <option value="<?php echo $ing['ingredient_id']; ?>">
                                            <?php echo $ing['name'] . ' (' . $ing['unit'] . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" step="0.01" class="form-control" name="quantities[]" placeholder="Quantity" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
                            </div>
                        </div>
                    `;
                    $('#ingredients-container').append(ingredientRow);
                });
                
                // Remove ingredient row
                $(document).on('click', '.remove-ingredient', function() {
                    $(this).closest('.ingredient-row').remove();
                });
            });
        </script>
        <?php
    } else {
        echo '<div class="alert alert-danger">Recipe not found.</div>';
        echo '<a href="index.php?page=recipes" class="btn btn-primary">Back to Recipes</a>';
    }
} else {
    // Display recipes list
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Recipes</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createRecipeModal">
            Add New Recipe
        </button>
    </div>
    
    <?php if (!empty($search)): ?>
        <div class="alert alert-info">
            Search results for: <strong><?php echo htmlspecialchars($search); ?></strong>
            <a href="index.php?page=recipes" class="float-right">Clear Search</a>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($categoryFilter)): ?>
        <?php 
        $category->category_id = $categoryFilter;
        $category->read_single();
        ?>
        <div class="alert alert-info">
            Filtering by category: <strong><?php echo $category->name; ?></strong>
            <a href="index.php?page=recipes" class="float-right">Clear Filter</a>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php
        // Get recipes based on search and category filter
        if (!empty($categoryFilter)) {
            $category->category_id = $categoryFilter;
            $result = $category->getRecipes();
            $recipes = $result->fetchAll();
        } else {
            $result = $recipe->read($search);
            $recipes = $result->fetchAll();
        }
        
        if (count($recipes) > 0) {
            foreach($recipes as $r):
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($r['image_url'])): ?>
                            <img src="<?php echo $r['image_url']; ?>" class="card-img-top" alt="<?php echo $r['title']; ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
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
                            <a href="index.php?page=recipes&id=<?php echo $r['recipe_id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php
            endforeach;
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No recipes found.</div></div>';
        }
        ?>
    </div>
    
    <?php
    // Get all categories for form
    $categoriesResult = $category->read();
    $categories = $categoriesResult->fetchAll();
    
    // Get all ingredients for form
    $ingredientsResult = $ingredient->read();
    $ingredients = $ingredientsResult->fetchAll();
    ?>
    
    <!-- Create Recipe Modal -->
    <div class="modal fade" id="createRecipeModal" tabindex="-1" role="dialog" aria-labelledby="createRecipeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createRecipeModalLabel">Add New Recipe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="index.php?page=recipes">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="category_id">Category</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>"><?php echo $cat['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="difficulty">Difficulty</label>
                                <select class="form-control" id="difficulty" name="difficulty" required>
                                    <option value="Easy">Easy</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Hard">Hard</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="prep_time">Prep Time (minutes)</label>
                                <input type="number" class="form-control" id="prep_time" name="prep_time" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cook_time">Cook Time (minutes)</label>
                                <input type="number" class="form-control" id="cook_time" name="cook_time" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="servings">Servings</label>
                                <input type="number" class="form-control" id="servings" name="servings" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input type="text" class="form-control" id="image_url" name="image_url">
                        </div>
                        
                        <div class="form-group">
                            <label>Ingredients</label>
                            <div id="ingredients-container-new">
                                <div class="form-row mb-2 ingredient-row">
                                    <div class="col-md-6">
                                        <select class="form-control" name="ingredient_ids[]" required>
                                            <?php foreach($ingredients as $ing): ?>
                                                <option value="<?php echo $ing['ingredient_id']; ?>">
                                                    <?php echo $ing['name'] . ' (' . $ing['unit'] . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" class="form-control" name="quantities[]" placeholder="Quantity" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="add-ingredient-new">Add Ingredient</button>
                        </div>
                        
                        <div class="form-group">
                            <label for="instructions">Instructions</label>
                            <textarea class="form-control" id="instructions" name="instructions" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" name="create" class="btn btn-primary">Create Recipe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            // Add ingredient row for new recipe
            $('#add-ingredient-new').click(function() {
                var ingredientRow = `
                    <div class="form-row mb-2 ingredient-row">
                        <div class="col-md-6">
                            <select class="form-control" name="ingredient_ids[]" required>
                                <?php foreach($ingredients as $ing): ?>
                                    <option value="<?php echo $ing['ingredient_id']; ?>">
                                        <?php echo $ing['name'] . ' (' . $ing['unit'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" step="0.01" class="form-control" name="quantities[]" placeholder="Quantity" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-ingredient">Remove</button>
                        </div>
                    </div>
                `;
                $('#ingredients-container-new').append(ingredientRow);
            });
            
            // Remove ingredient row
            $(document).on('click', '.remove-ingredient', function() {
                $(this).closest('.ingredient-row').remove();
            });
        });
    </script>
    <?php
}

?>