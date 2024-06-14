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

//PROCESAR FORMULARIO DE REGISTRO----------------------------------------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["registrar"])) {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar el usuario: " . $conn->error;
    }
}

//PROCESAR FORMULARIO DE INICIO----------------------------------------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["iniciar_sesion"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND password = '$password'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION["email_usuario"] = $usuario["email"];
        $_SESSION["nombre_usuario"] = $usuario["nombre"];
        $_SESSION["id_usuario"] = $usuario["id"];
        ob_end_clean();
        header("Location: principal.php");
        exit();
    } else {
        echo "Credenciales inválidas. Por favor, verifica tu correo y contraseñaa.";
    }
}

//PROCESAR FORMULARIO DE CIERRE----------------------------------------------------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cerrar_sesion"])) {
    session_destroy();
    header("Location: inicio.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rincón Literario - Registro de Usuario</title>
</head>
<body>
    <h1>Registro de Usuario</h1>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required>
        <br>
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" name="registrar" value="Registrar">
    </form>
    <h1>Iniciar sesión</h1>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>
        <br>
        <input type="submit" name="iniciar_sesion" value="Iniciar Sesión">
    </form>
</body>
</html>
