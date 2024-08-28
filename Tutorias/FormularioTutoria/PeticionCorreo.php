<?php
// DMCH
include("../../../../bases_datos/adodb/adodb.inc.php");
include("../../../../bases_datos/usb_defglobales.inc");

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


if (isset($_POST["CODIGO"])) {
    $doc_est = $dbi->qstr($_POST['CODIGO']);

    $consultaCorreo = "SELECT CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = $doc_est";

    $ejecutarConsulta = $dbi->Execute($consultaCorreo);

    if ($ejecutarConsulta && $ejecutarConsulta->RecordCount() > 0) {
        $correoInstitucional = $ejecutarConsulta->fields['CORREOINSTITUCIONAL'];

        $result = array('CORREOINSTITUCIONAL' => $correoInstitucional);
        echo json_encode($result);
    } else {
        echo json_encode(array('error' => 'No se encontró un correo para el estudiante'));
    }
} else {
    echo json_encode(array('error' => 'No se proporcionó el código del estudiante'));
}

$dbi->Close();
?>


