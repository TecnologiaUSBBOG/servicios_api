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


if (isset($_POST["NATIONAL_ID"]) && isset($_POST["VINCULACION"])) {
        $NATIONAL_ID    = $_POST['NATIONAL_ID'];
        $VINCULACION    = $_POST['VINCULACION'];
        $CICLO    = $_POST['CICLO'];


        $Consulta_OpcFinanciacion = "SELECT OP.OPCFIN_DESCR AS D, OP.opcfin_id AS R FROM ACADEMICO.T_CAR_SF_OPCFIN OP
                        WHERE OP.OPCFIN_ID IN (SELECT OPT.OPCFIN_ID FROM 
                                                ACADEMICO.OPCFIN_TIPO_CARRERA OPT,
                                                ACADEMICO.T_CAR_SF_VALMAT V
                                                WHERE V.ACAD_PROG in (SELECT ACAD_PROG FROM   ACADEMICO.T_CAR_SF_EST WHERE NATIONAL_ID = '$NATIONAL_ID')
                                                AND OPT.ACAD_CAREER = V.ACAD_CAREER)
                                                AND OP.OPCFIN_ESTADO='A' AND 
                                                ((CASE WHEN '$VINCULACION' = 'CUPOS_DISPONIBLES_NUEVOS' THEN CUPOS_DISPONIBLES_NUEVOS ELSE CUPOS_DISPONIBLES_ANTIGUOS END) -
                                                (select count(*) from ACADEMICO.procesos_cartera where  replace(observaciones,'Crédito directo usb - ','') = OPCFIN_DESCR AND CICLO = '$CICLO' AND solicitud IN (CASE
                                                WHEN '$VINCULACION'  = 'CUPOS_DISPONIBLES_NUEVOS' THEN 'NUEVO'
                                                ELSE '$VINCULACION'  END))) > 0";


        $Ejecutar_Consulta = $dbi->Execute($Consulta_OpcFinanciacion);
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
                        $resulta["D"] = "NO";
                        $resulta["R"] = "NO";
                        $resus[] = $resulta;
                        echo json_encode($resus);
                }
        } else {
                $resulta["D"] = "NO";
                $resulta["R"] = "NO";
                $resus[] = $resulta;
                echo json_encode($resus);
        }
} else {
        $resulta["D"] = "NO";
        $resulta["R"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
}

$dbi->close();
