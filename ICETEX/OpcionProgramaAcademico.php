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


if (isset($_POST["PROG_ACADEMICO"])) {
    $prog_academico = $_POST["PROG_ACADEMICO"];

    $Consulta_ProgramaAcademico = "SELECT DISTINCT USB_NOM_FRML_ST AS D, ACAD_PROG AS R 
                                    FROM ACADEMICO.V_CAR_SF_VALMAT
                                    WHERE NIVEL = '$prog_academico'
                                    ORDER BY D";

    $Ejecutar_Consulta = $dbi->Execute($Consulta_ProgramaAcademico);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_D = $Ejecutar_Consulta->fields["D"];
                $Valor_R = $Ejecutar_Consulta->fields["R"];
                $resulta["D"] = $Valor_D;
                $resulta["R"] = $Valor_R;
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
} else {
    $resulta["D"] = "CARGANDO...";
    $resulta["R"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->close();
