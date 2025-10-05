<?php
session_start();
if ($_SESSION['logueado']) {
    include_once("../config/database.php");
    include_once("../classes/db.class.php");
    include_once("../classes/upload.class.php");
    
    $link = new Db();
    $id = $_POST['id'];
    $name = $_POST['nombre'];
    $price = $_POST['precio'];
    $category = $_POST['categoria'];
    $fechaing = $_POST['fecha'];
    
    // Handle image upload
    $imageing = $_POST['current_image']; // Keep current image by default
    $uploadError = false;
    $uploadMessage = '';
    
    // Check if a new image was uploaded
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        try {
            $upload = new Upload();
            $newImageName = $upload->uploadImage(); // Upload new image
            
            if (!empty($newImageName)) {
                // Delete old image if it exists and is different from the new one
                $oldImagePath = dirname(dirname(__DIR__)) . "/assets/images/" . basename($_POST['current_image']);
                if (!empty($_POST['current_image']) && file_exists($oldImagePath) && basename($_POST['current_image']) != $newImageName) {
                    unlink($oldImagePath);
                }
                $imageing = $newImageName;
                $uploadMessage = "Imagen actualizada correctamente";
            }
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Upload error: " . $e->getMessage());
            $uploadError = true;
            $uploadMessage = "Error al subir la imagen: " . $e->getMessage();
            // If upload fails, keep the current image
            $imageing = $_POST['current_image'];
        }
    } else if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] != 4) {
        // Handle upload errors (error code 4 means no file was uploaded)
        $uploadErrors = [
            1 => "El archivo es demasiado grande (límite del servidor)",
            2 => "El archivo es demasiado grande (límite del formulario)",
            3 => "El archivo se subió parcialmente",
            6 => "Falta el directorio temporal",
            7 => "Error al escribir el archivo en disco",
            8 => "Extensión de PHP detuvo la subida"
        ];
        $uploadError = true;
        $uploadMessage = isset($uploadErrors[$_FILES['imagen']['error']]) 
            ? $uploadErrors[$_FILES['imagen']['error']] 
            : "Error desconocido al subir archivo";
        error_log("Upload error code: " . $_FILES['imagen']['error']);
    }
    
    // Store upload status in session for feedback
    if ($uploadError) {
        $_SESSION['upload_error'] = $uploadMessage;
    } else if (!empty($uploadMessage)) {
        $_SESSION['upload_success'] = $uploadMessage;
    }
    
    $sql = "update products set product_name=?, price=?, id_category=?, start_date=?, image=? where id_product=?";
    $stmt = $link->run($sql, [$name, $price, $category, $fechaing, $imageing, $id]);
    header('Location:welcome.php');
    exit();
} else {
    // Not logged in
    header('Location: ../../public/login.html');
    exit();
}
?>