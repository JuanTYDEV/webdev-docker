<?php
/**
 * Obtiene la URL base para redireccionar a phpMyAdmin
 */
function getPhpMyAdminUrl() {
    $phpmyadmin_port = '8081';
    
    // Intentar desde variable de entorno (para casos manuales específicos)
    $host_ip = getenv('HOST_IP');
    if ($host_ip && $host_ip !== 'localhost') {
        return "http://{$host_ip}:{$phpmyadmin_port}/";
    }
    
    // Usar el host que el navegador solicitó
    if (isset($_SERVER['HTTP_HOST'])) {
        $current_host = $_SERVER['HTTP_HOST'];
        
        // Separar el host del puerto (ej: localhost:8080 -> localhost)
        $parts = explode(':', $current_host);
        $host = $parts[0];
        
        return "http://{$host}:{$phpmyadmin_port}/";
    }
    
    // Fallback final
    return "http://localhost:{$phpmyadmin_port}/";
}

// Obtener URL y redirigir
$phpmyadmin_url = getPhpMyAdminUrl();

// Redirección con JavaScript como fallback
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; url=<?php echo htmlspecialchars($phpmyadmin_url); ?>">
    <title>Redirigiendo a phpMyAdmin...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
        }
        .info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 600px;
        }
        .url {
            font-family: monospace;
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 3px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <h1>Redirigiendo a phpMyAdmin...</h1>
    
    <div class="info">
        <p>URL de phpMyAdmin detectada:</p>
        <div class="url"><?php echo htmlspecialchars($phpmyadmin_url); ?></div>
        
        <p style="margin-top: 20px;">
            <a href="<?php echo htmlspecialchars($phpmyadmin_url); ?>">
                Haz clic aquí si no eres redirigido automáticamente
            </a>
        </p>
    </div>
    
    <script>
        // Redirección con JavaScript
        window.location.href = "<?php echo addslashes($phpmyadmin_url); ?>";
        
        // Mostrar mensaje después de 3 segundos por si falla
        setTimeout(function() {
            document.getElementById('timeout-msg').style.display = 'block';
        }, 3000);
    </script>
    
    <p id="timeout-msg" style="display: none; color: #d32f2f;">
        La redirección automática está tardando más de lo esperado. 
        Por favor, usa el enlace de arriba manualmente.
    </p>
</body>
</html>