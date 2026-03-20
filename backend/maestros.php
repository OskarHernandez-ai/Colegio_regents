<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Configuración de la base de datos
$conn = new mysqli("localhost", "root", "", "sistema_escolar");

// Comprobar errores de conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}
$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    $res = $conn->query("SELECT * FROM maestros");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}
if ($method == "POST") {
    $accion = $_POST["accion"];
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $id_usuario = $_POST["id_usuario"] ?? 1; // Valor por defecto 1 si no llega nada

    if ($accion == "guardar") {
        $stmt = $conn->prepare("INSERT INTO maestros(nombre, correo, id_usuario) VALUES(?, ?, ?)");
        $stmt->bind_param("ssi", $nombre, $correo, $id_usuario);
        $stmt->execute();
    } elseif ($accion == "editar") {
        $id = $_POST["id"];
        $stmt = $conn->prepare("UPDATE maestros SET nombre=?, correo=? WHERE id_maestro=?");
        $stmt->bind_param("ssi", $nombre, $correo, $id);
        $stmt->execute();
    }
    echo json_encode(["success" => true]);
}

if ($method == "DELETE") {
    $id = $_GET["id"]; 
    $stmt = $conn->prepare("DELETE FROM maestros WHERE id_maestro=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(["success" => true]);
}
?>