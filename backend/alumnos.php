<?php
// Configuración de cabeceras para permitir JSON
header("Content-Type: application/json");

// Conexión a la base de datos (ajusta tus credenciales)
$pdo = new PDO("mysql:host=localhost;dbname=tu_base_datos", "usuario", "password");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lógica para obtener todos los alumnos
        $stmt = $pdo->query("SELECT * FROM alumnos");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        // Lógica para guardar un nuevo alumno
        $nombre = $_POST['nombre'];
        $matricula = $_POST['matricula'];
        $correo = $_POST['correo'];
        
        $stmt = $pdo->prepare("INSERT INTO alumnos (nombre, matricula, correo) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $matricula, $correo]);
        echo json_encode(["status" => "success"]);
        break;

    case 'DELETE':
        // Lógica para eliminar (obtenemos el ID de la URL)
        $id = $_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
        $stmt->execute([$id]);
        echo json_encode(["status" => "deleted"]);
        break;
}
?>