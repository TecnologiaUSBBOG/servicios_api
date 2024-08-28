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

if (empty($doc_est) || empty($ciclo) || empty($doc_doc)) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$sql_table = "SELECT NUMEROSESION, PERIODOACADEMICO, TIPOTUTORIA, FACULTAD, PROGRAMA, NOMBREDELCURSO, PROFESORRESPONSABLE, TEMATICA, MODALIDAD, METODOLOGIA, FECHATUTORIA, LUGAR, DOCUMENTO
    FROM ACADEMICO.PROGRAMACION_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est'  
    AND  PERIODOACADEMICO = '$ciclo' AND
    PROFESORRESPONSABLE IN (SELECT 
    NOMBREPROFESOR ||' '||APELLIDOSPROFESOR FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE DOCUMENTO= '$doc_doc')
    ORDER BY NUMEROSESION ASC";

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
