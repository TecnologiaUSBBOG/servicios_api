
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


$sql_ciclo = "SELECT DISTINCT CICLO FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB
                ORDER BY CICLO DESC FETCH FIRST 1 ROW ONLY";

$execute_ciclo = $dbi->Execute($sql_ciclo);

$cquery = $execute_ciclo->RecordCount();

if ($cquery > 0) {
    $resultsC = array();
    while (!$execute_ciclo->EOF) {
        $resultsC[] = $execute_ciclo->fields;
        $execute_ciclo->MoveNext();
    }

    $doc_doc = isset($_POST['DOC_DOC']) ? $_POST['DOC_DOC'] : '';

    if (empty($doc_doc)) {
        echo json_encode(array('error' => 'Faltan parámetros'));
        exit;
    }

    $sql_table = "SELECT ROWID,
                        ID_GRUPO,
                        NOMBRE,
                        DOCUMENTOPROFESOR,
                        PERIODOACADEMICO,
                        'Info Grupo' Info_grupo
                        FROM ACADEMICO.GRUPOSTUTORIAS where DOCUMENTOPROFESOR IN (SELECT DOCUMENTO FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE 
                        DOCUMENTO = '$doc_doc') AND PERIODOACADEMICO = '{$resultsC[0]['CICLO']}'";

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
        echo json_encode(array());
    }
} else {
    echo json_encode(array('error' => 'No se encontraron resultados en la primera consulta'));
}

$dbi->close();
?>


