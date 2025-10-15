<?php
session_start();

// Check authentication
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: ../../public/login.html');
    exit();
}

// Validate product ID
if (!isset($_GET['q']) || !is_numeric($_GET['q'])) {
    $_SESSION['error'] = 'ID de producto invalido';
    header('Location: welcome.php');
    exit();
}

$data = null;
$categories = [];
$error = '';

try {
    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    $link = new Db();
    $idUpt = (int)$_GET['q'];
    
    // Get product data
    $sql = "SELECT p.id_product, p.id_category, p.product_name, p.price, p.start_date, p.image, c.category_name 
            FROM products p 
            INNER JOIN categories c ON p.id_category = c.id_category 
            WHERE id_product = ?";
    $stmt = $link->run($sql, [$idUpt]);
    $data = $stmt->fetch();
    
    if (!$data) {
        throw new Exception('Producto no encontrado');
    }
    
    // Get all categories for dropdown
    $sqlCategory = "SELECT id_category, category_name FROM categories ORDER BY category_name";
    $stmt = $link->run($sqlCategory);
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Edit product error: " . $e->getMessage());
    $error = 'Error al cargar el producto';
}

// If there's an error, redirect back
if ($error || !$data) {
    $_SESSION['error'] = $error ?: 'Producto no encontrado';
    header('Location: welcome.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Il Napolitano</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Oswald:wght@400;500;600&family=Work+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" type="image/svg+xml" href="../../assets/images/pizza.svg">
</head>

<body class="grain-background">
    <div class="container">
        <div class="form-container">
            <h1 class="form-title">EDITAR PRODUCTO</h1>
            
            <?php
            // Show upload feedback messages
            if (isset($_SESSION['upload_error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                echo '<strong>Error:</strong> ' . htmlspecialchars($_SESSION['upload_error']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['upload_error']);
            }
            if (isset($_SESSION['upload_success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo '<strong>Éxito:</strong> ' . htmlspecialchars($_SESSION['upload_success']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['upload_success']);
            }
            ?>
            <form accept-charset="utf-8" action="update_Product.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $data['id_product'] ?>">
                
                <div class="mb-4">
                    <label class="form-label" for="nombre">🍕 Nombre del Producto</label>
                    <input id="nombre" name="nombre" class="form-control" type="text" 
                           value="<?php echo htmlspecialchars($data['product_name']) ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="precio">💰 Precio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input id="precio" name="precio" class="form-control" type="number" 
                               step="0.01" min="0" value="<?php echo trim($data['price']) ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label" for="categoria">📂 Categoría</label>
                    <select id="categoria" name="categoria" class="form-control" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id_category']; ?>" 
                                <?php echo ($data['id_category'] == $category['id_category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="fecha">📅 Fecha de Alta</label>
                    <input id="fecha" name="fecha" class="form-control" type="date" 
                           value="<?php echo $data['start_date'] ?>" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">📷 Imagen del Producto</label>
                    
                    <?php if (!empty($data['image'])): ?>
                        <div class="current-image text-center mb-3">
                            <p class="mb-2"><strong>Imagen actual:</strong></p>
                            <?php 
                            $imagePath = $data['image'];
                            $imageName = basename($imagePath);
                            
                            // Check if the image exists in assets/images/
                            $assetImagePath = "../../assets/images/" . $imageName;
                            $fullServerPath = dirname(dirname(__DIR__)) . "/assets/images/" . $imageName;
                            
                            if (file_exists($fullServerPath)) {
                                $displayPath = $assetImagePath;
                                $imageFound = true;
                            } else {
                                // Try the original path
                                $displayPath = "../../" . $imagePath;
                                $imageFound = false;
                            }
                            ?>
                            
                            <?php if ($imageFound): ?>
                                <img src="<?php echo $displayPath ?>" alt="Imagen actual" 
                                     class="img-thumbnail product-thumbnail">
                                <div class="mt-2">
                                    <small class="text-muted">📁 <?php echo htmlspecialchars($imageName) ?></small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <strong>⚠️ Imagen no encontrada</strong><br>
                                    Archivo: <?php echo htmlspecialchars($imageName) ?><br>
                                    <small>La imagen se buscó en: assets/images/<?php echo $imageName ?></small>
                                </div>
                            <?php endif; ?>
                            
                            <input type="hidden" name="current_image" value="<?php echo $data['image'] ?>">
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*">
                    <small class="form-text text-muted mt-2">
                        💡 Seleccione una nueva imagen (JPG, PNG, GIF) o deje vacío para mantener la imagen actual. Tamaño máximo: 2MB
                    </small>
                </div>
                <div class="text-center mt-5">
                    <button type="button" class="btn btn-secondary me-3" onclick="window.history.back()">
                        ❌ Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        ✅ Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Add file preview and validation
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2000000) {
                    alert('El archivo es demasiado grande. El tamaño máximo es 2MB.');
                    e.target.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no válido. Solo se permiten JPG, PNG y GIF.');
                    e.target.value = '';
                    return;
                }
                
                // Show file info
                const fileInfo = document.createElement('div');
                fileInfo.className = 'mt-2 p-2 bg-light border rounded';
                fileInfo.innerHTML = `
                    <small class="text-success">
                        <strong>Nueva imagen seleccionada:</strong><br>
                        📁 ${file.name}<br>
                        📏 ${(file.size / 1024 / 1024).toFixed(2)} MB<br>
                        <em>La imagen se subirá al guardar el producto</em>
                    </small>
                `;
                
                // Remove any existing file info
                const existingInfo = document.querySelector('.file-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                fileInfo.className += ' file-info';
                e.target.parentNode.appendChild(fileInfo);
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>

</html>