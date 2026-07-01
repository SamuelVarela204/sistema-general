<?php
require_once 'controlador/controlador.php';

// INICIAR SESIÓN 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//
if (isset($_POST['action']) && $_POST['action'] == 'nuevo_usuario') {
    
    $nom_com = trim($_POST['nom_com']);
    $correo  = trim($_POST['correo']);
    $usu_con = $_POST['usu_con'];
    $id_rol  = (int)$_POST['id_rol'];
    $estado  = trim($_POST['estado']); // Captura el estado ('activo' o 'inactivo')

    if (empty($nom_com) || empty($correo) || empty($usu_con) || empty($id_rol) || empty($estado)) {
        header("Location: paginas/usuarios.php?error=Todos los campos son requeridos.");
        exit();
    }

    try {
        // Verificar que el rol pertenezca al grupo permitido
        $checkRol = $pdo->prepare("SELECT nombre_rol FROM roles WHERE id_rol = ?");
        $checkRol->execute([$id_rol]);
        $rolResult = $checkRol->fetch();

        $roles_permitidos = ['inventario', 'gerente'];

        if (!$rolResult || !in_array($rolResult['nombre_rol'], $roles_permitidos, true)) {
            header("Location: paginas/usuarios.php?error=Rol no permitido para el personal.");
            exit();
        }

        // Inserción incluyendo la columna estado
        $stmt = $pdo->prepare("INSERT INTO usuarios (nom_com, correo, usu_con, id_rol, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom_com, $correo, $usu_con, $id_rol, $estado]);

        header("Location: paginas/usuarios.php?msg=Usuario creado exitosamente con estado " . $estado . ".");
        exit();

    } catch (PDOException $e) {
        header("Location: paginas/usuarios.php?error=El correo electronico ya se encuentra registrado.");
        exit();
    }
}
// --- DETECTAR ACCIONES POR MÉTODO GET (Cerrar Sesión) ---
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
    header("Location: login.php");
    exit();
}

// --- DETECTAR ACCIONES POR MÉTODO POST (Formularios) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ACCIÓN 1: Registrar Producto
    if (isset($_POST['action']) && $_POST['action'] === 'nuevo_producto') {
        $sql = "INSERT INTO producto (nom_pro, descripcion, precio, stock, categoria) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nom_pro'],
            $_POST['descripcion'],
            $_POST['precio'],
            $_POST['stock'],
            $_POST['categoria']
        ]);
        header("Location: paginas/productos.php?msg=Producto+registrado");
        exit;
    }
    
    // ACCIÓN 2: Registrar Pedido (Venta de múltiples productos)
    if (isset($_POST['action']) && $_POST['action'] === 'nuevo_pedido') {
        $id_usu = $_POST['id_usu'];
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
            header("Location: index.php?msg=Pedido+creado+con+exito");
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            header("Location: index.php?error=" . urlencode($e->getMessage()));
            exit;
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
            $_SESSION['nom_com'] = $usuario['nom_com'];
            $_SESSION['usuario_rol'] = $usuario['nombre_rol']; // Guarda 'admin', 'vendedor' o 'gerente'
            
            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?error=Usuario o contraseña incorrectos");
            exit();
        }
    }
}
?>