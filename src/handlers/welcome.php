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

        function updateProduct(cod) {
            window.location = "edit.php?q=" + cod;
        }
    </script>

    <script>
        // Simple Il-Napolitano styled modal function
        function showSimpleModal(options) {
            // Remove existing modal
            const existing = document.getElementById('simpleModal');
            if (existing) existing.remove();
            
            // Create modal
            const modal = document.createElement('div');
            modal.id = 'simpleModal';
            modal.innerHTML = `
                <div style="
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
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
                        max-width: 400px;
                        width: 90%;
                        border: 3px solid #d4af37;
                    ">
                        <div style="
                            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                            color: white;
                            padding: 1rem 1.5rem;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-radius: 8px 8px 0 0;
                        ">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 1.5rem;">🍕</span>
                                <h3 style="margin: 0; font-family: 'Oswald', sans-serif; font-size: 1.2rem;">Il Napolitano</h3>
                            </div>
                            <button onclick="closeSimpleModal()" style="
                                background: none;
                                border: none;
                                color: white;
                                font-size: 1.8rem;
                                cursor: pointer;
                                padding: 0;
                                width: 32px;
                                height: 32px;
                            ">&times;</button>
                        </div>
                        
                        <div style="padding: 2rem 1.5rem; text-align: center;">
                            <div style="margin-bottom: 1rem;">
                                <i class="fas fa-trash-alt" style="font-size: 3rem; color: #ce2b37;"></i>
                            </div>
                            <h4 style="
                                font-family: 'Oswald', sans-serif;
                                font-size: 1.5rem;
                                color: #2c3e50;
                                margin: 0 0 0.75rem 0;
                            ">${options.title}</h4>
                            <p style="
                                font-size: 1rem;
                                color: #555;
                                margin-bottom: 1.5rem;
                                line-height: 1.4;
                            ">${options.message}</p>
                            
                            ${options.extraLabel ? `
                            <div style="
                                background: rgba(212, 175, 55, 0.1);
                                border: 2px solid #d4af37;
                                border-radius: 6px;
                                padding: 1rem;
                                margin-bottom: 1rem;
                                display: flex;
                                justify-content: space-between;
                            ">
                                <span style="font-weight: 600; color: #2c3e50;">${options.extraLabel}:</span>
                                <span style="font-weight: 700; color: #d4af37;">${options.extraInfo}</span>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div style="
                            padding: 1rem 1.5rem 1.5rem;
                            display: flex;
                            gap: 1rem;
                            justify-content: center;
                        ">
                            <button onclick="closeSimpleModal()" style="
                                background: white;
                                color: #6c757d;
                                border: 2px solid #dee2e6;
                                border-radius: 6px;
                                padding: 0.75rem 1.5rem;
                                font-family: 'Oswald', sans-serif;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 1rem;
                            ">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button onclick="confirmSimpleModal()" style="
                                background: linear-gradient(135deg, #ce2b37 0%, #e74c3c 100%);
                                color: white;
                                border: 2px solid #ce2b37;
                                border-radius: 6px;
                                padding: 0.75rem 1.5rem;
                                font-family: 'Oswald', sans-serif;
                                font-weight: 600;
                                cursor: pointer;
                                font-size: 1rem;
                            ">
                                <i class="fas fa-trash-can"></i> ${options.confirmText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            window.modalCallback = options.onConfirm;
        }
        
        function closeSimpleModal() {
            const modal = document.getElementById('simpleModal');
            if (modal) modal.remove();
            window.modalCallback = null;
        }
        
        function confirmSimpleModal() {
            if (window.modalCallback) window.modalCallback();
            closeSimpleModal();
        }

        // Delete function with simple modal
        function deleteProduct(cod) {
            showSimpleModal({
                title: '¿Eliminar Producto?',
                message: `¿Estás seguro de que quieres eliminar el producto con ID ${cod}?`,
                confirmText: 'Sí, eliminar',
                extraLabel: 'ID del Producto',
                extraInfo: cod,
                onConfirm: function() {
                    window.location = "delete.php?q=" + cod;
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>