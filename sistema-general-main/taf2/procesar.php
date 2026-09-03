<?php
require_once __DIR__ . '/../includes/functions.php';
require_once 'controlador/controlador.php';

// INICIAR SESIÓN 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//
if (isset($_POST['action']) && $_POST['action'] == 'nuevo_usuario') {
    // Validar permisos - Solo admin puede crear usuarios
    if (!verificar_permiso('admin')) {
        redirigir('taf2/paginas/usuarios.php?error=No+tienes+permiso+para+crear+usuarios');
    }
    
    $nom_com = trim($_POST['nom_com']);
    $correo  = trim($_POST['correo']);
    $usu_con = $_POST['usu_con'];
    $id_rol  = (int)$_POST['id_rol'];
    $estado  = trim($_POST['estado']); // Captura el estado ('activo' o 'inactivo')

    if (empty($nom_com) || empty($correo) || empty($usu_con) || empty($id_rol) || empty($estado)) {
        redirigir('taf2/paginas/usuarios.php?error=Todos los campos son requeridos.');
    }

    try {
        // Verificar que el rol pertenezca al grupo permitido
        $checkRol = $pdo->prepare("SELECT nombre_rol FROM roles WHERE id_rol = ?");
        $checkRol->execute([$id_rol]);
        $rolResult = $checkRol->fetch();

        $roles_permitidos = ['inventario', 'gerente'];

        if (!$rolResult || !in_array($rolResult['nombre_rol'], $roles_permitidos, true)) {
            redirigir('taf2/paginas/usuarios.php?error=Rol no permitido para el personal.');
        }

        // Inserción incluyendo la columna estado
        $stmt = $pdo->prepare("INSERT INTO usuarios (nom_com, correo, usu_con, id_rol, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom_com, $correo, $usu_con, $id_rol, $estado]);

        redirigir('taf2/paginas/usuarios.php?msg=Usuario creado exitosamente con estado ' . $estado . '.');

    } catch (PDOException $e) {
        redirigir('taf2/paginas/usuarios.php?error=El correo electronico ya se encuentra registrado.');
    }
}
// --- DETECTAR ACCIONES POR MÉTODO GET (Cerrar Sesión, Eliminar) ---
if (isset($_GET['action']) && $_GET['action'] === 'cerrar_sesion') {
    // Limpiar variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión en el navegador
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destruir la sesión en el servidor y redirigir inmediatamente
    session_destroy();
    redirigir('taf2/login.php');
}

// Eliminar categoría (Solo Admin)
if (isset($_GET['action']) && $_GET['action'] === 'eliminar_categoria') {
    // Validar permisos
    if (!verificar_permiso('admin')) {
        redirigir('taf2/paginas/categorias.php?error=No+tienes+permiso+para+eliminar+categorías');
    }
    
    $id_cat = (int)$_GET['id_cat'];
    
    if ($id_cat <= 0) {
        redirigir('taf2/paginas/categorias.php?error=ID+de+categoría+no+válido');
    }
    
    try {
        // Verificar que la categoría exista
        $check = $pdo->prepare("SELECT id_cat FROM categorias WHERE id_cat = ?");
        $check->execute([$id_cat]);
        
        if (!$check->fetch()) {
            redirigir('taf2/paginas/categorias.php?error=Categoría+no+encontrada');
        }
        
        // Verificar que no haya productos en esta categoría
        $checkProducts = $pdo->prepare("SELECT COUNT(*) as count FROM producto WHERE id_cat = ?");
        $checkProducts->execute([$id_cat]);
        $result = $checkProducts->fetch();
        
        if ($result['count'] > 0) {
            redirigir('taf2/paginas/categorias.php?error=No+puedes+eliminar+esta+categoría+porque+tiene+productos+asociados');
        }
        
        // Eliminar la categoría
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_cat = ?");
        $stmt->execute([$id_cat]);
        
        redirigir('taf2/paginas/categorias.php?msg=Categoría+eliminada+exitosamente');
    } catch (PDOException $e) {
        redirigir('taf2/paginas/categorias.php?error=Error+al+eliminar+categoría');
    }
}

// --- DETECTAR ACCIONES POR MÉTODO POST (Formularios) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ACCIÓN 1: Registrar Producto (Solo Admin e Inventario)
    if (isset($_POST['action']) && $_POST['action'] === 'nuevo_producto') {
        // Validar permisos
        if (!verificar_permiso(['admin', 'inventario'])) {
            redirigir('taf2/paginas/productos.php?error=No+tienes+permiso+para+registrar+productos');
        }
        
        $nom_pro = trim($_POST['nom_pro']);
        $descripcion = trim($_POST['descripcion']);
        $precio = (float)$_POST['precio'];
        $stock = (int)$_POST['stock'];
        $id_cat = (int)$_POST['id_cat'];
        
        if (empty($nom_pro) || $precio <= 0 || $stock < 0 || $id_cat <= 0) {
            redirigir('taf2/paginas/productos.php?error=Datos+inválidos');
        }
        
        try {
            // Verificar que la categoría existe
            $checkCat = $pdo->prepare("SELECT id_cat FROM categorias WHERE id_cat = ? AND estado = 'activo'");
            $checkCat->execute([$id_cat]);
            
            if (!$checkCat->fetch()) {
                redirigir('taf2/paginas/productos.php?error=Categoría+no+válida');
            }
            
            $sql = "INSERT INTO producto (nom_pro, descripcion, precio, stock, id_cat) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom_pro, $descripcion, $precio, $stock, $id_cat]);
            redirigir('taf2/paginas/productos.php?msg=Producto+registrado+exitosamente');
        } catch (PDOException $e) {
            redirigir('taf2/paginas/productos.php?error=Error+al+registrar+producto');
        }
    }
    
    // ACCIÓN 2: Registrar Pedido (Venta de múltiples productos) - Admin e Inventario
    if (isset($_POST['action']) && $_POST['action'] === 'nuevo_pedido') {
        // Validar permisos
        if (!verificar_permiso(['admin', 'inventario'])) {
            redirigir('taf2/index.php?error=No+tienes+permiso+para+crear+pedidos');
        }
        
        $id_usu = (int)$_POST['id_usu'];
        $productos = $_POST['productos']; // Array con IDs de productos
        $cantidades = $_POST['cantidades']; // Array con cantidades
        
        try {
            // Iniciamos transacción para asegurar que se guarde todo o nada
            $pdo->beginTransaction();
            
            // 1. Calcular el total acumulado del pedido
            $total = 0;
            $items_validos = [];
            
            foreach ($productos as $index => $id_pro) {
                $cant = intval($cantidades[$index]);
                if ($id_pro && $cant > 0) {
                    $stmt = $pdo->prepare("SELECT precio FROM producto WHERE id_pro = ?");
                    $stmt->execute([$id_pro]);
                    $precio = $stmt->fetchColumn();
                    
                    if ($precio) {
                        $total += ($precio * $cant);
                        $items_validos[] = [
                            'id_pro' => $id_pro,
                            'cantidad' => $cant,
                            'precio' => $precio
                        ];
                    }
                }
            }
            
            if (empty($items_validos)) {
                throw new Exception("Debe seleccionar al menos un producto válido.");
            }
            
            // 2. Insertar el encabezado del pedido
            $sqlPed = "INSERT INTO pedido (id_usu, total, estado) VALUES (?, ?, 'pendiente')";
            $stmtPed = $pdo->prepare($sqlPed);
            $stmtPed->execute([$id_usu, $total]);
            $id_pedido_generado = $pdo->lastInsertId();
            
            // 3. Insertar el desglose en detalles_pedido
            $sqlDet = "INSERT INTO detalles_pedido (id_ped, id_pro, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmtDet = $pdo->prepare($sqlDet);
            
            foreach ($items_validos as $item) {
                $stmtDet->execute([
                    $id_pedido_generado,
                    $item['id_pro'],
                    $item['cantidad'],
                    $item['precio']
                ]);
            }
            
            $pdo->commit();
            redirigir('index.php?msg=Pedido+creado+con+exito');

        } catch (Exception $e) {
            $pdo->rollBack();
            redirigir('index.php?error=' . urlencode($e->getMessage()));
        }
    }

    // ACCIÓN 3: Iniciar Sesión
    if (isset($_POST['action']) && $_POST['action'] === 'iniciar_sesion') {
        $correo = trim($_POST['correo']);
        $password = trim($_POST['password']);
        
        // Buscamos al usuario y traemos el NOMBRE de su rol haciendo un JOIN con la tabla roles
        $stmt = $pdo->prepare("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();
        
        // Verificación de datos
        $storedPassword = (string)($usuario['usu_con'] ?? '');
        $passwordValido = password_verify($password, $storedPassword) || $storedPassword === $password;

        if ($usuario && $passwordValido) {
            // Guardamos los datos clave en la sesión
            $_SESSION['usuario_id'] = $usuario['id_usu'];
            $_SESSION['usuario'] = $usuario['nom_com'];
            $_SESSION['nom_com'] = $usuario['nom_com'];
            $_SESSION['correo'] = $correo;
            $_SESSION['usuario_rol'] = $usuario['nombre_rol']; // Guarda 'admin', 'vendedor' o 'gerente'
            
            redirigir('taf2/index.php');
        } else {
            redirigir('taf2/login.php?error=Usuario o contraseña incorrectos');
        }
    }

    // ACCIÓN 4: Crear Nueva Categoría (Solo Admin)
    if (isset($_POST['action']) && $_POST['action'] === 'nueva_categoria') {
        // Validar permisos
        if (!verificar_permiso('admin')) {
            redirigir('taf2/paginas/categorias.php?error=No+tienes+permiso+para+crear+categorías');
        }
        
        $nombre_cat = trim($_POST['nombre_cat']);
        $descripcion = trim($_POST['descripcion']);
        $estado = trim($_POST['estado']);
        
        if (empty($nombre_cat) || empty($estado)) {
            redirigir('taf2/paginas/categorias.php?error=El+nombre+de+la+categoría+es+requerido');
        }
        
        try {
            $sql = "INSERT INTO categorias (nombre_cat, descripcion, estado) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre_cat, $descripcion, $estado]);
            redirigir('taf2/paginas/categorias.php?msg=Categoría+creada+exitosamente');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                redirigir('taf2/paginas/categorias.php?error=La+categoría+ya+existe');
            }
            redirigir('taf2/paginas/categorias.php?error=Error+al+crear+categoría');
        }
    }
}
?>