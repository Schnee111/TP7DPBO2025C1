<?php
require_once 'class/Category.php';

// Get search parameter if exists
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Set category properties
        $category->name = $_POST['name'];
        $category->description = $_POST['description'];
        
        // Create category
        if ($category->create()) {
            echo '<div class="alert alert-success">Category created successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to create category.</div>';
        }
    } else if (isset($_POST['update'])) {
        // Set category properties
        $category->category_id = $_POST['category_id'];
        $category->name = $_POST['name'];
        $category->description = $_POST['description'];
        
        // Update category
        if ($category->update()) {
            echo '<div class="alert alert-success">Category updated successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to update category.</div>';
        }
    } else if (isset($_POST['delete'])) {
        // Delete category
        $category->category_id = $_POST['category_id'];
        if ($category->delete()) {
            echo '<div class="alert alert-success">Category deleted successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Unable to delete category.</div>';
        }
    }
}

// Check if viewing a single category
if (isset($_GET['id'])) {
    $category->category_id = $_GET['id'];
    if ($category->read_single()) {
        // Get recipes in this category
        $recipesResult = $category->getRecipes();
        $recipes = $recipesResult->fetchAll();
        
        // Display single category view
        ?>
        <div class="row">
            <div class="col-md-8">
                <h1><?php echo $category->name; ?></h1>
                <p><?php echo $category->description; ?></p>
                
                <h3>Recipes in this Category</h3>
                <?php if (count($recipes) > 0): ?>
                    <div class="row">
                        <?php foreach($recipes as $recipe): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $recipe['title']; ?></h5>
                                        <p class="card-text">
                                            Prep: <?php echo $recipe['prep_time']; ?> min | 
                                            Cook: <?php echo $recipe['cook_time']; ?> min | 
                                            Serves: <?php echo $recipe['servings']; ?>
                                        </p>
                                        <a href="index.php?page=recipes&id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No recipes in this category yet.</div>
                <?php endif; ?>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editCategoryModal">
                        Edit Category
                    </button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCategoryModal">
                        Delete Category
                    </button>
                    <a href="index.php?page=categories" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
        
        <!-- Edit Category Modal -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="index.php?page=categories">
                            <input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>">
                            
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $category->name; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $category->description; ?></textarea>
                            </div>
                            
                            <button type="submit" name="update" class="btn btn-primary">Update Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delete Category Modal -->
        <div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                        <?php if (count($recipes) > 0): ?>
                            <div class="alert alert-warning">
                                <strong>Warning:</strong> This category contains <?php echo count($recipes); ?> recipe(s). 
                                Deleting it will set their category to NULL.
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="index.php?page=categories">
                            <input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Delete Category</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Category not found.</div>';
        echo '<a href="index.php?page=categories" class="btn btn-primary">Back to Categories</a>';
    }
} else {
    // Display categories list
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Categories</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCategoryModal">
            Add New Category
        </button>
    </div>
    
    <?php if (!empty($search)): ?>
        <div class="alert alert-info">
            Search results for: <strong><?php echo htmlspecialchars($search); ?></strong>
            <a href="index.php?page=categories" class="float-right">Clear Search</a>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php
        $result = $category->read($search);
        $categories = $result->fetchAll();
        
        if (count($categories) > 0) {
            foreach($categories as $cat):
                // Get recipe count for this category
                $category->category_id = $cat['category_id'];
                $recipeCount = $category->getRecipes()->rowCount();
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $cat['name']; ?></h5>
                            <p class="card-text"><?php echo $cat['description']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php?page=categories&id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                <span class="badge badge-pill badge-secondary"><?php echo $recipeCount; ?> recipes</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            endforeach;
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No categories found.</div></div>';
        }
        ?>
    </div>
    
    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="index.php?page=categories">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" name="create" class="btn btn-primary">Create Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>