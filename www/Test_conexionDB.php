<!DOCTYPE html>
<html>

<head>
  <title>Docker PHP Environment</title>
</head>

<body>
  <h1>¡Entorno Docker PHP funcionando!</h1>

  <?php
  echo "<p>PHP version: " . phpversion() . "</p>";
  echo "<p>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

  // Probar conexión a MySQL
  try {
    // Desde el contenedor PHP, conectar al servicio "database"
    $host = "database";  // Nombre del servicio en docker-compose
    $user = "devuser";
    $pass = "devpassword";
    $db   = "app_db";

    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color: green;'>✅ Conexión a MySQL exitosa</p>";

    // Mostrar bases de datos disponibles
    $stmt = $pdo->query("SHOW DATABASES");
    echo "<h3>Bases de datos:</h3><ul>";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
      echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
  } catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error de conexión a MySQL: " . $e->getMessage() . "</p>";
  }

  // Mostrar información del servidor
  echo "<h3>Extensiones PHP cargadas:</h3>";
  $extensions = get_loaded_extensions();
  echo "<ul>";
  foreach ($extensions as $ext) {
    echo "<li>$ext</li>";
  }
  echo "</ul>";
  ?>
</body>

</html>
