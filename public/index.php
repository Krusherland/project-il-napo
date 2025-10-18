<?php
// PHP Logic - Data Processing
include_once("../src/config/database.php");
include_once("../src/classes/db.class.php");

$link = new Db();
$sql = "select p.id_product,c.category_name,p.image,p.product_name,p.price, date_format(p.start_date,'%d/%m/%Y') as date from products p inner join categories c 
on p.id_category=c.id_category order by c.category_name,p.price";
$stmt = $link->run($sql);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/svg+xml" href="../assets/images/pizza.svg">
    <title>Pizzeria Pizze il Napolitano</title>
    <meta name="description" content="Las mejores pizzas de San Martín, Buenos Aires. Masa artesanal, 
        ingredientes frescos y sabores únicos. Pedí online con delivery rápido a toda la zona norte del GBA.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
        integrity="sha512-c42qTSw/wPZ3/5LBzD+Bw5f7bSF2oxou6wEb+I/lqeaKV5FDIfMvvRp772y4jcJLKuGUOpbJMdg/BTl50fJYAw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <header>
        <div id="header-container">
            <div id="logo">
                <a href="index.php"><img src="../assets/images/pizza.svg" alt="logo napolitano"></a>
                <a href="index.php"><img class="logo-text" src="../assets/images/text.svg" alt="nombre pizzeria"></a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="./pages/nosotros.html">NOSOTROS</a></li>
                    <li><a href="./pages/sucursales.html">SUCURSALES & DELIVERY</a></li>
                    <li><a href="./pages/contacto.html">CONTACTO</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="main-content">
        <h2 class="animate__animated animate__rubberBand">Nuestras Pizzas</h2>
        <div id="cart">
            <div class="cart-container">
                <a href="checkout.php" class="fa badge" id="badge" value="0"><i class="fa-solid fa-cart-shopping fa-xl"></i></a>
                <div class="cart-tooltip">
                    <div class="cart-info">
                        <span class="cart-subtotal" id="cartSubtotal">Subtotal: $0.00</span>
                    </div>
                    <button class="clear-cart-btn" id="clearCartBtn">
                        <i class="fa-solid fa-trash"></i>
                        Vaciar Orden
                    </button>
                </div>
            </div>
        </div>
        <ul class="gallery">
            <?php foreach ($products as $product): ?>
                <li>
                    <div class="box">
                        <figure>
                            <img src="../assets/images/<?php echo basename($product['image']); ?>" 
                                 class="img-pizzas" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <figcaption>
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p><?php echo htmlspecialchars($product['price']); ?></p>
                                <time><?php echo htmlspecialchars($product['date']); ?></time>
                            </figcaption>
                        </figure>
                        <button class="button" 
                                value="<?php echo $product['id_product']; ?>" 
                                data-price="<?php echo $product['price']; ?>">
                            Añadir al carrito
                            <i class="fa-solid fa-cart-shopping fa-lg"></i>
                        </button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <footer>
        <div class="footer-content">
            <div class="footer-nav">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Nosotros</a></li>
                    <li><a href="#">Sucursales & Delivery</a></li>
                    <li><a href="#">Contacto</a></li>
                    <li><a href="login.html">Login</a></li>
                    <li><a href="#">Política de Privacidad</a></li>
                </ul>
            </div>
            <!-- Redes sociales -->
            <div class="footer-social" id="social">
                <a href="https://www.facebook.com/user"><i class="fab fa-facebook-f"></i></a>
                <a href="https://x.com/user"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/user"><i class="fab fa-instagram"></i></a>
            </div>
            <div class="footer-copyright">
                <p> &copy;
                    <script>
                        let currentYear = new Date().getFullYear();
                        document.write(currentYear);
                    </script> Pizzeria Pizze il Napolitano. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>

</html>