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

if (isset($_POST["ESTUDIANTE"])) {
    $CursoTutInformacionoria = $_POST['ESTUDIANTE'];

    $Consulta_InformacionEstudiante = "SELECT 'Documento '||documento||'/ Nombre '||primernombre||' '||segundonombre||' '||primerapellido||' '||segundoapellido dato,
    'Documento '||documento||'/ Nombre '||primernombre||' '||segundonombre||' '||primerapellido||' '||segundoapellido dato2 
    FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB 
        WHERE   (codigoestudiantil LIKE '%$CursoTutInformacionoria%') OR 
                (documento LIKE '%$CursoTutInformacionoria%') OR
                (primernombre LIKE '%$CursoTutInformacionoria%') OR 
                (segundonombre LIKE '%$CursoTutInformacionoria%') OR 
                (primerapellido LIKE '%$CursoTutInformacionoria%') OR 
                (segundoapellido LIKE '%$CursoTutInformacionoria%')";

    $Ejecutar_Consulta = $dbi->Execute($Consulta_InformacionEstudiante);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_DATOS1 = $Ejecutar_Consulta->fields["DATO"];
                $Valor_DATOS2 = $Ejecutar_Consulta->fields["DATO2"];
                $resulta["DATO"] = $Valor_DATOS1;
                $resulta["DATO2"] = $Valor_DATOS2;
                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["DATO"] = "No se encontraron resultados.";
            $resulta["DATO2"] = "NO";
            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        $resulta["DATO"] = "No se encontraron resultados.";
        $resulta["DATO2"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
    }
} else {
    $resulta["DATO"] = "No se encontraron resultados.";
    $resulta["DATO2"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}
