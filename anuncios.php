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

$usuarioID = $_SESSION["id_usuario"];
$sql = "SELECT libros.titulo, libros.sinopsis, generos_autores.nombre AS autor, generos_autores.tipo AS tipo
        FROM libros
        INNER JOIN generos_autores ON libros.autor_id = generos_autores.id
        INNER JOIN transacciones ON transacciones.libro_id = libros.id
        WHERE transacciones.usuario_id = $usuarioID
        AND transacciones.formato = 'intercambio'
        AND transacciones.usuario2_id IS NULL
        AND transacciones.libro2_id IS NULL";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
//MOSTRAR ANUNCIOS DEL USUARIO----------------------------------------------------------------------------------------
    echo "<h2>Libros Disponibles para Intercambiar</h2>";
    echo "<ul>";
    while ($row = $resultado->fetch_assoc()) {
        $titulo = $row["titulo"];
        $sinopsis = $row["sinopsis"];
        $autor = $row["autor"];
        $tipo = $row["tipo"];
        echo "<li><a href='expandir.php?titulo=$titulo&sinopsis=$sinopsis&autor=$autor&tipo=$tipo'>$titulo</a></li>";
    }
    echo "</ul>";
} else {
    echo "No tienes libros disponibles para intercambiar.";
}

//PROCESAR FORMULARIO PARA CREAR ANUNCIO-----------------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["crear_anuncio"])) {
    $libroId = $_POST["libro"];
    $usuarioId = $_SESSION["id_usuario"];

    $sql = "INSERT INTO transacciones (usuario_id, libro_id, formato) VALUES ('$usuarioId', '$libroId', 'intercambio')";
    if ($conn->query($sql) === TRUE) {
        echo "Anuncio creado exitosamente";
    } else {
        echo "Error al crear el anuncio: " . $conn->error;
    }
}

//MOSTRAR LIBROS DISPONIBLES---------------------------------------------------------------------------------------------------------------
$sql = "SELECT id, titulo FROM libros";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Anuncio</title>
</head>
<body>
    <h2>Crear Nuevo Anuncio</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="libro">Selecciona un libro:</label>
        <select name="libro" id="libro">
            <?php
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $libroId = $row["id"];
                    $titulo = $row["titulo"];
                    echo "<option value='$libroId'>$titulo</option>";
                }
            } else {
                echo "<option value=''>No hay libros disponibles</option>";
            }
            ?>
        </select>
        <br>
        <input type="submit" name="crear_anuncio" value="Crear Anuncio">
    </form>
</body>
</html>