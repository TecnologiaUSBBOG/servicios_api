<?php
// DMCH
include("../../../bases_datos/adodb/adodb.inc.php");
include("../../../bases_datos/usb_defglobales.inc");

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

$Consulta_Ciclos = " SELECT DESCR AS D, STRM AS R 
                        FROM PS_TERM_VAL_TBL@PEOPLE_LINK
                        WHERE STRM IN (SELECT CICLO FROM ACADEMICO.CICLOS_FINANCIACION WHERE ESTADO ='A' AND SUBSTR(CICLO, 3, 2) IN ('61', '66'))";

$Ejecutar_Consulta = $dbi->Execute($Consulta_Ciclos);
$fquery = $Ejecutar_Consulta->recordCount();

if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
                for ($i = 0; $i < $fquery; $i++) {
                        $Valor_DATOS1 = $Ejecutar_Consulta->fields["D"];
                        $Valor_DATOS2 = $Ejecutar_Consulta->fields["R"];

                        $resulta["D"] = $Valor_DATOS1;
                        $resulta["R"] = $Valor_DATOS2;
                        $resus[] = $resulta;
                        $Ejecutar_Consulta->MoveNext();
                }
                echo json_encode($resus);
        } else {
                $resulta["D"] = "CARGANDO...";
                $resulta["R"] = "NO";
                $resus[] = $resulta;
                echo json_encode($resus);
        }
} else {
        $resulta["D"] = "CARGANDO...";
        $resulta["R"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
}

$dbi->close();
