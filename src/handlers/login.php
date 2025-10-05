<?php
session_start();

// Check if already logged in
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header('Location: welcome.php');
    exit();
}

$error = '';
$showForm = true;

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['username']) || empty($_POST['password'])) {
            throw new Exception('Por favor complete todos los campos');
        }
        
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        // Validate username length and format
        if (strlen($username) < 3) {
            throw new Exception('El nombre de usuario debe tener al menos 3 caracteres');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('La contraseña debe tener al menos 6 caracteres');
        }
        
        include_once("../config/database.php");
        include_once("../classes/db.class.php");
        
        $link = new Db();
        $hashed_pass = hash('sha256', $password);
        
        // Check user credentials
        $sql = "SELECT username, email FROM users WHERE (username=? OR email=?) AND password=? AND active='SI'";
        $stmt = $link->run($sql, [$username, $username, $hashed_pass]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('Credenciales inválidas');
        }
        
        // Successful login - set session variables
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logueado'] = true;
        $_SESSION['time'] = date('H:i:s');
        $_SESSION['login_time'] = time();
        
        // Redirect to dashboard
        header('Location: welcome.php');
        exit();
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Il Napolitano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body class="login-page-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h2 class="mb-0">
                            <i class="fas fa-pizza-slice me-2"></i>
                            Il Napolitano
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Panel de Administración</p>
                    </div>
                    
                    <div class="login-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" autocomplete="on">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i> Usuario o Email
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                       required 
                                       autocomplete="username"
                                       placeholder="Ingrese su usuario o email">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Ingrese su contraseña">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Iniciar Sesión
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Acceso seguro al sistema
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Auto-dismiss alerts after 8 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-permanent')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
        
        // Focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input[name="username"]');
            if (firstInput && !firstInput.value) {
                firstInput.focus();
            }
        });
        
        // Form validation feedback
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByTagName('form');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>

</html>