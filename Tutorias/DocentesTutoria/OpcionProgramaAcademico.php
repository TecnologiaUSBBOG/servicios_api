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


if (isset($_POST["PROG_ACADEMICO"])) {
    $facultad = $_POST["PROG_ACADEMICO"];
    error_log("variable: ".$facultad);
    $Consulta_ProgramaAcademico = "SELECT DISTINCT PROGRAMA, PROGRAMA2 from academico.V_VAC_TUT_PROGRAMA WHERE FACULTAD ='$facultad'";

    $Ejecutar_Consulta = $dbi->Execute($Consulta_ProgramaAcademico);
    $fquery = $Ejecutar_Consulta->recordCount();

    

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_D = $Ejecutar_Consulta->fields["PROGRAMA"];
                $Valor_R = $Ejecutar_Consulta->fields["PROGRAMA2"];
                $resulta["PROGRAMA"] = $Valor_D;
                $resulta["PROGRAMA2"] = $Valor_R;
                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["PROGRAMA"] ="SELECCION UNA FACULTAD" ;
            $resulta["PROGRAMA2"] = "NO";
            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        if(isset($_POST['PROGRAMA_ESCOGIO'])){
            $resulta["PROGRAMA"] =$_POST['PROGRAMA_ESCOGIO'] ;
            $resulta["PROGRAMA2"] = $_POST['PROGRAMA_ESCOGIO'];
            $resus[] = $resulta;
            echo json_encode($resus);
        }else{
            $resulta["PROGRAMA"] ="SELECCION UNA FACULTAD" ;
            $resulta["PROGRAMA2"] = "NO";
            $resus[] = $resulta;
            echo json_encode($resus);
        }
        
    }
} else {
    $resulta["PROGRAMA"] = "CARGANDO...";
    $resulta["PROGRAMA2"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->close();
