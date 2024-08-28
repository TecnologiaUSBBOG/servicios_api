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
    $doc_est = $_POST['CODIGO'];

    $Consulta_NivelAcade = "SELECT DISTINCT PROGRAMA FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est' ORDER BY PROGRAMA";
    $Ejecutar_Consulta = $dbi->Execute($Consulta_NivelAcade);

    if ($Ejecutar_Consulta) {
        $result = array();

        while (!$Ejecutar_Consulta->EOF) {
            $result[] = $Ejecutar_Consulta->fields['PROGRAMA'];
            $Ejecutar_Consulta->MoveNext();
        }
        echo json_encode($result);
    } else {
        echo json_encode(array('error' => 'Error al ejecutar la consulta SQL', 'sql_error' => $dbi->ErrorMsg()));
    }
} else {
    echo json_encode(array('error' => 'Código no proporcionado'));
}

$dbi->Close();
