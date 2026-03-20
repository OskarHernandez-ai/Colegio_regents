<?php
header("Content-Type: application/json");
include("conexion.php"); // Usamos tu conexión existente (mysqli)

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM alumnos ORDER BY id_alumno DESC";
        $resultado = $conn->query($sql);
        $alumnos = [];
        while($fila = $resultado->fetch_assoc()){
            $alumnos[] = $fila;
        }
        echo json_encode($alumnos);
        break;

    case 'POST':
        // Recibimos los datos del formulario (FormData)
        $nombre = $_POST['nombre'] ?? '';
        $matricula = $_POST['matricula'] ?? '';
        $correo = $_POST['correo'] ?? '';

        if (!empty($nombre) && !empty($matricula)) {
            $stmt = $conn->prepare("INSERT INTO alumnos (nombre, matricula, correo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $matricula, $correo);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Alumno registrado"]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Nombre y matrícula son obligatorios"]);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo json_encode(["status" => "deleted"]);
        }
        break;
}
?>