<?php
require_once 'config/db.php';

class RecipeIngredient {
    private $conn;
    private $table = 'recipe_ingredients';
    
    // Properties
    public $recipe_id;
    public $ingredient_id;
    public $quantity;
    
    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }
    
    // Add ingredient to recipe
    public function create() {
        $query = "INSERT INTO " . $this->table . " (recipe_id, ingredient_id, quantity) VALUES (:recipe_id, :ingredient_id, :quantity)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        
        // Bind data
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->bindParam(':quantity', $this->quantity);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update ingredient quantity in recipe
    public function update() {
        $query = "UPDATE " . $this->table . " SET quantity = :quantity WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        
        // Bind data
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Remove ingredient from recipe
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        
        // Bind data
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Remove all ingredients from a recipe
    public function deleteAllFromRecipe() {
        $query = "DELETE FROM " . $this->table . " WHERE recipe_id = :recipe_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        
        // Bind data
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>