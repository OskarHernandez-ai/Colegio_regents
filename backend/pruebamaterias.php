<?php
include "conexion.php";

echo "Conectado a la BD: " . $database . "<br>";

$sql = "SELECT * FROM materias";
$resultado = $conn->query($sql);

if($resultado) {
    echo "Tabla materias existe<br>";
    echo "Número de materias: " . $resultado->num_rows;
} else {
    echo "Error: " . $conn->error;
}
?>