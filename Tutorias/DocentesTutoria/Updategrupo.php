<?php
// DMCH
include_once "../../../../bases_datos/adodb/adodb.inc.php";
include_once "../../../../bases_datos/usb_defglobales.inc";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

/*$dbi = NewADOConnection("$motor_p");
$dbi->Connect($base_p, $usuario_p, $contra_p);

if (!$dbi) {
    echo json_encode(array('error' => 'Error en la conexión a la base de datos'));
    exit;
}*/

try {
    $dbi = NewADOConnection($motor_p);
    $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit;
}


$name_group = isset($_POST['NOMBRE']) ? $_POST['NOMBRE'] : '';
$doc_doc = isset($_POST['ID_GRUPO']) ? $_POST['ID_GRUPO'] : '';


if (empty($doc_doc) || empty($name_group)) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    error_log("Algun dato está vacio", 0);
    exit;
}

$sql_insert = "UPDATE ACADEMICO.GRUPOSTUTORIAS SET NOMBRE='$name_group' WHERE ID_GRUPO='$doc_doc'";

$Ejecutar_Consulta = $dbi->Execute($sql_insert);

if ($Ejecutar_Consulta === false) {
    echo json_encode(array('error' => 'Error en la consulta de inserción a la base de datos'));
    exit;
} else {
    echo json_encode(array('success' => 'Actualizado los datos con  exito'));
}

$dbi->close();

?>