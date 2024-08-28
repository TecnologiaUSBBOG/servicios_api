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

$sql_ciclo = "SELECT DISTINCT CICLO 
              FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB
              ORDER BY CICLO DESC
              FETCH FIRST 1 ROW ONLY";

$execute_ciclo = $dbi->Execute($sql_ciclo);

// Verificar si la consulta se ejecutó correctamente
if ($execute_ciclo === false) {
    $error_message = $dbi->ErrorMsg();
    echo json_encode(array('error' => 'Error en la consulta de ciclo', 'details' => $error_message));
    exit;
}

$ciclo_result = $execute_ciclo->fields['CICLO'];

$cquery = $execute_ciclo->RecordCount();

if (empty($ciclo_result)) {
    echo json_encode(array('error' => 'El ciclo está vacío'));
} else {
    echo json_encode(array('CICLO' => $ciclo_result));
}
