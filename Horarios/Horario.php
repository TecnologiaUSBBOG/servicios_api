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
}
*/

try {
        $dbi = NewADOConnection($motor_p);
        $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
        echo json_encode(["error" => "Error en la conexión a la base de datos"]);
        exit;
}

if (isset($_POST["Codigo"])) {
        $codi = $_POST['Codigo'];

        $Consulta_Horarios = "SELECT DISTINCT DATOS,
                                TO_CHAR(FECHA_INICIO, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') AS FECHA_INICIO_DIA, 
                                TO_CHAR(FECHA_INICIO, 'HH:MI PM') AS FECHA_INICIO, 
                                TO_CHAR(FECHA_FIN, 'HH:MI PM') AS FECHA_FIN 
                                FROM  ACADEMICO.APEX_LEGEND  
                                WHERE (NATIONAL_ID_DOC = '$codi' OR EMPLID='$codi' OR NATIONAL_ID='$codi') AND 
                                (NATIONAL_ID_DOC IS NOT NULL AND EMPLID IS NOT NULL AND NATIONAL_ID IS NOT NULL)  ORDER BY FECHA_INICIO DESC, FECHA_FIN DESC";

        $Ejecutar_Consulta = $dbi->Execute($Consulta_Horarios);

        $fquery = $Ejecutar_Consulta->recordCount();

        if ($fquery > 0) {
                if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {


                        for ($i = 0; $i < $fquery; $i++) {
                                $Valor_DATOS2 = $Ejecutar_Consulta->fields["DATOS"];
                                $Valor_FECHA_INICIO_DIA2 = $Ejecutar_Consulta->fields["FECHA_INICIO_DIA"];
                                $Valor_FECHA_INICIO2 = $Ejecutar_Consulta->fields["FECHA_INICIO"];
                                $Valor_FECHA_FIN2 = $Ejecutar_Consulta->fields["FECHA_FIN"];

                                $resulta["CANT_CONSULTA"] = $fquery;

                                $resulta["DATOS"] = $Valor_DATOS2;
                                $resulta["FECHA_INICIO_DIA"] = $Valor_FECHA_INICIO_DIA2;
                                $resulta["FECHA_INICIO"] = $Valor_FECHA_INICIO2;
                                $resulta["FECHA_FIN"] = $Valor_FECHA_FIN2;

                                $resus[] = $resulta;
                                $Ejecutar_Consulta->MoveNext();
                        }

                        echo json_encode($resus);
                } else {
                        $resulta["CANT_CONSULTA"] = "NO CLASS";
                        $resulta["DATOS"] = "NO CLASS";
                        $resulta["FECHA_INICIO_DIA"] = "NO";
                        $resulta["FECHA_INICIO"] = "NO";
                        $resulta["FECHA_FIN"] = "NO";

                        $resus[] = $resulta;
                        echo json_encode($resus);
                }
        } else {
                $resulta["CANT_CONSULTA"] = "NO CLASS";
                $resulta["DATOS"] = "NO CLASS";
                $resulta["FECHA_INICIO_DIA"] = "NO";
                $resulta["FECHA_INICIO"] = "NO";
                $resulta["FECHA_FIN"] = "NO";

                $resus[] = $resulta;

                // $resus.array_push($resulta);     
                echo json_encode($resus);
        }
} else {
        $resulta["CANT_CONSULTA"] = "NO CLASS";
        $resulta["DATOS"] = "NO CLASS";
        $resulta["FECHA_INICIO_DIA"] = "NO";
        $resulta["FECHA_INICIO"] = "NO";
        $resulta["FECHA_FIN"] = "NO";

        $resus[] = $resulta;

        // $resus.array_push($resulta);     
        echo json_encode($resus);
}
