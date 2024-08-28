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
    echo json_encode(array('error' => 'Error de conexión a la base de datos'));
    exit;
}*/

try {
    $dbi = NewADOConnection($motor_p);
    $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit;
}


//$Consulta_Programas = "SELECT DISTINCT PROGRAMA AS D, PROGRAMA AS R FROM ACADEMICO.APEX_LEGEND ORDER BY PROGRAMA ASC ";
$Consulta_Programas = "SELECT * FROM academico.v_tutorias_lista_programas ORDER BY D ASC";
$Ejecutar_Consulta = $dbi->Execute($Consulta_Programas);
$fquery = $Ejecutar_Consulta->recordCount();

if ($fquery > 0) {
    $resus = array();
    while (!$Ejecutar_Consulta->EOF) {
        $Valor_D = $Ejecutar_Consulta->fields["D"];
        $Valor_R = $Ejecutar_Consulta->fields["R"];

        $resulta = array(
            "D" => $Valor_D,
            "R" => $Valor_R
        );

        $resus[] = $resulta;

        $Ejecutar_Consulta->MoveNext();
    }

    echo json_encode($resus);

} else {
    $resulta["D"] = "NO";
    $resulta["R"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->Close();
