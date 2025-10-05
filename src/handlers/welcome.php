<?php
session_start();

// Check authentication
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: ../../public/login.html');
    exit();
}

$products = [];
$error = '';

try {
    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    $link = new Db();
    
    // Load products with categories
    $sql = "SELECT p.id_product, p.product_name, c.category_name, p.price, p.start_date 
            FROM products p 
            INNER JOIN categories c ON p.id_category = c.id_category 
            ORDER BY p.id_product DESC";
    $stmt = $link->run($sql);
    $products = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Welcome page error: " . $e->getMessage());
    $error = 'Error al cargar los productos';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Il Napolitano</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="../../assets/images/pizza.svg">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

</head>

<body>
    <nav class="navtop">
        <div>
            <h1>Panel Administrador</h1>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </nav>
    
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>Bienvenido/a, <?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
                        Horario de Conexión: <?php echo htmlspecialchars($_SESSION['time']); ?>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="insert.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> INSERTAR PRODUCTOS
                        </a>
                        <a href="../../public/index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> VER SITIO WEB
                        </a>
                        <a href="manage_orders.php" class="btn btn-secondary">
                            <i class="fas fa-receipt"></i> Gestión de Pedidos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Session Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Éxito:</strong> <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="ourTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Fecha de Alta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id_product']; ?></td>
                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($product['start_date'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="updateProduct(<?php echo $product['id_product']; ?>)">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteProduct(<?php echo $product['id_product']; ?>)">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>

    </div>
    <script>
        let table = new DataTable('#ourTable', {
            info: false,
            ordering: true,
            paging: false,
            // Descargar el archivo es-MX.json desde la pagina: https://datatables.net/plug-ins/i18n/Spanish_Argentina.html
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/es-AR.json',
            },
        });

        function deleteProduct(cod) {

            bootbox.confirm("Desea ud. eliminar realmente el id " + cod, function(result) {
                if (result) {
                    window.location = "delete.php?q=" + cod;
                }
            });

        }

        function updateProduct(cod) {

            window.location = "edit.php?q=" + cod;

        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js" integrity="sha512-oVbWSv2O4y1UzvExJMHaHcaib4wsBMS5tEP3/YkMP6GmkwRJAa79Jwsv+Y/w7w2Vb/98/Xhvck10LyJweB8Jsw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>