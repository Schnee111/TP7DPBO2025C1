<?php
require_once 'config/db.php';

class Category {
    private $conn;
    private $table = 'categories';
    
    // Properties
    public $category_id;
    public $name;
    public $description;
    public $created_at;
    
    public function __construct() {
        $this->conn = DB::getInstance()->getConnection();
    }
    
    // Create category
    public function create() {
        $query = "INSERT INTO " . $this->table . " (name, description) VALUES (:name, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        
        // Execute query
        if($stmt->execute()) {
            $this->category_id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Read all categories
    public function read($search = '') {
        $query = "SELECT * FROM " . $this->table;
        
        // Add search condition if provided
        if(!empty($search)) {
            $query .= " WHERE name LIKE :search OR description LIKE :search";
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
    
    // Read single category
    public function read_single() {
        $query = "SELECT * FROM " . $this->table . " WHERE category_id = :category_id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->created_at = $row['created_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update category
    public function update() {
        $query = "UPDATE " . $this->table . " SET name = :name, description = :description WHERE category_id = :category_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        // Bind data
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete category
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE category_id = :category_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        // Bind data
        $stmt->bindParam(':category_id', $this->category_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get recipes in this category
    public function getRecipes() {
        $query = "SELECT * FROM recipes WHERE category_id = :category_id ORDER BY title ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>