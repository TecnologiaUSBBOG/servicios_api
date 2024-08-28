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



$doc_est = isset($_POST['DOC_EST']) ? $_POST['DOC_EST'] : '';
$ciclo = isset($_POST['CICLO']) ? $_POST['CICLO'] : '';
$doc_doc = isset($_POST['DOC_DOC']) ? $_POST['DOC_DOC'] : '';
$sesion = isset($_POST['SESION']) ? $_POST['SESION'] : '';

if (empty($doc_est) || empty($ciclo) || empty($doc_doc) || empty($sesion)) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$sql_table = "SELECT    ROWID,
                        ID_DETALLADO,
                        NOMBREESTUDIANTE,
                        DOCUMENTO,
                        CODIGO,
                        ACTIVIDADREALIZADA,
                        ASISTENCIA,
                        CALIFICACIONESTUDIANTE,
                        SESION,
                        DOCUMENTOP,
                        ACUERDOSYCOMPROMISOS,
                        INICIO_TUTORIA,
                        FINAL_TUTORIA,
                        (SELECT NOMBREDELCURSO FROM ACADEMICO.PROGRAMACION_TUTORIAS_TB 
                        WHERE DOCUMENTO = '$doc_est' AND DOCUMENTOP = '$doc_doc' 
                        AND NUMEROSESION = '$sesion' AND PERIODOACADEMICO = '$ciclo') AS CURSO
                        FROM ACADEMICO.DETALLADO_TUTORIAS_TB 
                        WHERE DOCUMENTO = '$doc_est' AND SESION = '$sesion'  
                        AND PERIODOACADEMICO = '$ciclo' AND
                        DOCUMENTOP = '$doc_doc'";

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
