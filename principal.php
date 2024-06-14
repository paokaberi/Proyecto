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
    echo "<h1>¡Bienvenid@, $nombreUsuario!</h1><br>";

//CERRAR SESION-------------------------------------------------------------------------------------------------------------------------------------
    echo '<div class="button-container">';
    echo '<form method="POST" action="inicio.php">';
    echo '<input type="submit" name="cerrar_sesion" value="Cerrar Sesión">';
    echo '</form>';
    echo '<form method="POST" action="mensajes.php">';
    echo '<input type="submit" value="Ir a mensajes">';
    echo '</form>';
    echo '<form method="POST" action="anuncios.php">';
    echo '<input type="submit" value="Ir a mis anuncios">';
    echo '</form>';
    echo '</div>';
} else {
    echo 'No se ha iniciado sesión.';
    echo '<a href="inicio.php">Ir a Inicio</a>';
    exit();
}


//SELECCIONAR TODOS LOS LIBROS DISPONIBLES-------------------------------------------------------------------------------------------------------------
$sql = "SELECT libros.id, libros.titulo, libros.sinopsis, generos.nombre AS genero, autores.nombre AS autor 
        FROM libros
        INNER JOIN generos_autores AS generos ON libros.genero_id = generos.id
        INNER JOIN generos_autores AS autores ON libros.autor_id = autores.id
        WHERE libros.titulo IS NOT NULL";

//FILTRAR POR GENERO O AUTOR-------------------------------------------------------------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filtrar"])) {
    $filtro = $_POST["filtro"];
    $sql .= " AND (libros.titulo LIKE '%$filtro%' OR generos.nombre LIKE '%$filtro%' OR autores.nombre LIKE '%$filtro%')";
}

$resultado = $conn->query($sql);
//MOSTRAR LIBROS EN PANTALLA-------------------------------------------------------------------------------------------------------------
if ($resultado->num_rows > 0) {
    echo "<h2>Libros Disponibles</h2>";
    echo '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
    echo '<label for="filtro">Filtrar por título, género o autor:</label>';
    echo '<input type="text" name="filtro" id="filtro">';
    echo '<input type="submit" name="filtrar" value="Filtrar">';
    echo '</form>';
    echo "<ul>";
    while ($row = $resultado->fetch_assoc()) {
        $titulo = $row["titulo"];
        $id = $row["id"];
        echo "<li><a href='expandir.php?id=$id'>$titulo</a></li>";
    }
    echo "</ul>";
} else {
    echo "No hay libros disponibles.";
}
$conn->close();
?>
