<?php
// Directorio base para el sistema de archivos (DENTRO del contenedor)
// Basado en el contexto de Docker, la raíz de tu proyecto es /var/www/html.
$directorio_base = './'; 
$directorio_raiz_completo = realpath($directorio_base);

// Obtener el directorio a explorar desde el parámetro GET 'dir'.
$dir_solicitado = $_GET['dir'] ?? '';

// Construir la ruta completa y segura para el SISTEMA DE ARCHIVOS.
$ruta_actual_filesystem = realpath($directorio_base . $dir_solicitado);

// Validación de seguridad: Asegurar que el usuario no salga del directorio base.
if ($ruta_actual_filesystem === false || strpos($ruta_actual_filesystem, $directorio_raiz_completo) !== 0) {
    $ruta_actual_filesystem = $directorio_raiz_completo;
}

// -------------------------------------------------------------
// EXTRAER la ruta RELATIVA HTTP para URLs y visualización
$ruta_relativa_http = str_replace($directorio_raiz_completo, '', $ruta_actual_filesystem);
$ruta_relativa_http = trim($ruta_relativa_http, DIRECTORY_SEPARATOR);
// -------------------------------------------------------------

// Obtener el contenido del directorio
$contenido = scandir($ruta_actual_filesystem);

// Determinar el directorio padre HTTP
$ruta_padre_http = dirname($ruta_relativa_http);
if ($ruta_padre_http === '.') $ruta_padre_http = ''; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Archivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file-icon { width: 20px; height: 20px; margin-right: 10px; }
        .list-group-item:hover { background-color: #f8f9fa; }
    </style>
</head>
<body class="p-4">

    <div class="container">
        <h2 class="mb-4">📂 Explorador de Archivos</h2>
        <p class="lead">Ubicación actual: <code><?php echo htmlspecialchars($ruta_relativa_http === '' ? '/' : $ruta_relativa_http); ?></code></p>
        
        <hr>

        <div class="list-group">
            
            <?php 
            // Botón para volver al directorio padre
            if ($ruta_relativa_http !== '') : 
            ?>
                <a href="?dir=<?php echo urlencode($ruta_padre_http); ?>" class="list-group-item list-group-item-action d-flex align-items-center bg-light">
                    <span class="file-icon">⬅️</span>
                    <strong>.. (Volver al directorio padre)</strong>
                </a>
            <?php 
            endif; 
            
            // Listar el contenido
            foreach ($contenido as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                
                // Construir la ruta completa para verificar si es un directorio (usando la ruta del filesystem)
                $ruta_completa_item_filesystem = $ruta_actual_filesystem . DIRECTORY_SEPARATOR . $item;

                // Construir la nueva ruta HTTP para URLs
                if ($ruta_relativa_http === '') {
                    $nueva_ruta_http = $item;
                } else {
                    $nueva_ruta_http = $ruta_relativa_http . DIRECTORY_SEPARATOR . $item;
                }

                $enlace = '';

                if (is_dir($ruta_completa_item_filesystem)) {
                    $icono = '📁';
                    $clase_item = 'list-group-item-dark';
                    
                    // Buscar index.php o index.html
                    $index_file = false;
                    if (file_exists($ruta_completa_item_filesystem . DIRECTORY_SEPARATOR . 'index.php')) {
                        $index_file = 'index.php';
                    } elseif (file_exists($ruta_completa_item_filesystem . DIRECTORY_SEPARATOR . 'index.html')) {
                        $index_file = 'index.html';
                    }

                    if ($index_file) {
                        // SI HAY INDEX: Construimos la URL manualmente sin codificar las barras '/'
                        $enlace = '/' . $nueva_ruta_http . '/' . $index_file;
                        
                        $icono = '➡️'; 
                        $clase_item = 'list-group-item-success'; 
                    } else {
                        // Si no hay index, navegamos con el explorador (?dir=...)
                        // Aquí usamos urlencode porque es un parámetro GET
                        $enlace = '?dir=' . urlencode($nueva_ruta_http); 
                    }

                } else {
                    // Es un archivo:
                    $icono = '📄';
                    $clase_item = '';
                    
                    // Construimos la URL parte por parte
                    if (empty($ruta_relativa_http)) {
                        // Raíz: /archivo.php
                        $enlace = '/' . urlencode($item);
                    } else {
                        // Subcarpeta: /carpeta/archivo.php
                        // Solo codificamos el nombre del archivo final para respetar espacios, etc.
                        // Las barras de la ruta relativa se mantienen intactas.
                        $enlace = '/' . $ruta_relativa_http . '/' . urlencode($item);
                    }
                }

                // Imprimir el elemento en la lista
                echo '<a href="' . $enlace . '" class="list-group-item list-group-item-action d-flex align-items-center ' . $clase_item . '">';
                echo '    <span class="file-icon">' . $icono . '</span>';
                echo '    ' . htmlspecialchars($item);
                echo '</a>';
            }
            ?>
        </div>
    </div>

</body>
</html>