<?php
require_once 'class/Ingredient.php';


// Get search parameter if exists
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Set ingredient properties
        $ingredient->name = $_POST['name'];
        $ingredient->unit = $_POST['unit'];
        
        // Create ingredient
        if ($ingredient->create()) {
            echo '<div class="alert alert-success">Ingredient created successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to create ingredient.</div>';
        }
    } else if (isset($_POST['update'])) {
        // Set ingredient properties
        $ingredient->ingredient_id = $_POST['ingredient_id'];
        $ingredient->name = $_POST['name'];
        $ingredient->unit = $_POST['unit'];
        
        // Update ingredient
        if ($ingredient->update()) {
            echo '<div class="alert alert-success">Ingredient updated successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to update ingredient.</div>';
        }
    } else if (isset($_POST['delete'])) {
        // Delete ingredient
        $ingredient->ingredient_id = $_POST['ingredient_id'];
        if ($ingredient->delete()) {
            echo '<div class="alert alert-success">Ingredient deleted successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to delete ingredient.</div>';
        }
    }
}

// Check if viewing a single ingredient
if (isset($_GET['id'])) {
    $ingredient->ingredient_id = $_GET['id'];
    if ($ingredient->read_single()) {
        // Get recipes that use this ingredient
        $recipesResult = $ingredient->getRecipes();
        $recipes = $recipesResult->fetchAll();
        
        // Display single ingredient view
        ?>
        <div class="row">
            <div class="col-md-8">
                <h1><?php echo $ingredient->name; ?></h1>
                <p class="text-muted">Unit: <?php echo $ingredient->unit; ?></p>
                
                <h3>Used in Recipes</h3>
                <?php if (count($recipes) > 0): ?>
                    <ul class="list-group mb-4">
                        <?php foreach($recipes as $recipe): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="index.php?page=recipes&id=<?php echo $recipe['recipe_id']; ?>">
                                    <?php echo $recipe['title']; ?>
                                </a>
                                <span class="badge badge-primary badge-pill">
                                    <?php echo $recipe['quantity'] . ' ' . $ingredient->unit; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">This ingredient is not used in any recipes yet.</div>
                <?php endif; ?>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editIngredientModal">
                        Edit Ingredient
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteIngredientModal">
                        Delete Ingredient
                    </button>
                    <a href="index.php?page=ingredients" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
        
        <!-- Edit Ingredient Modal -->
        <div class="modal fade" id="editIngredientModal" tabindex="-1" role="dialog" aria-labelledby="editIngredientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIngredientModalLabel">Edit Ingredient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="index.php?page=ingredients">
                            <input type="hidden" name="ingredient_id" value="<?php echo $ingredient->ingredient_id; ?>">
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $ingredient->name; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit" value="<?php echo $ingredient->unit; ?>" required>
                            </div>
                            
                            <button type="submit" name="update" class="btn btn-primary">Update Ingredient</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Ingredient Modal -->
        <div class="modal fade" id="deleteIngredientModal" tabindex="-1" role="dialog" aria-labelledby="deleteIngredientModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteIngredientModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this ingredient? This action cannot be undone.</p>
                        <?php if (count($recipes) > 0): ?>
                            <div class="alert alert-warning">
                                <strong>Warning:</strong> This ingredient is used in <?php echo count($recipes); ?> recipe(s). 
                                Deleting it will remove it from all recipes.
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="index.php?page=ingredients">
                            <input type="hidden" name="ingredient_id" value="<?php echo $ingredient->ingredient_id; ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete Ingredient</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Ingredient not found.</div>';
        echo '<a href="index.php?page=ingredients" class="btn btn-primary">Back to Ingredients</a>';
    }
} else {
    // Display ingredients list
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ingredients</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createIngredientModal">
            Add New Ingredient
        </button>
    </div>
    
    <?php if (!empty($search)): ?>
        <div class="alert alert-info">
            Search results for: <strong><?php echo htmlspecialchars($search); ?></strong>
            <a href="index.php?page=ingredients" class="float-right">Clear Search</a>
        </div>
    <?php endif; ?>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $ingredient->read($search);
                $ingredients = $result->fetchAll();
                
                if (count($ingredients) > 0) {
                    foreach($ingredients as $ing):
                    ?>
                        <tr>
                            <td><?php echo $ing['name']; ?></td>
                            <td><?php echo $ing['unit']; ?></td>
                            <td>
                                <a href="index.php?page=ingredients&id=<?php echo $ing['ingredient_id']; ?>" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                } else {
                    echo '<tr><td colspan="3" class="text-center">No ingredients found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Create Ingredient Modal -->
    <div class="modal fade" id="createIngredientModal" tabindex="-1" role="dialog" aria-labelledby="createIngredientModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createIngredientModalLabel">Add New Ingredient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="index.php?page=ingredients">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                        
                        <button type="submit" name="create" class="btn btn-primary">Create Ingredient</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>