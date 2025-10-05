<?php
session_start();

// Check authentication
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: ../login.html');
    exit();
}

// Validate input
if (!isset($_GET['q']) || !is_numeric($_GET['q'])) {
    $_SESSION['error'] = 'ID de producto inválido';
    header('Location: welcome.php');
    exit();
}

try {
    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    
    $link = new Db();
    $idDel = (int)$_GET['q'];
    
    // Use prepared statement for security
    $sql = "DELETE FROM products WHERE id_product = ?";
    $stmt = $link->run($sql, [$idDel]);
    
    $_SESSION['success'] = 'Producto eliminado correctamente';
    header('Location: welcome.php');
    exit();
    
} catch (Exception $e) {
    error_log("Delete error: " . $e->getMessage());
    $_SESSION['error'] = 'Error al eliminar el producto';
    header('Location: welcome.php');
    exit();
}
?>