<?php
require_once 'config/db.php';

class Ingredient {
    private $conn;
    private $table = 'ingredients';
    
    // Properties
    public $ingredient_id;
    public $name;
    public $unit;
    public $created_at;
    
    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }
    
    // Create ingredient
    public function create() {
        $query = "INSERT INTO " . $this->table . " (name, unit) VALUES (:name, :unit)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':unit', $this->unit);
        
        // Execute query
        if($stmt->execute()) {
            $this->ingredient_id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Read all ingredients
    public function read($search = '') {
        $query = "SELECT * FROM " . $this->table;
        
        // Add search condition if provided
        if(!empty($search)) {
            $query .= " WHERE name LIKE :search OR unit LIKE :search";
        }
        
        $query .= " ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind search parameter if provided
        if(!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read single ingredient
    public function read_single() {
        $query = "SELECT * FROM " . $this->table . " WHERE ingredient_id = :ingredient_id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->unit = $row['unit'];
            $this->created_at = $row['created_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update ingredient
    public function update() {
        $query = "UPDATE " . $this->table . " SET name = :name, unit = :unit WHERE ingredient_id = :ingredient_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->unit = htmlspecialchars(strip_tags($this->unit));
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':unit', $this->unit);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete ingredient
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE ingredient_id = :ingredient_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->ingredient_id = htmlspecialchars(strip_tags($this->ingredient_id));
        
        // Bind data
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get recipes that use this ingredient
    public function getRecipes() {
        $query = "SELECT r.*, ri.quantity 
                  FROM recipes r
                  JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
                  WHERE ri.ingredient_id = :ingredient_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingredient_id', $this->ingredient_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>