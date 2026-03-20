<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "conexion.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT id_grupo, nombre_grupo, cuatrimestre FROM grupos ORDER BY nombre_grupo";
        $resultado = $conn->query($sql);
        
        $grupos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $grupos[] = $fila;
        }
        
        echo json_encode($grupos);
        break;

    case 'POST':
        $datos = json_decode(file_get_contents("php://input"), true);
        
        $nombre_grupo = $datos['nombre_grupo'] ?? '';
        $cuatrimestre = $datos['cuatrimestre'] ?? '';
        
        if (empty($nombre_grupo) || empty($cuatrimestre)) {
            echo json_encode(["success" => false, "error" => "Campos requeridos"]);
            break;
        }
        
        $sql = "INSERT INTO grupos (nombre_grupo, cuatrimestre) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre_grupo, $cuatrimestre);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Grupo guardado"]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    case 'PUT':
        $datos = json_decode(file_get_contents("php://input"), true);
        $id = $_GET['id'] ?? 0;
        
        $nombre_grupo = $datos['nombre_grupo'] ?? '';
        $cuatrimestre = $datos['cuatrimestre'] ?? '';
        
        $sql = "UPDATE grupos SET nombre_grupo = ?, cuatrimestre = ? WHERE id_grupo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombre_grupo, $cuatrimestre, $id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Grupo actualizado"]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? 0;
        
        $sql = "DELETE FROM grupos WHERE id_grupo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Grupo eliminado"]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Método no soportado"]);
}
?>