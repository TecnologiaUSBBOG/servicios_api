<?
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


$ID_GRUPO = $_POST['ID_GRUPO'];

$sql_COUNT = "SELECT COUNT(*) AS CANTIDAD FROM  ACADEMICO.GRUPOS_SESIONES_TUTORIAS WHERE GRUPO='$ID_GRUPO'";
$execute_count = $dbi->Execute($sql_COUNT);
// Verificar si la consulta se ejecutó correctamente
if ($execute_count === false) {
    $error_message = $dbi->ErrorMsg();
    echo json_encode(array('error' => 'Error en la consulta de contar estudiantes', 'details' => $error_message));
    exit;
}

$fquery = $execute_count->RecordCount();
if ($fquery > 0) {
    $results = array();
    while (!$execute_count->EOF) {
        $results[] = $execute_count->fields;
        $execute_count->MoveNext();
    }
    echo json_encode($results);
} else {
    echo json_encode(array('error' => 'No se encontraron resultados'));
}

$dbi->close();

?>