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

//EXPANDIR INFORMACION---------------------------------------------------------------------------------------------------------------------------------
if (isset($_GET["id"])) {
    $libroId = $_GET["id"];

    $sql = "SELECT libros.titulo, libros.sinopsis, generos_autores.nombre AS autor, generos_autores2.nombre AS genero
            FROM libros
            INNER JOIN generos_autores ON libros.autor_id = generos_autores.id
            INNER JOIN generos_autores AS generos_autores2 ON libros.genero_id = generos_autores2.id
            WHERE libros.id = $libroId";

    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $titulo = $row["titulo"];
        $sinopsis = $row["sinopsis"];
        $autor = $row["autor"];
        $genero = $row["genero"];

        echo "<h2>$titulo</h2>";
        echo "<p><strong>Género:</strong> $genero</p>";
        echo "<p><strong>Autor:</strong> $autor</p>";
        echo "<p><strong>Sinopsis:</strong> $sinopsis</p>";
    } else {
        echo "No se encontró información del libro.";
    }

    $sql = "SELECT * FROM transacciones WHERE formato = 'intercambio' AND libro_id = '$libroId'";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $idUsuario = $row["usuario_id"];
    echo '<form method="POST" action="mensajes.php">';
    echo '<input type="hidden" name="idUsuario" value="' . $idUsuario . '">';
    echo '<input type="hidden" name="titulo" value="' . htmlspecialchars($titulo) . '">';
    echo '<input type="submit" name="escribir_mensaje" value="Enviar mensaje a usuarios que lo intercambian ">';
    echo '</form>';
}

//ESTE BOTON ES SOLO UN EJEMPLO
echo '<input type="submit" value="Comprar en tienda">';

$conn->close();
} else {
echo "No se encontró información del libro.";
}
?>
