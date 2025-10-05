<?php

class Upload
{

    private $imagesFolder;

    public function  uploadImage()
    {
        // Set the correct absolute path to the images directory
        $this->imagesFolder = dirname(dirname(__DIR__)) . "/assets/images/";
        
        $nombre_img = $_FILES['imagen']['name'];
        $tipo = $_FILES['imagen']['type'];
        $tamano = $_FILES['imagen']['size'];
        //Si existe imagen y tiene un tamaño correcto
        if (($nombre_img == !NULL) && ($_FILES['imagen']['size'] <= 2000000)) {
            //indicamos los formatos que permitimos subir a nuestro servidor
            if (($_FILES["imagen"]["type"] == "image/gif")
                || ($_FILES["imagen"]["type"] == "image/jpeg")
                || ($_FILES["imagen"]["type"] == "image/jpg")
                || ($_FILES["imagen"]["type"] == "image/png")
            ) {
                // Ruta donde se guardarán las imágenes que subamos
                $directorio = $this->imagesFolder;
                
                // Create directory if it doesn't exist
                if (!is_dir($directorio)) {
                    mkdir($directorio, 0755, true);
                }
                
                // Generate unique filename to avoid conflicts
                $fileInfo = pathinfo($nombre_img);
                $baseName = $fileInfo['filename'];
                $extension = $fileInfo['extension'];
                $uniqueFileName = $baseName . '_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Check if file already exists and create unique name if needed
                $counter = 1;
                $finalFileName = $uniqueFileName;
                while (file_exists($directorio . $finalFileName)) {
                    $finalFileName = $baseName . '_' . time() . '_' . uniqid() . '_' . $counter . '.' . $extension;
                    $counter++;
                }
                
                // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio . $finalFileName)) {
                    // Upload successful
                } else {
                    throw new Exception("Error al subir la imagen al directorio: " . $directorio);
                }
                
                // Return the unique filename to store in database
                return $finalFileName;
            } else {
                //si no cumple con el formato
                exit("No se puede subir una imagen con ese formato ");
            }
        } else {
            //si existe la variable pero se pasa del tamaño permitido   
            if ($nombre_img == !NULL) exit("La imagen es demasiado grande ");
        }
        // This should never be reached, but return empty string as fallback
        return '';
    }
}
