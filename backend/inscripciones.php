<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "localhost";
$db = "sistema_escolar";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["error" => "Conexión fallida"]));
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null;

switch ($method) {
    case 'GET':
        if ($tipo == 'alumnos') {
            echo json_encode($pdo->query("SELECT id_alumno, nombre FROM alumnos")->fetchAll(PDO::FETCH_ASSOC));
        } elseif ($tipo == 'materias') {
            echo json_encode($pdo->query("SELECT mm.id_maestro_materia, m.nombre_materia FROM maestro_materia mm JOIN materias m ON mm.id_materia = m.id_materia")->fetchAll(PDO::FETCH_ASSOC));
        } else {
            $sql = "SELECT am.id_alumno_materia, a.nombre AS nombre_alumno, m.nombre_materia 
                    FROM alumnomateria am
                    JOIN alumnos a ON am.id_alumno = a.id_alumno
                    JOIN maestro_materia mm ON am.id_maestro_materia = mm.id_maestro_materia
                    JOIN materias m ON mm.id_materia = m.id_materia";
            echo json_encode($pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $sql = "INSERT INTO alumnomateria (id_alumno, id_maestro_materia) VALUES (?, ?)";
        $pdo->prepare($sql)->execute([$input['id_alumno'], $input['id_maestro_materia']]);
        echo json_encode(["status" => "Inscrito"]);
        break;

    case 'DELETE':
        $pdo->prepare("DELETE FROM alumnomateria WHERE id_alumno_materia = ?")->execute([$id]);
        echo json_encode(["status" => "Eliminado"]);
        break;
}
?>