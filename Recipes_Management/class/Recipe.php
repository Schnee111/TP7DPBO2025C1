<?php
require_once 'config/db.php';

class Recipe {
    private $conn;
    private $table = 'recipes';
    
    // Properties
    public $recipe_id;
    public $title;
    public $instructions;
    public $prep_time;
    public $cook_time;
    public $servings;
    public $difficulty;
    public $image_url;
    public $category_id;
    public $created_at;
    
    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }
    
    // Create recipe
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, instructions, prep_time, cook_time, servings, difficulty, image_url, category_id) 
                  VALUES (:title, :instructions, :prep_time, :cook_time, :servings, :difficulty, :image_url, :category_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->instructions = htmlspecialchars(strip_tags($this->instructions));
        $this->prep_time = htmlspecialchars(strip_tags($this->prep_time));
        $this->cook_time = htmlspecialchars(strip_tags($this->cook_time));
        $this->servings = htmlspecialchars(strip_tags($this->servings));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':instructions', $this->instructions);
        $stmt->bindParam(':prep_time', $this->prep_time);
        $stmt->bindParam(':cook_time', $this->cook_time);
        $stmt->bindParam(':servings', $this->servings);
        $stmt->bindParam(':difficulty', $this->difficulty);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':category_id', $this->category_id);
        
        // Execute query
        if($stmt->execute()) {
            $this->recipe_id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Read all recipes
    public function read($search = '') {
        $query = "SELECT r.*, c.name as category_name 
                  FROM " . $this->table . " r
                  LEFT JOIN categories c ON r.category_id = c.category_id";
        
        if(!empty($search)) {
            $query .= " WHERE r.title LIKE :search OR r.instructions LIKE :search OR c.name LIKE :search";
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind search parameter if provided
        if(!empty($search)) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read single recipe
    public function read_single() {
        $query = "SELECT r.*, c.name as category_name 
                  FROM " . $this->table . " r
                  LEFT JOIN categories c ON r.category_id = c.category_id
                  WHERE r.recipe_id = :recipe_id
                  LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->title = $row['title'];
            $this->instructions = $row['instructions'];
            $this->prep_time = $row['prep_time'];
            $this->cook_time = $row['cook_time'];
            $this->servings = $row['servings'];
            $this->difficulty = $row['difficulty'];
            $this->image_url = $row['image_url'];
            $this->category_id = $row['category_id'];
            $this->created_at = $row['created_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update recipe
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET title = :title, 
                      instructions = :instructions, 
                      prep_time = :prep_time, 
                      cook_time = :cook_time, 
                      servings = :servings, 
                      difficulty = :difficulty, 
                      image_url = :image_url, 
                      category_id = :category_id
                  WHERE recipe_id = :recipe_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->instructions = htmlspecialchars(strip_tags($this->instructions));
        $this->prep_time = htmlspecialchars(strip_tags($this->prep_time));
        $this->cook_time = htmlspecialchars(strip_tags($this->cook_time));
        $this->servings = htmlspecialchars(strip_tags($this->servings));
        $this->difficulty = htmlspecialchars(strip_tags($this->difficulty));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->recipe_id = htmlspecialchars(strip_tags($this->recipe_id));
        
        // Bind data
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':instructions', $this->instructions);
        $stmt->bindParam(':prep_time', $this->prep_time);
        $stmt->bindParam(':cook_time', $this->cook_time);
        $stmt->bindParam(':servings', $this->servings);
        $stmt->bindParam(':difficulty', $this->difficulty);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete recipe
    public function delete() {
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
    
    // Get ingredients for a recipe
    public function getIngredients() {
        $query = "SELECT i.*, ri.quantity 
                  FROM ingredients i
                  JOIN recipe_ingredients ri ON i.ingredient_id = ri.ingredient_id
                  WHERE ri.recipe_id = :recipe_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recipe_id', $this->recipe_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>