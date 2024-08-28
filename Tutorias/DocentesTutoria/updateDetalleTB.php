<?php
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


$id_sesion = (isset($_POST['NUMEROSESION'])) ? $_POST['NUMEROSESION'] : "";
$codigo = (isset($_POST['CODIGOESTUDIANTE'])) ? $_POST['CODIGOESTUDIANTE'] : "";
$actividad = (isset($_POST['ACTIVIDAD'])) ? $_POST['ACTIVIDAD'] : "";
$acuerdos = (isset($_POST['ACUERDOS'])) ? $_POST['ACUERDOS'] : "";
$asistencia = (isset($_POST['ASISTENCIA'])) ? $_POST['ASISTENCIA'] : "";
$fecha_fin_tuto = (isset($_POST['FECH_FIN'])) ? $_POST['FECH_FIN'] : "";
error_log($actividad);
    error_log($acuerdos);
    error_log($asistencia);
    error_log($codigo);
    error_log($id_sesion); 
    error_log($fecha_fin_tuto);

   
// Primera inserción:
if (
    empty($id_sesion) || empty($codigo) || empty($actividad) || empty($acuerdos) || empty($asistencia)
) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$insercion = "UPDATE ACADEMICO.DETALLADO_TUTORIAS_TB SET ACTIVIDADREALIZADA='$actividad', 
ACUERDOSYCOMPROMISOS='$acuerdos', ASISTENCIA='$asistencia', FINAL_TUTORIA =TO_DATE('$fecha_fin_tuto', 'DD/MM/YYYY HH24:MI') WHERE CODIGO='$codigo' AND ID_DETALLADO='$id_sesion'";
$resultadoInsercion = $dbi->Execute($insercion);

if (!$resultadoInsercion) {
    $dbi->FailTrans(); 
    echo json_encode(array('error' => 'Error al actualizar la session: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
    error_log($actividad);
    error_log($acuerdos);
    error_log($asistencia);
    error_log($codigo);
    error_log($id_sesion); 
    
    exit;
}

$dbi -> close();

?>