-- Create database
CREATE DATABASE IF NOT EXISTS recipe_system;
USE recipe_system;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create recipes table
CREATE TABLE IF NOT EXISTS recipes (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    instructions TEXT NOT NULL,
    prep_time INT,
    cook_time INT,
    servings INT,
    difficulty VARCHAR(50),
    image_url VARCHAR(255),
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
);

-- Create ingredients table
CREATE TABLE IF NOT EXISTS ingredients (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create junction table for recipes and ingredients
CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT,
    ingredient_id INT,
    quantity DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO categories (name, description) VALUES
('Breakfast', 'Morning meals to start your day'),
('Main Course', 'Primary dishes for lunch or dinner'),
('Dessert', 'Sweet treats to finish a meal'),
('Appetizer', 'Small dishes served before a main course'),
('Beverage', 'Drinks and cocktails');

INSERT INTO ingredients (name, unit) VALUES
('Flour', 'cups'),
('Sugar', 'cups'),
('Eggs', 'units'),
('Milk', 'cups'),
('Butter', 'tablespoons'),
('Salt', 'teaspoons'),
('Olive Oil', 'tablespoons'),
('Chicken', 'pounds'),
('Tomatoes', 'units'),
('Onions', 'units');

INSERT INTO recipes (title, instructions, prep_time, cook_time, servings, difficulty, category_id) VALUES
('Classic Pancakes', 'Mix dry ingredients. Add wet ingredients. Cook on griddle until golden brown.', 10, 15, 4, 'Easy', 1),
('Chicken Stir Fry', 'Cut chicken into strips. Stir fry with vegetables. Add sauce and serve over rice.', 20, 15, 2, 'Medium', 2),
('Chocolate Chip Cookies', 'Cream butter and sugar. Add eggs and vanilla. Mix in dry ingredients and chocolate chips. Bake at 350F.', 15, 12, 24, 'Easy', 3);

INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES
(1, 1, 2), -- 2 cups flour for pancakes
(1, 2, 0.25), -- 1/4 cup sugar for pancakes
(1, 3, 2), -- 2 eggs for pancakes
(1, 4, 1.5), -- 1.5 cups milk for pancakes
(2, 8, 1), -- 1 pound chicken for stir fry
(2, 9, 2), -- 2 tomatoes for stir fry
(2, 10, 1), -- 1 onion for stir fry
(2, 7, 2), -- 2 tbsp olive oil for stir fry
(3, 1, 2.25), -- 2.25 cups flour for cookies
(3, 2, 1.5), -- 1.5 cups sugar for cookies
(3, 3, 2), -- 2 eggs for cookies
(3, 5, 8); -- 8 tbsp butter for cookies