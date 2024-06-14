<link rel="stylesheet" href="estilos.css">
<div class="logo">
        <img src="logo.png" alt="Logo">
</div>
<br>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rincon_literario";
$conn = new mysqli($servername, $username, $password, $dbname);
session_start();

//VERIFICAR SESION-------------------------------------------------------------------------------------------------------------------------------------
if (isset($_SESSION["nombre_usuario"])) {
    $nombreUsuario = $_SESSION["nombre_usuario"];

//CERRAR SESION-------------------------------------------------------------------------------------------------------------------------------------
    echo '<div class="button-container">';
    echo '<form method="POST" action="inicio.php">';
    echo '<input type="submit" name="cerrar_sesion" value="Cerrar Sesión">';
    echo '</form>';
    echo '<form method="POST" action="principal.php">';
    echo '<input type="submit" value="Ir a la pagina principal">';
    echo '</form>';
    echo '</div>';
} else {
    echo 'No se ha iniciado sesión.';
    echo '<a href="inicio.php">Ir a Inicio</a>';
    exit();
}

// Verificar si se han pasado parámetros en la URL
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["escribir_mensaje"])) {
    $titulo = $_POST["titulo"];
    $id = $_POST["idUsuario"];

    // Mostrar el formulario de envío de mensajes
    echo "<h2>Enviar Mensaje sobre el Libro: $titulo</h2>";
    echo '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    echo '<input type="hidden" name="titulo" value="' . htmlspecialchars($titulo) . '">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<textarea name="mensaje" placeholder="Escribe tu mensaje..." required></textarea>';
    echo '<input type="submit" name="enviar_mensaje" value="Enviar">';
    echo '</form>';
}
    // Procesar el envío de mensajes
    if (isset($_POST["enviar_mensaje"])) {
        $mensaje = $_POST["mensaje"];
        $id = $_POST["id"];
        // Obtener el ID del usuario remitente (el que tiene la sesión iniciada)
        $remitente_id = $_SESSION["id_usuario"];

        // Insertar el mensaje en la tabla de mensajes
        $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, mensaje) VALUES ('$remitente_id', '$id', '$mensaje')";
        if ($conn->query($sql) === TRUE) {
            echo "Mensaje enviado correctamente.";
        } else {
            echo "Error al enviar el mensaje: " . $conn->error;
        }
    }


    else {
    // Mostrar los mensajes recibidos por el usuario
    echo "<h2>Mensajes Recibidos</h2>";

    // Obtener el ID del usuario destinatario (el que tiene la sesión iniciada)
    $destinatario_id = $_SESSION["id_usuario"]; // Reemplaza 1 por el ID del usuario destinatario desde la sesión

    // Obtener los mensajes recibidos de la base de datos
    

    $sql = "SELECT remitente_id, mensaje, fecha_envio FROM mensajes WHERE destinatario_id = '$destinatario_id' ORDER BY fecha_envio DESC";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $remitente_id = $row["remitente_id"];
            $mensaje = $row["mensaje"];
            $fecha_envio = $row["fecha_envio"];

            // Obtener el nombre del remitente desde la base de datos
            $sql_remitente = "SELECT nombre FROM usuarios WHERE id = '$remitente_id'";
            $resultado_remitente = $conn->query($sql_remitente);
            if ($resultado_remitente->num_rows > 0) {
                $row_remitente = $resultado_remitente->fetch_assoc();
                $remitente_nombre = $row_remitente["nombre"];

                // Mostrar el mensaje recibido
                echo "<div>";
                echo "<p>De: $remitente_nombre</p>";
                echo "<p>Mensaje: $mensaje</p>";
                echo "<p>Fecha de Envío: $fecha_envio</p>";
                echo "</div>";
            }
        }
    } else {
        echo "No tienes mensajes.";
    }

    $conn->close();
}
?>
</body>
</html>
