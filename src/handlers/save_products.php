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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Il-Napolitano themed success modal
        function showSuccessModal() {
            const modalHTML = `
                <div id="successModal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                    font-family: 'Work Sans', sans-serif;
                ">
                    <div style="
                        background: linear-gradient(135deg, #fff5e6 0%, #fff 50%, #fff5e6 100%);
                        border-radius: 12px;
                        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
                        max-width: 400px;
                        width: 90%;
                        overflow: hidden;
                        border: 3px solid #d4af37;
                        animation: modalSlideIn 0.3s ease-out;
                    ">
                        <!-- Italian flag accent -->
                        <div style="
                            background: linear-gradient(90deg, #009246, #ce2b37);
                            height: 6px;
                        "></div>
                        
                        <!-- Header -->
                        <div style="
                            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                            color: white;
                            padding: 1rem 1.5rem;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            gap: 0.75rem;
                        ">
                            <span style="font-size: 1.5rem;">🍕</span>
                            <h3 style="margin: 0; font-family: 'Oswald', sans-serif; font-size: 1.2rem; text-transform: uppercase;">Il Napolitano</h3>
                        </div>
                        
                        <!-- Body -->
                        <div style="padding: 2rem 1.5rem; text-align: center;">
                            <div style="margin-bottom: 1rem;">
                                <i class="fas fa-check-circle" style="font-size: 3rem; color: #009246;"></i>
                            </div>
                            <h4 style="
                                font-family: 'Oswald', sans-serif;
                                font-size: 1.5rem;
                                color: #2c3e50;
                                margin: 0 0 0.75rem 0;
                                text-transform: uppercase;
                            ">¡Éxito!</h4>
                            <p style="
                                font-size: 1rem;
                                color: #555;
                                margin-bottom: 1.5rem;
                                line-height: 1.4;
                            ">Producto insertado correctamente</p>
                        </div>
                        
                        <!-- Footer -->
                        <div style="
                            padding: 1rem 1.5rem 1.5rem;
                            display: flex;
                            justify-content: center;
                            background: linear-gradient(to bottom, transparent 0%, rgba(212, 175, 55, 0.05) 100%);
                        ">
                            <button onclick="redirectToInsert()" style="
                                background: linear-gradient(135deg, #009246 0%, #27ae60 100%);
                                color: white;
                                border: 2px solid #009246;
                                border-radius: 6px;
                                padding: 0.75rem 1.5rem;
                                font-family: 'Oswald', sans-serif;
                                font-weight: 600;
                                cursor: pointer;
                                text-transform: uppercase;
                                font-size: 1rem;
                                min-width: 150px;
                            ">
                                <i class="fas fa-check"></i> Continuar
                            </button>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes modalSlideIn {
                        from {
                            opacity: 0;
                            transform: scale(0.8) translateY(20px);
                        }
                        to {
                            opacity: 1;
                            transform: scale(1) translateY(0);
                        }
                    }
                </style>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        function redirectToInsert() {
            window.location.href = 'insert.php';
        }
        
        // Show the modal when page loads
        $(document).ready(function() {
            showSuccessModal();
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                redirectToInsert();
            }
        });
    </script>
</body>

</html>