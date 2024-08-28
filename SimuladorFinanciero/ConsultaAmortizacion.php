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

    $Consulta_ValMatriculan = "SELECT D.OPCFIN_DTL_NUMCUOTA,
                    TO_CHAR((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') * D.OPCFIN_DTL_PORCENTAJE / 100, 'FML999G999G999G999G990')  
                    AS CAPITAL,
                    '30' AS DIAS_CREDITO,
                    TO_CHAR(round(((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') - ((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') * (c.OPCFIN_CUOTA_INICIAL + nvl(
                        (select sum(i.OPCFIN_DTL_PORCENTAJE) from ACADEMICO.T_CAR_SF_OPCFIN_DTL i where  i.opcfin_id=d.opcfin_id and i.OPCFIN_DTL_NUMCUOTA>1 and i.OPCFIN_DTL_NUMCUOTA<=d.OPCFIN_DTL_NUMCUOTA group by i.opcfin_id),0))/100))* c.OPCFIN_TASA_INTCOR/100) 
                    , 'FML999G999G999G999G990')AS INT_COR,
                    TO_CHAR(round(((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') * D.OPCFIN_DTL_PORCENTAJE / 100) + (((SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') - ( (SELECT LC_VALOR_MATR FROM ACADEMICO.T_CAR_SF_VALMAT WHERE ACAD_PROG='$Prog_Academic') * (c.OPCFIN_CUOTA_INICIAL + nvl( (select sum(i.OPCFIN_DTL_PORCENTAJE) from ACADEMICO.T_CAR_SF_OPCFIN_DTL i where  i.opcfin_id=d.opcfin_id and i.OPCFIN_DTL_NUMCUOTA>1 and i.OPCFIN_DTL_NUMCUOTA<=d.OPCFIN_DTL_NUMCUOTA group by i.opcfin_id),0))/100) )* c.OPCFIN_TASA_INTCOR/100 )) 
                    , 'FML999G999G999G999G990') AS VALOR_CUOTA
                    FROM ACADEMICO.T_CAR_SF_OPCFIN_DTL D    INNER JOIN ACADEMICO.T_CAR_SF_OPCFIN C ON C.OPCFIN_ID=D.OPCFIN_ID WHERE C.OPCFIN_ID='$Opc_Financiacion'";

    error_log("SQL Query: " . $Consulta_ValMatriculan, 0);
    error_log("Programa ID: " . $Prog_Academic, 0);
    error_log("Financiacion ID: " . $Opc_Financiacion, 0);

    $Ejecutar_Consulta = $dbi->Execute($Consulta_ValMatriculan);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_NUMCUOTA_FINANCI =   $Ejecutar_Consulta->fields["OPCFIN_DTL_NUMCUOTA"];
                $Valor_CAPITAL          =   $Ejecutar_Consulta->fields["CAPITAL"];
                $Valor_DIAS_CREDITO     =   $Ejecutar_Consulta->fields["DIAS_CREDITO"];
                $Valor_InteresCorriente =   $Ejecutar_Consulta->fields["INT_COR"];
                $Valor_CUOTA            =   $Ejecutar_Consulta->fields["VALOR_CUOTA"];

                $resulta["Num_Cuotas"] = $Valor_NUMCUOTA_FINANCI;
                $resulta["Capital"] = $Valor_CAPITAL;
                $resulta["Dias_Credito"] = $Valor_DIAS_CREDITO;
                $resulta["Interes_Corrient"] = $Valor_InteresCorriente;
                $resulta["Valor_Cuota"] = $Valor_CUOTA;

                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["Num_Cuotas"]          = "NO HAY INFORMACION";
            $resulta["Capital"]             =   "NO HAY INFORMACION";
            $resulta["Dias_Credito"]        =   "NO HAY INFORMACION";
            $resulta["Interes_Corrient"]    =   "NO HAY INFORMACION";
            $resulta["Valor_Cuota"]         =   "NO HAY INFORMACION";

            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        $resulta["Num_Cuotas"]          = "NO HAY INFORMACION";
        $resulta["Capital"]             =   "NO HAY INFORMACION";
        $resulta["Dias_Credito"]        =   "NO HAY INFORMACION";
        $resulta["Interes_Corrient"]    =   "NO HAY INFORMACION";
        $resulta["Valor_Cuota"]         =   "NO HAY INFORMACION";

        $resus[] = $resulta;
        echo json_encode($resus);
    }
} else {
    $resulta["Num_Cuotas"]          = "NO HAY INFORMACION";
    $resulta["Capital"]             =   "NO HAY INFORMACION";
    $resulta["Dias_Credito"]        =   "NO HAY INFORMACION";
    $resulta["Interes_Corrient"]    =   "NO HAY INFORMACION";
    $resulta["Valor_Cuota"]         =   "NO HAY INFORMACION";

    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->close();
