# 🍕 Il Napoli## 📑 Tabla de Contenidos

- [Descripción](#-descripción)
- [Tecnologías Utilizadas](#-tecnologías-utilizadas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Funcionalidades](#-funcionalidades)
- [Sistema de Pedidos Online](#-sistema-de-pedidos-online---totalmente-funcional)
- [Base de Datos](#️-base-de-datos)
- [Instalación](#️-instalación)
- [Guía de Inicio Rápido](#-guía-de-inicio-rápido)
- [Características Técnicas](#-características-técnicas)
- [Roadmap](#-roadmap---futuras-mejoras)
- [Solución de Problemas](#-solución-de-problemas)
- [Contribuciones](#-contribuciones)

## 📸 Vista Previa

> **Nota**: Agregar screenshots de las siguientes secciones:
> - 🏠 Catálogo de productos (página principal)
> - 🛒 Carrito de compras en acción
> - 💳 Proceso de checkout
> - ✅ Página de confirmación de pedido
> - 📦 Rastreo de pedidos en tiempo real
> - 👨‍💼 Panel de administración de productos
> - 📊 Panel de gestión de pedidos con estadísticas

## 📋 Descripciónma Integral de Pizzería Online

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

Una aplicación web completa para pizzería con sistema de gestión de productos, carrito de compras, procesamiento de pedidos en tiempo real, autenticación de usuarios y panel de administración. Desarrollada con PHP, MySQL, JavaScript y diseño responsive.

[![Estado del Proyecto](https://img.shields.io/badge/Estado-Activo-success?style=flat-square)]()
[![Licencia](https://img.shields.io/badge/Licencia-Educational-blue?style=flat-square)]()
[![Última Actualización](https://img.shields.io/badge/Última%20Actualización-Oct%202025-green?style=flat-square)]()

## � Vista Previa

> **Nota**: Agregar screenshots de las siguientes secciones:
> - Catálogo de productos (página principal)
> - Carrito de compras en acción
> - Proceso de checkout
> - Página de confirmación de pedido
> - Panel de administración de productos
> - Panel de gestión de pedidos
> - Rastreo de pedidos en tiempo real

## �📋 Descripción

Il Napolitano es una **aplicación web de e-commerce completa** que ofrece una experiencia integral tanto para clientes como para administradores. Los clientes pueden explorar el catálogo de productos, gestionar su carrito de compras, realizar pedidos online con múltiples métodos de pago y rastrear sus pedidos en tiempo real. Los administradores tienen acceso a un potente panel de gestión para administrar productos, procesar pedidos y controlar el flujo completo del negocio.

### 🌟 Características Destacadas

| Categoría | Características |
|-----------|----------------|
| 🛒 **E-Commerce** | Carrito persistente, checkout completo, códigos promocionales |
| � **Pedidos** | Gestión en tiempo real, rastreo con timeline, múltiples estados |
| 💳 **Pagos** | Efectivo, tarjeta, transferencia, Mercado Pago (próximamente) |
| �‍💼 **Administración** | CRUD completo, panel de pedidos, estadísticas en tiempo real |
| 🔐 **Seguridad** | Autenticación segura, PDO prepared statements, hashing SHA-256 |
| 🎨 **Diseño** | Responsive, animaciones suaves, UX optimizada |
| � **Analytics** | Métricas de pedidos, ventas diarias, estadísticas completas |
| 🏗️ **Arquitectura** | POO, patrón MVC, código modular y escalable |

## 🚀 Tecnologías Utilizadas

### Frontend
- **HTML5** - Estructura semántica
- **CSS3** - Estilos personalizados y diseño responsive
- **JavaScript (Vanilla)** - Lógica del carrito de compras
- **Animate.css** - Animaciones y transiciones
- **Font Awesome 6.7** - Iconografía
- **Google Fonts** - Tipografía (Oswald & Work Sans)

### Backend
- **PHP 8.x** - Lógica del servidor
- **MySQL** - Base de datos relacional
- **PDO** - Capa de abstracción de base de datos
- **Sessions** - Manejo de autenticación

### Arquitectura y Patrones
- **MVC Pattern** - Separación de responsabilidades (Modelo-Vista-Controlador)
- **OOP (POO)** - Programación orientada a objetos con herencia y encapsulación
- **Singleton Pattern** - Gestión de conexión única a base de datos
- **PDO Prepared Statements** - Prevención de SQL injection
- **Password Hashing (SHA-256)** - Seguridad de contraseñas
- **RESTful API Design** - Endpoints lógicos y organizados

## 📁 Estructura del Proyecto

```
Project-Il-Napolitano/
├── assets/
│   ├── css/
│   │   ├── style.css          # Estilos principales del sitio
│   │   ├── edit.css           # Estilos del panel admin
│   │   ├── checkout.css       # Estilos del proceso de checkout
│   │   ├── order-success.css  # Estilos de confirmación
│   │   ├── track-order.css    # Estilos de rastreo de pedidos
│   │   └── manage-orders.css  # Estilos de gestión de pedidos
│   ├── images/                # Imágenes de productos (pizzas, bebidas, etc.)
│   └── js/
│       ├── main.js            # Lógica del carrito de compras
│       ├── checkout.js        # Validación y proceso de pago
│       └── manage-orders.js   # Gestión de pedidos (admin)
├── public/
│   ├── index.php              # Página principal (catálogo)
│   ├── login.html             # Autenticación de administradores
│   ├── checkout.php           # Página de checkout y pago
│   ├── order_success.php      # Confirmación de pedido
│   ├── track_order.php        # Rastreo de pedidos en tiempo real
│   └── pages/
│       ├── nosotros.html      # Acerca de la pizzería
│       ├── sucursales.html    # Ubicaciones y horarios
│       └── contacto.html      # Formulario de contacto
├── src/
│   ├── classes/
│   │   ├── db.class.php        # Conexión PDO con singleton pattern
│   │   ├── products.class.php  # Modelo de productos
│   │   ├── discount.class.php  # Productos con descuento (herencia)
│   │   ├── upload.class.php    # Gestión de archivos
│   │   ├── order.class.php     # Modelo de pedidos
│   │   ├── orderItem.class.php # Items individuales de pedidos
│   │   └── orderManager.class.php # Gestión y procesamiento de pedidos
│   ├── config/
│   │   └── database.php        # Configuración de base de datos
│   └── handlers/
│       ├── login.php           # Autenticación de usuarios
│       ├── logout.php          # Cerrar sesión
│       ├── welcome.php         # Panel de administración
│       ├── insert.php          # Crear productos (CRUD)
│       ├── edit.php            # Formulario de edición
│       ├── update_Product.php  # Actualizar productos
│       ├── delete.php          # Eliminar productos
│       ├── save_products.php   # Guardar productos
│       ├── process_order.php   # Procesar nuevos pedidos
│       ├── manage_orders.php   # Panel de gestión de pedidos
│       └── get_order_details.php # API para detalles de pedidos
└── docs/
    ├── queries.txt             # Scripts SQL para BD
    ├── urls.txt                # URLs del sistema
    ├── SYSTEM_FLOW_DIAGRAMS.md # Diagramas de flujo
    └── wireframe_index.pdf     # Diseño wireframe
```

## ✨ Funcionalidades

### Para Clientes
- **Catálogo de Productos**: Visualización dinámica de pizzas con precios e imágenes
- **Carrito de Compras**: 
  - Agregar productos con un clic
  - Prevención de productos duplicados
  - Contador visual de items
  - Cálculo automático del total
  - Badge animado con notificaciones
- **Navegación**: Secciones de nosotros, sucursales y contacto
- **Diseño Responsive**: Adaptado para móviles, tablets y desktop

### Para Administradores
- **Sistema de Login**:
  - Autenticación segura con hash SHA-256
  - Validación de credenciales contra MySQL
  - Manejo de sesiones PHP
  - Protección contra acceso no autorizado
  
- **Panel de Gestión de Productos** (CRUD completo):
  - ✅ **Crear**: Agregar nuevos productos con imágenes
  - 📝 **Leer**: Visualizar listado de productos
  - ✏️ **Actualizar**: Editar información y precios
  - 🗑️ **Eliminar**: Remover productos del catálogo
  - **Gestión de Imágenes**: Upload y validación de archivos
  - **Categorización**: Organización por tipo de producto

- **Panel de Gestión de Pedidos**:
  - 📊 **Dashboard con Estadísticas**: Métricas de pedidos y ventas
  - 🔄 **Actualización de Estados**: Workflow completo (pendiente → preparando → enviado → entregado)
  - 💰 **Gestión de Pagos**: Control de estados de pago
  - 🔍 **Filtros Avanzados**: Por estado, método de pago, fecha
  - 📄 **Vista Detallada**: Modal con información completa del pedido
  - 📈 **Métricas en Tiempo Real**: Total de pedidos, monto total, etc.

## 🗄️ Base de Datos

### Configuración
- **Servidor**: localhost (XAMPP)
- **Usuario**: root
- **Contraseña**: (vacío)
- **Base de datos**: `il-napolitano`

### Tablas Principales

#### Gestión de Productos
- **products**: Catálogo de productos con precios e imágenes
- **categories**: Categorías de productos (pizzas, bebidas, empanadas, etc.)

#### Gestión de Usuarios
- **users**: Usuarios administradores con credenciales

#### Sistema de Pedidos (Nuevas)
- **orders**: Pedidos de clientes con información completa
  - Campos: ID, cliente, dirección, teléfono, email, método de pago, estado, fecha
- **order_items**: Items individuales de cada pedido
  - Campos: ID, order_id, product_id, cantidad, precio unitario, subtotal
- **order_status_history**: Historial de cambios de estado (para auditoría)

### Scripts SQL
Ver archivo `docs/queries.txt` para scripts SQL completos de creación e inserción de datos.

## 🛠️ Instalación

### Requisitos Previos
- **XAMPP** (o LAMP/WAMP/MAMP)
- **PHP** >= 8.0
- **MySQL** >= 5.7
- **Navegador web moderno** (Chrome, Firefox, Edge, Safari)

### Pasos de Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   git clone <repository-url>
   # O descargar y extraer el ZIP
   ```

2. **Mover a la carpeta de XAMPP**
   ```bash
   # Copiar a: C:\xampp\htdocs\Project-Il-Napolitano
   ```

3. **Iniciar servicios de XAMPP**
   - Abrir el Panel de Control de XAMPP
   - Iniciar **Apache** y **MySQL**

4. **Crear la base de datos**
   - Abrir phpMyAdmin: http://localhost/phpmyadmin
   - Crear una nueva base de datos llamada `il-napolitano`
   - Importar el archivo SQL desde `docs/queries.txt`

5. **Configurar la conexión**
   - Verificar las credenciales en `src/config/database.php`:
   ```php
   $host = 'localhost';
   $dbname = 'il-napolitano';
   $username = 'root';
   $password = ''; // Vacío por defecto en XAMPP
   ```

6. **Acceder a la aplicación**
   - **Frontend**: http://localhost/Project-Il-Napolitano/public/index.php
   - **Admin Login**: http://localhost/Project-Il-Napolitano/public/login.html

### Credenciales de Prueba (Admin)
- **Usuario**: admin (o el definido en la BD)
- **Contraseña**: admin123 (hash SHA-256 almacenado)

## 🚀 Guía de Inicio Rápido

### Para Clientes (Frontend)
1. Navega a `http://localhost/Project-Il-Napolitano/public/index.php`
2. Explora el catálogo de productos
3. Haz clic en "Agregar al carrito" en los productos deseados
4. Haz clic en el carrito y luego en "Proceder al checkout"
5. Completa el formulario con tus datos
6. Selecciona tu método de pago preferido
7. Confirma tu pedido y obtén tu número de orden
8. Rastrea tu pedido en tiempo real con el número recibido

### Para Administradores (Backend)
1. Inicia sesión en `http://localhost/Project-Il-Napolitano/public/login.html`
2. Accede al panel de administración
3. **Gestionar Productos**: CRUD completo desde el panel principal
4. **Gestionar Pedidos**: Accede desde el menú o directamente a `manage_orders.php`
5. **Actualizar Estados**: Haz clic en los botones de estado para cambiarlos
6. **Ver Detalles**: Haz clic en "Ver Detalles" para información completa
7. **Filtrar Pedidos**: Usa los filtros por estado, pago o fecha

## 🎨 Características Técnicas

### Seguridad
- ✅ Prepared Statements (PDO) contra SQL Injection
- ✅ Hashing de contraseñas (SHA-256)
- ✅ Validación de sesiones
- ✅ Sanitización de inputs con `htmlspecialchars()`
- ✅ Validación de archivos en uploads

### Programación Orientada a Objetos (POO)
- **Clase `Db`**: Conexión PDO singleton con manejo de excepciones
- **Clase `Product`**: Modelo de producto con encapsulación y métodos CRUD
- **Clase `Discount`**: Herencia para productos con descuento
- **Clase `Upload`**: Gestión y validación de archivos subidos
- **Clase `Order`**: Modelo de pedido con propiedades encapsuladas
- **Clase `OrderItem`**: Representación de items individuales del pedido
- **Clase `OrderManager`**: Lógica de negocio para gestión de pedidos

### JavaScript Moderno
- **Carrito de Compras**: localStorage persistente con gestión de estado
- **Validación en Cliente**: Formularios con validación en tiempo real
- **AJAX/Fetch API**: Comunicación asíncrona con el servidor
- **Event Listeners Dinámicos**: Manejo de eventos del DOM
- **Animaciones**: Feedback visual y transiciones suaves
- **Auto-refresh**: Actualización automática de estados de pedidos

## 🎉 Sistema de Pedidos Online - Totalmente Funcional

### ✅ Características Implementadas

El sistema de pedidos online está **100% operativo** con las siguientes funcionalidades:

#### 🛒 Para Clientes

**Carrito de Compras:**
- ✅ Agregar/eliminar productos dinámicamente
- ✅ Persistencia con localStorage (mantiene items al recargar)
- ✅ Contador visual de items en badge animado
- ✅ Cálculo automático de totales y subtotales
- ✅ Prevención de productos duplicados

**Proceso de Checkout:**
- ✅ Formulario completo con validación en cliente y servidor
- ✅ Campos: nombre, dirección, teléfono, email, método de pago
- ✅ Múltiples métodos de pago disponibles
- ✅ Resumen del pedido con desglose de precios
- ✅ Sistema de códigos promocionales y descuentos

**Confirmación y Rastreo:**
- ✅ Página de confirmación con número de pedido único
- ✅ Detalles completos del pedido y datos de entrega
- ✅ Rastreo en tiempo real con timeline visual
- ✅ Auto-refresh para actualización de estados
- ✅ Estados: Pendiente → Preparando → Enviado → Entregado

#### 👨‍💼 Para Administradores

**Panel de Gestión de Pedidos:**
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Métricas: total de pedidos, pedidos del día, monto total
- ✅ Tabla de pedidos con información detallada
- ✅ Filtros por estado, método de pago y fecha
- ✅ Actualización de estados con un clic
- ✅ Gestión de estados de pago
- ✅ Modal de detalles con información completa del pedido
- ✅ Vista de productos ordenados por el cliente

**Workflow de Pedidos:**
```
📝 Pendiente → 👨‍🍳 Preparando → 🚚 Enviado → ✅ Entregado → ❌ Cancelado
```

#### 💳 Métodos de Pago Soportados

| Método | Estado | Descripción |
|--------|--------|-------------|
| 💵 Efectivo | ✅ Activo | Pago contra entrega |
| 💳 Tarjeta | ✅ Activo | Tarjeta débito/crédito en el momento |
| 🏦 Transferencia | ✅ Activo | Transferencia bancaria |
| 💰 Mercado Pago | 🔜 Próximamente | Integración con API externa |

### � URLs del Sistema

| Página | URL | Descripción |
|--------|-----|-------------|
| Catálogo | `/public/index.php` | Tienda online con productos |
| Checkout | `/public/checkout.php` | Proceso de pago |
| Confirmación | `/public/order_success.php` | Confirmación de pedido |
| Rastreo | `/public/track_order.php` | Seguimiento en tiempo real |
| Admin Login | `/public/login.html` | Acceso administradores |
| Gestión Pedidos | `/src/handlers/manage_orders.php` | Panel admin de pedidos |
| Gestión Productos | `/src/handlers/welcome.php` | CRUD de productos |

### � Flujo Completo del Sistema

1. **Cliente navega el catálogo** → Agrega productos al carrito
2. **Procede al checkout** → Completa formulario de datos
3. **Selecciona método de pago** → Confirma el pedido
4. **Recibe número de orden** → Puede rastrear su pedido
5. **Admin recibe notificación** → Actualiza estados del pedido
6. **Cliente recibe actualizaciones** → Ve progreso en tiempo real
7. **Pedido completado** → Cliente recibe su orden

## 🚧 Roadmap - Futuras Mejoras

### 🔥 Alta Prioridad
- [ ] **Integración Mercado Pago API**: Pagos online seguros
- [ ] **Notificaciones por Email**: Confirmaciones automáticas
- [ ] **Sistema de Usuarios**: Registro y login de clientes
- [ ] **Historial de Pedidos**: Dashboard para clientes registrados

### 📱 Mejoras UX/UI
- [ ] **Progressive Web App (PWA)**: Instalable en móviles
- [ ] **Dark Mode**: Tema oscuro opcional
- [ ] **Sistema de Calificaciones**: Reseñas de productos
- [ ] **Chat en Vivo**: Soporte en tiempo real

### 🚀 Funcionalidades Avanzadas
- [ ] **API REST**: Para aplicación móvil nativa
- [ ] **Sistema de Roles**: Admin, cajero, delivery, cocina
- [ ] **Rastreo GPS**: Ubicación en tiempo real del delivery
- [ ] **Notificaciones Push**: Actualizaciones instantáneas
- [ ] **Programa de Lealtad**: Sistema de puntos y recompensas
- [ ] **Reportes Analytics**: Dashboards con métricas avanzadas

### 📊 Analytics y Métricas
- [ ] **Dashboard de Ventas**: Gráficos y estadísticas
- [ ] **Productos más vendidos**: Rankings y tendencias
- [ ] **Análisis de clientes**: Comportamiento de compra
- [ ] **Exportación de datos**: CSV, Excel, PDF

### 🔐 Seguridad
- [ ] **Two-Factor Authentication (2FA)**: Seguridad adicional
- [ ] **Rate Limiting**: Prevención de ataques
- [ ] **HTTPS**: Certificado SSL
- [ ] **Logs de Auditoría**: Registro completo de acciones

## 🔧 Solución de Problemas

### Problema: No se conecta a la base de datos
- ✅ Verifica que MySQL esté corriendo en XAMPP
- ✅ Confirma que la BD `il-napolitano` existe
- ✅ Revisa las credenciales en `src/config/database.php`

### Problema: Error 404 en las páginas
- ✅ Verifica que Apache esté corriendo en XAMPP
- ✅ Confirma la ruta correcta: `http://localhost/Project-Il-Napolitano/public/`
- ✅ Revisa que los archivos estén en `C:\xampp\htdocs\`

### Problema: Las imágenes no cargan
- ✅ Verifica que existan en `assets/images/`
- ✅ Revisa los permisos de la carpeta
- ✅ Confirma las rutas en la base de datos

### Problema: El carrito se vacía al recargar
- ✅ Verifica que JavaScript esté habilitado en el navegador
- ✅ Revisa la consola del navegador (F12) para errores
- ✅ Limpia el caché y cookies del navegador

### Problema: No puedo iniciar sesión como admin
- ✅ Verifica que el usuario exista en la tabla `users`
- ✅ Confirma que la contraseña esté hasheada con SHA-256
- ✅ Revisa los logs de PHP en XAMPP para errores

## 🤝 Contribuciones

Este proyecto está abierto a contribuciones. Si deseas colaborar:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Guías de Contribución
- Sigue los estándares de código PSR-12 para PHP
- Usa nombres descriptivos para variables y funciones
- Comenta código complejo
- Asegúrate de que el código pase todas las pruebas
- Actualiza la documentación si es necesario

## � Documentación Adicional

### Archivos de Documentación
- 📄 **`docs/queries.txt`**: Scripts SQL completos para crear la base de datos
- 📄 **`docs/SYSTEM_FLOW_DIAGRAMS.md`**: Diagramas de flujo del sistema
- 📄 **`docs/urls.txt`**: Lista de todas las URLs del sistema
- 📄 **`docs/wireframe_index.pdf`**: Diseño wireframe de la interfaz

### Endpoints API (Handlers)

| Endpoint | Método | Descripción |
|----------|--------|-------------|
| `/src/handlers/login.php` | POST | Autenticación de usuarios |
| `/src/handlers/logout.php` | GET | Cerrar sesión |
| `/src/handlers/process_order.php` | POST | Procesar nuevo pedido |
| `/src/handlers/get_order_details.php` | GET | Obtener detalles de pedido |
| `/src/handlers/save_products.php` | POST | Guardar productos (CRUD) |
| `/src/handlers/update_Product.php` | POST | Actualizar producto |
| `/src/handlers/delete.php` | POST | Eliminar producto |

## �📝 Licencia

Este proyecto es de código abierto para fines educativos.

## 👨‍💻 Autor

Desarrollado con ❤️ para Il Napolitano Pizzería

## 📞 Contacto

Para consultas o soporte, visita la sección de contacto en la aplicación web.

---

### 📊 Estadísticas del Proyecto

- **Total de Archivos PHP**: ~15
- **Total de Archivos JS**: 3
- **Total de Archivos CSS**: 6
- **Clases POO**: 7
- **Páginas Públicas**: 6
- **Handlers/API**: 12
- **Tablas de BD**: 6

---

**⭐ Si te gusta este proyecto, dale una estrella en GitHub!**

**🐛 ¿Encontraste un bug?** Abre un issue y lo revisaremos

**💡 ¿Tienes una idea?** Las sugerencias son bienvenidas


