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
        die('Error en la conexión a la base de datos: ' . $dbi->ErrorMsg());
}*/

try {
        $dbi = NewADOConnection($motor_p);
        $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
        echo json_encode(["error" => "Error en la conexión a la base de datos"]);
        exit;
}


// Función para obtener datos y enviar respuesta JSON
function obtenerDatos($resus)
{
        header('Content-Type: application/json');
        echo json_encode($resus);
        exit;
}
// Lógica para manejar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['NATIONAL_ID'])) {
                $NATIONAL_ID = $_POST['NATIONAL_ID'];

                $Conculta_InfoSolicitante =
                /*
                "SELECT DISTINCT * 
                        FROM (
                        SELECT DOCUMENTO AS NATIONAL_ID,NOMBRE AS NOMBRE_COMPLETO,SUBSTR(NOMBRE,1, INSTR(NOMBRE, ',')-1 )AS APELLIDOS,SUBSTR(NOMBRE, INSTR(NOMBRE, ',')+1 )AS NOMBRES,VINCULACION,EMAIL AS EMAIL_ADDR,TELEFONO2 AS PHONE,T1.PROGRAMA AS ACAD_PROG,T2.USB_NOM_FRML_ST AS PROGRAMA, T2.NIVEL, to_char(T2.LC_VALOR_MATR,'FML999G999G999G999G990') VALOR
                        FROM ACADEMICO.REPORTE_MATRICULADO_TB T1
                        FULL JOIN ACADEMICO.T_CAR_SF_VALMAT T2 ON T1.PROGRAMA = T2.ACAD_PROG
                        WHERE T1.documento ='$NATIONAL_ID' and ROWNUM=1
                ) RESULTADO";
                 */
                 "SELECT DISTINCT * 
                FROM (
                    SELECT DISTINCT T1.NATIONAL_ID, T1.NOMBRE_COMPLETO, T1.NOMBRES, T1.APELLIDOS, T1.VINCULACION, T1.EMAIL_ADDR, T1.PHONE, T1.ACAD_PROG, T2.USB_NOM_FRML_ST AS PROGRAMA, T2.NIVEL, to_char(T2.LC_VALOR_MATR,'FML999G999G999G999G990') VALOR
                    FROM ACADEMICO.T_CAR_SF_EST T1
                    FULL JOIN ACADEMICO.T_CAR_SF_VALMAT T2 ON T1.ACAD_PROG = T2.ACAD_PROG
                    WHERE T1.NATIONAL_ID = '$NATIONAL_ID'
                ) RESULTADO";
                
                $Consulta_Existe = "SELECT MAX(ID) AS MAX FROM ACADEMICO.PROCESOS_CARTERA WHERE DOCUMENTO = '$NATIONAL_ID' AND (ESTADO = 'I' OR ESTADO='P')";
                $Consulta_Ciclo = "SELECT CICLO FROM ACADEMICO.CICLOS_FINANCIACION WHERE ESTADO ='A'";

                $Ejecutar_Consulta_1 = $dbi->Execute($Conculta_InfoSolicitante);
                $Ejecutar_Consulta_2 = $dbi->Execute($Consulta_Existe);
                $Ejecutar_Consulta_3 = $dbi->Execute($Consulta_Ciclo);

                $fquery1 = $Ejecutar_Consulta_1->recordCount();
                $fquery2 = $Ejecutar_Consulta_2->recordCount();
                $fquery3 = $Ejecutar_Consulta_3->recordCount();

                if ($fquery1 >= 0) {
                        if ($Ejecutar_Consulta_1 && !$Ejecutar_Consulta_1->EOF) {

                                for ($i = 0; $i < $fquery1; $i++) {
                                        $Valor_Dato1 =  $Ejecutar_Consulta_1->fields["NATIONAL_ID"];
                                        $Valor_Dato2 =  $Ejecutar_Consulta_1->fields["NOMBRE_COMPLETO"];
                                        $Valor_Dato_nomb =  $Ejecutar_Consulta_1->fields["NOMBRES"];
                                        $Valor_Dato_apell =  $Ejecutar_Consulta_1->fields["APELLIDOS"];
                                        $Valor_Dato3 =  $Ejecutar_Consulta_1->fields["VINCULACION"];
                                        $Valor_Dato4 =  $Ejecutar_Consulta_1->fields["EMAIL_ADDR"];
                                        $Valor_Dato5 =  $Ejecutar_Consulta_1->fields["PHONE"];
                                        $Valor_Dato6 =  $Ejecutar_Consulta_1->fields["PROGRAMA"];
                                        $Valor_Dato7 =  $Ejecutar_Consulta_1->fields["ACAD_PROG"];
                                        $Valor_Dato8 =  $Ejecutar_Consulta_1->fields["NIVEL"];
                                        $Valor_Dato9=  $Ejecutar_Consulta_1->fields["VALOR"];

                                        $Valor_Consulta2 =  $Ejecutar_Consulta_2->fields["MAX"];
                                        $Valor_Consulta3 =  $Ejecutar_Consulta_3->fields["CICLO"];

                                        $resulta["NATIONAL_ID"]     =   $Valor_Dato1;
                                        $resulta["NOMBRE_COMPLETO"] =   $Valor_Dato2;
                                        $resulta["NOMBRES"]         =   $Valor_Dato_nomb;
                                        $resulta["APELLIDOS"]       =   $Valor_Dato_apell;
                                        $resulta["VINCULACION"]     =   $Valor_Dato3;
                                        $resulta["EMAIL_ADDR"]      =   $Valor_Dato4;
                                        $resulta["PHONE"]           =   $Valor_Dato5;
                                        $resulta["PROGRAMA"]        =   $Valor_Dato6;
                                        $resulta["ACAD_PROG"]       =   $Valor_Dato7;
                                        $resulta["NIVEL"]           =   $Valor_Dato8;
                                        $resulta["VALOR"]           =   $Valor_Dato9;

                                        $resulta["MAX"]             =   $Valor_Consulta2;
                                        $resulta["CICLO"]           =   $Valor_Consulta3;

                                        $resus[] = $resulta;
                                        $Ejecutar_Consulta_1->MoveNext();
                                }

                                obtenerDatos($resus);
                        } else {
                                $resulta["NATIONAL_ID"]     =   'NO';
                                $resulta["NOMBRE_COMPLETO"] =   'NO';
                                $resulta["NOMBRES"]         =   'INFORMACION NO VALIDA';
                                $resulta["APELLIDOS"]       =   'NO';
                                $resulta["VINCULACION"]     =   'NO';
                                $resulta["EMAIL_ADDR"]      =   'NO';
                                $resulta["PHONE"]           =   'NO';
                                $resulta["PROGRAMA"]        =   'NO';
                                $resulta["ACAD_PROG"]       =   'NO';
                                $resulta["NIVEL"]           =   'NO';
                                $resulta["VALOR"]           =   'NO';

                                $resulta["MAX"]             =   'NO';
                                $resulta["CICLO"]           =   'NO';

                                $resus[] = $resulta;
                        }

                                obtenerDatos($resus);
                } else {
                        $resulta["NATIONAL_ID"]     =   'NO';
                        $resulta["NOMBRE_COMPLETO"] =   'NO';
                        $resulta["NOMBRES"]         =   'INFORMACION NO VALIDA';
                        $resulta["APELLIDOS"]       =   'NO';
                        $resulta["VINCULACION"]     =   'NO';
                        $resulta["EMAIL_ADDR"]      =   'NO';
                        $resulta["PHONE"]           =   'NO';
                        $resulta["PROGRAMA"]        =   'NO';
                        $resulta["ACAD_PROG"]       =   'NO';
                        $resulta["NIVEL"]           =   'NO';
                        $resulta["VALOR"]           =   'NO';

                        $resulta["MAX"]             =   'NO';
                        $resulta["CICLO"]           =   'NO';

                        $resus[] = $resulta;
                        obtenerDatos($resus);
                }
        } else {
                $resulta["NATIONAL_ID"]     =   'NO';
                $resulta["NOMBRE_COMPLETO"] =   'NO';
                $resulta["NOMBRES"]         =   'INFORMACION NO VALIDA';
                $resulta["APELLIDOS"]       =   'NO';
                $resulta["VINCULACION"]     =   'NO';
                $resulta["EMAIL_ADDR"]      =   'NO';
                $resulta["PHONE"]           =   'NO';
                $resulta["PROGRAMA"]        =   'NO';
                $resulta["ACAD_PROG"]       =   'NO';
                $resulta["NIVEL"]           =   'NO';
                $resulta["VALOR"]           =   'NO';

                $resulta["MAX"]             =   'NO';
                $resulta["CICLO"]           =   'NO';

                $resus[] = $resulta;
                obtenerDatos($resus);
        }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        obtenerDatos([]);
}

$dbi->Close();
