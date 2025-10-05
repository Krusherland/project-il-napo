<?php
session_start();

// Check authentication
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: ../../public/login.html');
    exit();
}

$success = false;
$errorMessage = '';

try {
    // Validate input
    if (empty($_POST['producto']) || empty($_POST['precio']) || empty($_POST['categoria'])) {
        throw new Exception("Faltan datos del producto.");
    }
    
    $product = trim($_POST['producto']);
    $price = trim($_POST['precio']);
    $category = $_POST['categoria'];
    
    // Validate price is numeric
    if (!is_numeric($price)) {
        throw new Exception("El precio debe ser un n\u00famero v\u00e1lido.");
    }

    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    $link = new Db();

    include_once("../classes/upload.class.php");
    $upload = new Upload();
    
    $path_img = $upload->uploadImage();
    $sql = "INSERT INTO products (id_category, price, product_name, image) VALUES (?,?,?,?)";
    $stmt = $link->run($sql, [$category, $price, $product, $path_img]);
    
    $success = true;
    
} catch (Exception $e) {
    error_log("Save product error: " . $e->getMessage());
    $errorMessage = $e->getMessage();
}

// Now output HTML response
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardar Producto - Il Napolitano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if ($success): ?>
                    <div class="alert alert-success text-center">
                        <h4><i class="fas fa-check-circle"></i> \u00a1\u00c9xito!</h4>
                        <p>Producto insertado correctamente</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger text-center">
                        <h4><i class="fas fa-exclamation-triangle"></i> Error</h4>
                        <p><?php echo htmlspecialchars($errorMessage); ?></p>
                        <a href="insert.php" class="btn btn-primary">Volver a intentar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js" integrity="sha512-oVbWSv2O4y1UzvExJMHaHcaib4wsBMS5tEP3/YkMP6GmkwRJAa79Jwsv+Y/w7w2Vb/98/Xhvck10LyJweB8Jsw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            bootbox.alert({
                title: 'Insertar producto',
                message: 'Producto insertado correctamente',
                callback: function() {
                    window.location.href = 'insert.php';
                }
            })
        })
    </script>
</body>

</html>