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

    $Consulta_InformacionEstudiante = "SELECT 'Documento ' || DOCUMENTO ||'/ Nombre '|| PRIMERNOMBRE ||' '|| SEGUNDONOMBRE ||' '|| PRIMERAPELLIDO ||' '|| SEGUNDOAPELLIDO DATO,
                                        DOCUMENTO DOCUMENTO_EST, 
                                        PRIMERNOMBRE  ||' '|| SEGUNDONOMBRE ||' '|| PRIMERAPELLIDO ||' '|| SEGUNDOAPELLIDO NOMBRE
                                        FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB 
                                            WHERE   (CODIGOESTUDIANTIL LIKE '%$CursoTutInformacionoria%') OR 
                                                    (DOCUMENTO LIKE '%$CursoTutInformacionoria%') OR
                                                    (PRIMERNOMBRE LIKE '%$CursoTutInformacionoria%') OR 
                                                    (SEGUNDONOMBRE LIKE '%$CursoTutInformacionoria%') OR 
                                                    (PRIMERAPELLIDO LIKE '%$CursoTutInformacionoria%') OR 
                                                    (SEGUNDOAPELLIDO LIKE '%$CursoTutInformacionoria%')";

    $Ejecutar_Consulta = $dbi->Execute($Consulta_InformacionEstudiante);
    $fquery = $Ejecutar_Consulta->recordCount();

    if ($fquery > 0) {
        if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
            for ($i = 0; $i < $fquery; $i++) {
                $Valor_DATO = $Ejecutar_Consulta->fields["DATO"];
                $Valor_DOCUMENTO_EST = $Ejecutar_Consulta->fields["DOCUMENTO_EST"];
                $Valor_NOMBRE = $Ejecutar_Consulta->fields["NOMBRE"];

                $resulta["DATO"] = $Valor_DATO;
                $resulta["DOCUMENTO_EST"] = $Valor_DOCUMENTO_EST;
                $resulta["NOMBRE"] = $Valor_NOMBRE;

                $resus[] = $resulta;
                $Ejecutar_Consulta->MoveNext();
            }
            echo json_encode($resus);
        } else {
            $resulta["DATO"] = "No se encontraron resultados.";
            $resulta["DOCUMENTO_EST"] = "NO";
            $resulta["NOMBRE"] = "NO";
            $resus[] = $resulta;
            echo json_encode($resus);
        }
    } else {
        $resulta["DATO"] = "No se encontraron resultados.";
        $resulta["DOCUMENTO_EST"] = "NO";
        $resulta["NOMBRE"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
    }
} else {
    $resulta["DATO"] = "No se encontraron resultados.";
    $resulta["DOCUMENTO_EST"] = "NO";
    $resulta["NOMBRE"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
}
