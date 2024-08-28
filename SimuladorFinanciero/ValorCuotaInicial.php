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
    echo "<br>error <br>";
    exit;
}*/

try {
    $dbi = NewADOConnection($motor_p);
    $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit;
}


if (isset($_POST["PROGRAMA_ACADEMICO"]) && isset($_POST['OPC_FINANCIACION'])) {

    $Prog_Academic = $_POST['PROGRAMA_ACADEMICO'];
    $Opc_Financiacion = $_POST['OPC_FINANCIACION'];

    $Consulta_DatosFinanciacion = "SELECT OPCFIN_ID, OPCFIN_DESCR,
        TO_CHAR((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') * OPCFIN_CUOTA_INICIAL / 100, 'FML999G999G999G999G990') AS CUOTA_INICIAL,
        TO_CHAR(OPCFIN_CUOTA_INICIAL, 'FM999,999,999,999,990.99') || '%' AS OPCFIN_CUOTA_INICIAL,
        OPCFIN_NUM_CUOTAS, 
        TO_CHAR(OPCFIN_TASA_INTCOR, 'FM999,999,999,999,990.99') || '%' AS OPCFIN_TASA_INTCOR, 
        TO_CHAR(OPCFIN_TASA_INTMOR, 'FM999,999,999,999,990.99') || '%' AS OPCFIN_TASA_INTMOR
    FROM ACADEMICO.T_CAR_SF_OPCFIN 
    WHERE OPCFIN_ID='$Opc_Financiacion'";

    // error_log("Error query: " . $Consulta_DatosFinanciacion, 0);
    // error_log("Programa ID: " . $Prog_Academic, 0);
    // error_log("Financiacion ID: " . $Opc_Financiacion, 0);

    $Ejecutar_Consulta = $dbi->Execute($Consulta_DatosFinanciacion);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_DATOS1 = $Ejecutar_Consulta->fields["OPCFIN_ID"];
                $Valor_DATOS2 = $Ejecutar_Consulta->fields["OPCFIN_DESCR"];
                $Valor_DATOS3 = $Ejecutar_Consulta->fields["CUOTA_INICIAL"];
                $Valor_DATOS4 = $Ejecutar_Consulta->fields["OPCFIN_CUOTA_INICIAL"];
                $Valor_DATOS5 = $Ejecutar_Consulta->fields["OPCFIN_NUM_CUOTAS"];
                $Valor_DATOS6 = $Ejecutar_Consulta->fields["OPCFIN_TASA_INTCOR"];
                $Valor_DATOS7 = $Ejecutar_Consulta->fields["OPCFIN_TASA_INTMOR"];

                $resulta["OPCFIN_ID"] = $Valor_DATOS1;
                $resulta["OPCFIN_DESCR"] = $Valor_DATOS2;
                $resulta["CUOTA_INICIAL"] = $Valor_DATOS3;
                $resulta["OPCFIN_CUOTA_INICIAL"] = $Valor_DATOS4;
                $resulta["NUM_CUOTAS"] = $Valor_DATOS5;
                $resulta["TASA_InteresCORRIENTE"] = $Valor_DATOS6;
                $resulta["OPCFIN_TASA_INTMOR"] = $Valor_DATOS7;

                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["OPCFIN_ID"] = 'NO';
            $resulta["OPCFIN_DESCR"] = 'NO';
            $resulta["CUOTA_INICIAL"] = 'NO';
            $resulta["opcfin_cuota_inicial"] = 'NO';
            $resulta["opcfin_num_cuotas"] = 'NO';
            $resulta["opcfin_tasa_intcor"] = 'NO';
            $resulta["opcfin_tasa_intmor"] = 'NO';

            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        $resulta["OPCFIN_ID"] = 'NO';
        $resulta["OPCFIN_DESCR"] = 'NO';
        $resulta["CUOTA_INICIAL"] = 'NO';
        $resulta["opcfin_cuota_inicial"] = 'NO';
        $resulta["opcfin_num_cuotas"] = 'NO';
        $resulta["opcfin_tasa_intcor"] = 'NO';
        $resulta["opcfin_tasa_intmor"] = 'NO';

        $resus[] = $resulta;
        echo json_encode($resus);
    }
} else {
    $resulta["OPCFIN_ID"] = 'NO';
    $resulta["OPCFIN_DESCR"] = 'NO';
    $resulta["CUOTA_INICIAL"] = 'NO';
    $resulta["opcfin_cuota_inicial"] = 'NO';
    $resulta["opcfin_num_cuotas"] = 'NO';
    $resulta["opcfin_tasa_intcor"] = 'NO';
    $resulta["opcfin_tasa_intmor"] = 'NO';

    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->close();
