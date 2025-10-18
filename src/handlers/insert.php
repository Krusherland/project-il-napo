<?php
session_start();

// Check authentication
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: ../../public/login.html');
    exit();
}

$categories = [];
$error = '';

try {
    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    $link = new Db();
    
    // Load categories for dropdown
    $sql = "SELECT id_category, category_name FROM categories ORDER BY category_name";
    $stmt = $link->run($sql);
    $categories = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Insert form error: " . $e->getMessage());
    $error = 'Error al cargar las categor\u00edas';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Producto - Il Napolitano</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Oswald:wght@400;500;600&family=Work+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="grain-background">
    <div class="container">
        <div class="form-container">
            <h1 class="form-title">INSERTAR PRODUCTO</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="col-md-12">
                <form class="form-group" accept-charset="utf-8" action="save_products.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <br> <label class="control-label" for="producto">PRODUCTO</label>
                        <input id="producto" name="producto" placeholder="PRODUCTO"
                            class="form-control" required="" type="text">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="precio">PRECIO</label> <input
                            id="precio" name="precio" placeholder="PRECIO"
                            class="form-control" required="" type="text">
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="categoria">CATEGORIA DEL PRODUCTO</label>
                        <select id="categoria" name="categoria" class="form-control" required>
                            <option value="">Seleccionar categoria</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id_category']; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="file">Seleccione la imagen a subir</label>
                        <input type="file" id="imagen" class="form-control" name="imagen" size="30" />
                    </div>
                    <br>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary mr-3" onclick="window.history.back()">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../../assets/js/il-napolitano-modal.js"></script>
    <script>
        // File validation and preview
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2000000) {
                    ilNapolitanoAlert({
                        title: 'Archivo demasiado grande',
                        message: 'El archivo es demasiado grande. El tamaño máximo es 2MB.',
                        type: 'warning',
                        icon: 'fa-exclamation-triangle'
                    });
                    e.target.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    ilNapolitanoAlert({
                        title: 'Tipo de archivo inválido',
                        message: 'Tipo de archivo no válido. Solo se permiten JPG, PNG y GIF.',
                        type: 'warning',
                        icon: 'fa-file-image'
                    });
                    e.target.value = '';
                    return;
                }
                
                // Show file info
                const existingInfo = document.querySelector('.file-info');
                if (existingInfo) {
                    existingInfo.remove();
                }
                
                const fileInfo = document.createElement('div');
                fileInfo.className = 'mt-2 p-2 bg-light border rounded file-info';
                fileInfo.innerHTML = `
                    <small class="text-success">
                        <strong>✅ Imagen seleccionada:</strong><br>
                        📁 ${file.name}<br>
                        📏 ${(file.size / 1024 / 1024).toFixed(2)} MB
                    </small>
                `;
                
                e.target.parentNode.appendChild(fileInfo);
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const producto = document.getElementById('producto').value.trim();
            const precio = document.getElementById('precio').value;
            const categoria = document.getElementById('categoria').value;
            
            if (!producto) {
                ilNapolitanoAlert({
                    title: 'Campo requerido',
                    message: 'Por favor ingrese el nombre del producto.',
                    type: 'warning',
                    icon: 'fa-exclamation-triangle'
                });
                e.preventDefault();
                return;
            }
            
            if (!precio || parseFloat(precio) <= 0) {
                ilNapolitanoAlert({
                    title: 'Precio inválido',
                    message: 'Por favor ingrese un precio válido.',
                    type: 'warning',
                    icon: 'fa-dollar-sign'
                });
                e.preventDefault();
                return;
            }
            
            if (!categoria) {
                ilNapolitanoAlert({
                    title: 'Categoría requerida',
                    message: 'Por favor seleccione una categoría.',
                    type: 'warning',
                    icon: 'fa-list'
                });
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>