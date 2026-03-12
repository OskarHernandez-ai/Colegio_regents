<?php

include("conexion.php");

$usuario = $_POST["usuario"];
$password = $_POST["password"];

$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";

$resultado = $conn->query($sql);

if($resultado->num_rows > 0){
    
    echo json_encode([
        "success" => true
    ]);

}else{

    echo json_encode([
        "success" => false
    ]);

}

?>