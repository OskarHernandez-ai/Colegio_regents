<?php
// Configuración de cabeceras para permitir peticiones AJAX (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Conexión a la base de datos
$host = "localhost";
$db = "sistema_escolar"; // <-- Cambia esto por el nombre real
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Error de conexión: " . $e->getMessage()]));
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Obtener el ID si se envía en la URL (ej. calificaciones.php/5)
$path_info = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];
$id = $path_info[0] ?? null;

switch ($method) {
    case 'GET':
        // Selecciona datos uniendo con la tabla de inscripciones para obtener nombres
        $sql = "SELECT c.*, a.nombre as alumno, m.nombre as materia 
                FROM calificaciones c
                JOIN inscripciones i ON c.id_alumno_materia = i.id_alumno_materia
                JOIN alumnos a ON i.id_alumno = a.id
                JOIN materias m ON i.id_materia = m.id";
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $sql = "INSERT INTO calificaciones (id_alumno_materia, trimestre1, trimestre2, trimestre3, promedio) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input['id_alumno_materia'], $input['trimestre1'], $input['trimestre2'], $input['trimestre3'], $input['promedio']]);
        echo json_encode(["status" => "Guardado"]);
        break;

    case 'PUT':
        $sql = "UPDATE calificaciones SET trimestre1=?, trimestre2=?, trimestre3=?, promedio=? WHERE id_calificacion=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$input['trimestre1'], $input['trimestre2'], $input['trimestre3'], $input['promedio'], $id]);
        echo json_encode(["status" => "Actualizado"]);
        break;

    case 'DELETE':
        $sql = "DELETE FROM calificaciones WHERE id_calificacion = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        echo json_encode(["status" => "Eliminado"]);
        break;
}
?>