<?php

class TokenRecuperacion
{
    private $con;
    private $expiracionHoras = 1; // El token expira en 1 hora

    public function __construct($conexion)
    {
        $this->con = $conexion;
        $this->crearTablaSiNoExiste();
    }

    /**
     * Crea la tabla de tokens si no existe.
     */
    private function crearTablaSiNoExiste()
    {
        $sql = "CREATE TABLE IF NOT EXISTS recuperacion_tokens (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            correo VARCHAR(225) NOT NULL,
            token VARCHAR(128) NOT NULL,
            fecha_expiracion DATETIME NOT NULL,
            usado TINYINT(1) NOT NULL DEFAULT 0,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (correo),
            UNIQUE KEY (token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        mysqli_query($this->con, $sql);
    }

    /**
     * Genera un token único y lo guarda en la BD para el correo dado.
     * Retorna el token como string.
     */
    public function generarToken($correo)
    {
        // Eliminar tokens anteriores para este correo (opcional)
        $this->eliminarTokensAnteriores($correo);

        $token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+{$this->expiracionHoras} hours"));

        $stmt = mysqli_prepare($this->con,
            'INSERT INTO recuperacion_tokens (correo, token, fecha_expiracion) VALUES (?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'sss', $correo, $token, $fechaExpiracion);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $token;
    }

    /**
     * Verifica si un token es válido (existe, no expirado, no usado).
     * Retorna el correo asociado si es válido, o false en caso contrario.
     */
    public function verificarToken($token)
    {
        $stmt = mysqli_prepare($this->con,
            'SELECT correo, fecha_expiracion, usado FROM recuperacion_tokens WHERE token = ?'
        );
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $expiracion = strtotime($row['fecha_expiracion']);
            $ahora = time();
            if ($row['usado'] == 0 && $expiracion > $ahora) {
                mysqli_stmt_close($stmt);
                return $row['correo']; // Válido
            }
        }
        mysqli_stmt_close($stmt);
        return false;
    }

    /**
     * Marca un token como usado (después de restablecer la contraseña).
     */
    public function marcarUsado($token)
    {
        $stmt = mysqli_prepare($this->con,
            'UPDATE recuperacion_tokens SET usado = 1 WHERE token = ?'
        );
        mysqli_stmt_bind_param($stmt, 's', $token);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Elimina todos los tokens expirados o usados (limpieza).
     */
    public function limpiarTokens()
    {
        $stmt = mysqli_prepare($this->con,
            'DELETE FROM recuperacion_tokens WHERE usado = 1 OR fecha_expiracion < NOW()'
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Elimina tokens anteriores para un correo (opcional, para evitar acumulación).
     */
    private function eliminarTokensAnteriores($correo)
    {
        $stmt = mysqli_prepare($this->con,
            'DELETE FROM recuperacion_tokens WHERE correo = ?'
        );
        mysqli_stmt_bind_param($stmt, 's', $correo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}