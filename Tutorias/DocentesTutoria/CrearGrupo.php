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
    exit;
}

$name_group = isset($_POST['NOMBRE_G']) ? $_POST['NOMBRE_G'] : '';
$doc_doc = isset($_POST['DOC_DOC']) ? $_POST['DOC_DOC'] : '';

if (empty($doc_doc) || empty($name_group)) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    error_log("Algun dato está vacio", 0);
    exit;
}

$sql_insert = "INSERT INTO ACADEMICO.GRUPOSTUTORIAS(ID_GRUPO, NOMBRE, DOCUMENTOPROFESOR, PERIODOACADEMICO) 
               VALUES ((SELECT MAX(ID_GRUPO) + 1 AS ID FROM ACADEMICO.GRUPOSTUTORIAS), '$name_group', '$doc_doc', '$ciclo_result')";

$Ejecutar_Consulta = $dbi->Execute($sql_insert);

if ($Ejecutar_Consulta === false) {
    echo json_encode(array('error' => 'Error en la consulta de inserción a la base de datos'));
    exit;
} else {
    echo json_encode(array('success' => 'Inserción exitosa'));
}

$dbi->close();
?>
