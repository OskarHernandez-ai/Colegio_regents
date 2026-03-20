<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejar preflight requests de CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

//conexión a la base de datos
require_once "conexion.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener todas las materias
        $sql = "SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia";
        $resultado = $conn->query($sql);
        
        $materias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $materias[] = $fila;
        }
        
        echo json_encode($materias);
        break;

    case 'POST':
        // Guardar nueva materia
        $datos = json_decode(file_get_contents("php://input"), true);
        
        // Si no hay datos JSON, intentar con FormData
        if (!$datos) {
            $nombre_materia = $_POST['nombre_materia'] ?? '';
        } else {
            $nombre_materia = $datos['nombre_materia'] ?? '';
        }
        
        if (empty($nombre_materia)) {
            echo json_encode(["success" => false, "error" => "El nombre de la materia es requerido"]);
            break;
        }
        
        $sql = "INSERT INTO materias (nombre_materia) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nombre_materia);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "id" => $conn->insert_id,
                "message" => "Materia guardada correctamente"
            ]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    case 'PUT':
        // Actualizar materia existente
        $datos = json_decode(file_get_contents("php://input"), true);
        
        // Obtener ID de la URL (ej: /materias.php?id=1)
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(["success" => false, "error" => "ID no proporcionado"]);
            break;
        }
        
        $nombre_materia = $datos['nombre_materia'] ?? '';
        
        if (empty($nombre_materia)) {
            echo json_encode(["success" => false, "error" => "El nombre de la materia es requerido"]);
            break;
        }
        
        $sql = "UPDATE materias SET nombre_materia = ? WHERE id_materia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre_materia, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Materia actualizada correctamente"
            ]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    case 'DELETE':
        // Eliminar materia
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            echo json_encode(["success" => false, "error" => "ID no proporcionado"]);
            break;
        }
        
        // Verificar si la materia está siendo utilizada en inscripciones
        $check_sql = "SELECT COUNT(*) as total FROM alumnos_materias WHERE id_materia = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $resultado = $check_stmt->get_result();
        $fila = $resultado->fetch_assoc();
        
        if ($fila['total'] > 0) {
            echo json_encode([
                "success" => false, 
                "error" => "No se puede eliminar la materia porque tiene alumnos inscritos"
            ]);
            break;
        }
        
        $sql = "DELETE FROM materias WHERE id_materia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Materia eliminada correctamente"
            ]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Método no soportado"]);
        break;
}

$conn->close();
?>