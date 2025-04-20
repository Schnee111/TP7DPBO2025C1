# TP7DPBO2025C1

## Janji
_Saya, **Muhammad Daffa Ma'arif** dengan NIM **2305771**, mengerjakan **Tugas Praktikum 7** dalam mata kuliah **DPBO** dengan sebaik-baiknya demi keberkahan-Nya.  
Saya berjanji tidak melakukan kecurangan sebagaimana yang telah dispesifikasikan. **Aamiin.**_

## Deskripsi Program

Recipes Management adalah aplikasi manajemen resep berbasis PHP dan PDO yang memungkinkan pengguna untuk melihat, menambah, mengedit, menghapus, serta mencari resep makanan atau minuman. Aplikasi ini terdiri dari tiga fitur utama: pengelolaan resep, kategori, dan ingredients atau bahan.

## Fitur Utama

1. CRUD Resep – Tambah, edit, hapus, dan tampilkan resep lengkap dengan gambar dan kategori.
2. CRUD Kategori – Kelola daftar kategori resep.
3. CRUD Bahan – kelola list bahan yang dapat digunakan pada resep.
4. Search – Cari produk berdasarkan nama resep, kandungan bahan, atau kategori dari resep.
5. Relasi Database – resep terhubung ke kategori, juga resep dan bahan terhubung ke bahanResep secara langsung melalui Foreign Key.

## Alur Program

### Halaman Utama (index.php)

- Menampilkan navigasi untuk mengakses halaman Resep, Categories, dan Ingredients.
- Menyisipkan tampilan sesuai parameter page dan action.
- Search bar digunakan untuk mencari resep berdasarkan nama, kategori, atau kandungan bahannya.

### Resep

- Menampilkan List Resep, dapat melihat detailnya dengan meng-klik "view details", pada detail dapat menghapus dan edit resep
- Tombol "Add New Recipes" akan membuka form tambah produk.
- Data resep ditampilkan lengkap dengan nama, kategori, dan informasi lainnya.

### Kategori

- Menampilkan semua kategori resep, dapat diedit dan hapus pada detail kategori.
- Tombol "Add New Category" akan menampilkan form untuk menambahkan kategori baru.

### Ingredients/bahan

- Menampilkan semua bahan yang ada pada database, dapat diedit dan hapus pada detail bahan.
- Tombol "Add New Ingredients" untuk menambahkan transaksi baru.

## Struktur Database

![image](https://github.com/user-attachments/assets/ac467b0f-fdce-48b6-9826-6b813df5ea97)

Database: recipe_system

**Tabel `categories`**
- `category_id` (Primary Key)
- `name`
- `description`
- `created_at`

**Tabel `recipes`**
- `recipe_id` (Primary Key, AUTO_INCREMENT)
- `title`
- `instructions`
- `prep_time`
- `cook_time`
- `servings`
- `difficulty`
- `image_url`
- `category_id` (Foreign Key → `categories.category_id`)
- `created_at`

**Tabel `ingredients`**
- `ingredient_id` (Primary Key, AUTO_INCREMENT)
- `name`
- `unit`
- `created_at`

**Tabel `recipe_ingredients`**
- `recipe_id` (Foreign Key → `recipes.recipe_id`)
- `ingredient_id` (Foreign Key → `ingredients.ingredient_id`)
- `quantity`
- **Primary Key:** (`recipe_id`, `ingredient_id`)

---

## Penggunaan PDO dan Prepared Statement

### Contoh di `Recipes.php`:
```php
$query = "SELECT r.*, c.name as category_name 
                  FROM " . $this->table . " r
                  LEFT JOIN categories c ON r.category_id = c.category_id";
        
        if(!empty($search)) {
            $query .= " WHERE r.title LIKE :search OR r.instructions LIKE :search OR c.name LIKE :search";
        }

$query .= " ORDER BY r.created_at DESC";
        
$stmt = $this->conn->prepare($query);
```

### Contoh Insert:
```php
$query = "INSERT INTO " . $this->table . " 
                  (title, instructions, prep_time, cook_time, servings, difficulty, image_url, category_id) 
                  VALUES (:title, :instructions, :prep_time, :cook_time, :servings, :difficulty, :image_url, :category_id)";
        
        $stmt = $this->conn->prepare($query);
```

### Contoh Update:
```php
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
```

## Modularisasi Project

Struktur folder Recipes_Management:
```
/Recipes_Management
│
├── class/
│   ├── Ingredient.php
│   ├── Category.php
│   ├── Recipe.php
│   └── RecipeIngredient.php
│
├── config/
│   └── db.php
│
├── database/
│   └── db_recipes.sql
│   
├── view/
│   ├── header.php
│   ├── footer.php
│   ├── categories.php
│   ├── ingredients.php
│   └── recipes.php   
│
├── index.php
│
└── style.css
```

## Dokumentasi


https://github.com/user-attachments/assets/efee8955-c0c7-4ba7-b4e8-a784536e8fc1


