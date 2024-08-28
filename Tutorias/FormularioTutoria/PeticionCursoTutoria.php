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


if (isset($_POST["PROG_ACADEMICO"])) {
    $ProgAcad = $_POST['PROG_ACADEMICO'];
    /*$Consulta_CursoAcade = "SELECT DISTINCT REPLACE(SUBSTR(A.DATOS, 1, INSTR(DATOS, 'Salón:') -1),'Asignatura: ','') AS D, REPLACE(SUBSTR(A.DATOS, 1, INSTR(DATOS, 'Salón:') -1),'Asignatura: ','') AS R
                                    FROM ACADEMICO.APEX_LEGEND A 
                                    JOIN ACADEMICO.PROFESORES_TUTORIAS_TB B ON B.DOCUMENTO = A.NATIONAL_ID_DOC 
                                    JOIN ACADEMICO.ASIGNA_ACAD_DOCENTES AAD on  AAD.NIT = A.NATIONAL_ID_DOC 
                                    JOIN ACADEMICO.DETALLE_ACTIVIDAD_DOCENTES DAD on AAD.DAD_CODIGO = DAD.CODIGO
                                    JOIN ACADEMICO.FACULTADES C ON AAD.FAC_CODIGO = C.CODIGO
                                    AND AAD.PA_CODIGO = '360'
                                    AND  AAD.DAD_CODIGO = 704
                                    AND A.PROGRAMA = '$ProgAcad'
                                    ORDER BY TRANSLATE(REPLACE(SUBSTR(A.DATOS,1,INSTR(DATOS,'Salón:') -1),'Asignatura: ',''),
                                    'áéíóúàèìòùãõâêîôôäëïöüçñÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ ',
                                    'aeiouaeiouaoaeiooaeioucnAEIOUAEIOUAOAEIOOAEIOUC')  ";*/

    $Consulta_CursoAcade = "SELECT D, R FROM academico.v_tutorias_asignaturas
                            WHERE PROGRAMA = '$ProgAcad'
                            ORDER BY TRANSLATE(REPLACE(SUBSTR(DATOS, 1, INSTR(DATOS, 'Salón:') - 1), 'Asignatura: ', ''),
                            'áéíóúàèìòùãõâêîôôäëïöüçñÁÉÍÓÚÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ ',
                            'aeiouaeiouaoaeiooaeioucnAEIOUAEIOUAOAEIOOAEIOUC')";

    $Ejecutar_Consulta = $dbi->Execute($Consulta_CursoAcade);
    $fquery = $Ejecutar_Consulta->recordCount();

    $resus = array();
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
    echo json_encode(array('data' => $resus, 'error' => 'No se proporcionó PROG_ACADEMICO.'));
}

$dbi->Close();
