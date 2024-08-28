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



$id_grupo = isset($_POST['ID_GRUPO']) ? $_POST['ID_GRUPO'] : '';
$sesion = isset($_POST['SESION']) ? $_POST['SESION'] : '';

if (empty($id_grupo) || empty($sesion)) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$sql_table = "SELECT SESION,FECHAINICIO, FECHAFINAL, NOMBREESTUDIANTE, CODIGOESTUDIANTIL, DOCUMENTOESTUDIANTE, 
                    ACTIVIDADREALIZADA, ACUERDOSCOMPROMISOS, ASISTENCIA, ESTADOCALIFICACION
                    FROM ACADEMICO.GRUPOS_DETALLADO_TUTORIAS WHERE GRUPO = '$id_grupo' AND SESION = '$sesion'";

$Ejecutar_Consulta = $dbi->Execute($sql_table);

if ($Ejecutar_Consulta === false) {
    echo json_encode(array('error' => 'Error en la consulta a la base de datos'));
    exit;
}

$fquery = $Ejecutar_Consulta->RecordCount();

if ($fquery > 0) {
    $results = array();
    while (!$Ejecutar_Consulta->EOF) {
        $results[] = $Ejecutar_Consulta->fields;
        $Ejecutar_Consulta->MoveNext();
    }
    echo json_encode($results);
} else {
    echo json_encode(array('error' => 'No se encontraron resultados'));
}

$dbi->close();
