<?php
include_once "../../../../bases_datos/adodb/adodb.inc.php";
include_once "../../../../bases_datos/usb_defglobales.inc";


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
file_put_contents('php://stderr', 'Received data: ' . json_encode($_POST) . PHP_EOL);


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


$doc_doc = (isset($_POST['DOCUMENTOP'])) ? $_POST['DOCUMENTOP'] : "";
$nombre_est =(isset($_POST['NOMBRE_EST'])) ? $_POST['NOMBRE_EST']: "";
$doc_est =(isset($_POST['DOCUMENTO_EST'])) ? $_POST['DOCUMENTO_EST']: "";
$id_grupo = (isset($_POST['ID_GRUPO'])) ? $_POST['ID_GRUPO'] :  "";


if (isset($_POST['NOMBRE_EST'], $_POST['DOCUMENTO_EST'])) {

//consulta a ver si hay registro en la bases de datos
$sql_registro ="SELECT * FROM ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS WHERE CEDULA ='$doc_est' AND GRUPO='$id_grupo'";
$resultadoInsercion = $dbi ->Execute($sql_registro);


if ($resultadoInsercion === false) {
    $error_message = $dbi->ErrorMsg();
    echo json_encode(array('error' => 'Error en la consulta de contar estudiantes', 'details' => $error_message));
    exit;
} 
$fquery = $resultadoInsercion->RecordCount();
error_log("este rsultados de la consulta". $fquery);
if($fquery > 0){
echo json_encode(array('value'=>true));
}else{
echo json_encode(array('value'=>false));
//insertar el estudiante 
$insercion2 = "INSERT INTO ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS(ID_GRUPO_ESTUDIANTES, CEDULA, NOMBRES, DOCUMENTOPROFESOR, GRUPO) 
VALUES((SELECT MAX(ID_GRUPO_ESTUDIANTES)+1 AS ID FROM ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS), '$doc_est', '$nombre_est', '$doc_doc', '$id_grupo')";

$resultadoInsercion2 = $dbi->Execute($insercion2);

if (!$resultadoInsercion2) {
    echo json_encode(array('error' => 'Error al Registrar el estudiante: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
    $dbi->FailTrans(); 
    exit;
}

}

/*
if ($fquery > 0) {
    $results = array();
    while (!$resultadoInsercion->EOF) {
        $results[] = $resultadoInsercion->fields;
        $resultadoInsercion->MoveNext();
    }
    echo json_encode($results);
    error_log('este numero de registro que hay'.$fquery);
    
} else {

echo json_encode("ingreso") ;
$insercion2 = "INSERT INTO ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS(ID_GRUPO_ESTUDIANTES, CEDULA, NOMBRES, DOCUMENTOPROFESOR, GRUPO) 
VALUES((SELECT MAX(ID_GRUPO_ESTUDIANTES)+1 AS ID FROM ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS), '$doc_est', '$nombre_est', '$doc_doc', '$id_grupo')";


$resultadoInsercion2 = $dbi->Execute($insercion2);


if (!$resultadoInsercion2) {
    echo json_encode(array('error' => 'Error al Registrar el estudiante: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
    $dbi->FailTrans(); 
    exit;
}

}
*/
}

$dbi->close();
?>