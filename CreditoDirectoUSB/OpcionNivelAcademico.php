<?php
// DMCH
include_once "../../../bases_datos/adodb/adodb.inc.php";
include_once "../../../bases_datos/usb_defglobales.inc";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

/*$dbi = NewADOConnection("$motor_p");
$dbi->Connect($base_p, $usuario_p, $contra_p);*/

try {
    $dbi = NewADOConnection($motor_p);
    $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit;
}


if (!$dbi) {
    echo json_encode(array('error' => 'Error en la conexión a la base de datos'));
    exit;
}

$Consulta_NivelAcademico = "SELECT DISTINCT NIVEL AS D,NIVEL AS R FROM ACADEMICO.V_CAR_SF_VALMAT";

$Ejecutar_Consulta = $dbi->Execute($Consulta_NivelAcademico);

if ($Ejecutar_Consulta === false) {
    echo json_encode(array('error' => 'Error en la consulta a la base de datos'));
    exit;
}

$fquery = $Ejecutar_Consulta->RecordCount();

$resus = array();

if ($fquery > 0) {
    while (!$Ejecutar_Consulta->EOF) {
        $Valor_D = $Ejecutar_Consulta->fields["D"];
        $Valor_R = $Ejecutar_Consulta->fields["R"];
        $resulta["D"] = $Valor_D;
        $resulta["R"] = $Valor_R;
        $resus[] = $resulta;
        $Ejecutar_Consulta->MoveNext();
    }
} else {
    $resulta["D"] = "NO";
    $resulta["R"] = "NO";
    $resus[] = $resulta;
}

echo json_encode($resus);

$dbi->close();
