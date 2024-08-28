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


if (isset($_POST["CURSO"])) {
    $curso = $_POST["CURSO"];

    $sql_curso = 
     "SELECT DISTINCT
                            SUBSTR(DATOS,13,INSTR(DATOS,'Salón:')-14)MATERIA,
                            SUBSTR(DATOS,13,INSTR(DATOS,'Salón:')-14)MATERIA2
                            FROM ACADEMICO.APEX_LEGEND A JOIN ACADEMICO.PROFESORES_TUTORIAS_TB B ON B.DOCUMENTO = A.NATIONAL_ID_DOC 
                            AND SUBSTR(PROGRAMA,INSTR(PROGRAMA,'-')+2) = '$curso'
                            ORDER BY TRANSLATE(SUBSTR(DATOS,13,instr(DATOS,'Salón:')-14),
                            'ÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÔÄËÏÖÜÇÑÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ ',
                            'AEIOUAEIOUAOAEIOOAEIOUCNAEIOUAEIOUAOAEIOOAEIOUC')";
                            /*
                            "SELECT * from ACADEMICO.V_VAC_TUT_CURSO WHERE MATERIA ='$curso'";*/

    $Ejecutar_Consulta = $dbi->Execute($sql_curso);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_D = $Ejecutar_Consulta->fields["MATERIA"];
                $Valor_R = $Ejecutar_Consulta->fields["MATERIA2"];
                $resulta["MATERIA"] = $Valor_D;
                $resulta["MATERIA2"] = $Valor_R;
                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["MATERIA"] = "SELECCIONE UN PROGRAMA";
            $resulta["MATERIA2"] = "NO";
            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        if(isset($_POST['CURSO_ESCOGIO'])){
        $resulta["MATERIA"] =$_POST['CURSO_ESCOGIO'];
        $resulta["MATERIA2"] = $_POST['CURSO_ESCOGIO'];
        $resus[] = $resulta;
        echo json_encode($resus);
        }else{
        $resulta["MATERIA"] = "SELECCIONE UN PROGRAMA";
        $resulta["MATERIA2"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
        }
        
    }
} else {
    $resulta["MATERIA"] = "CARGANDO...";
    $resulta["MATERIA2"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}

$dbi->close();
