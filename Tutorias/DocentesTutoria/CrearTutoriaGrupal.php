<?php
include_once "../../../../bases_datos/adodb/adodb.inc.php";
include_once "../../../../bases_datos/usb_defglobales.inc";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
file_put_contents('php://stderr', 'Received data: ' . json_encode($_POST) . PHP_EOL);

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



$id_sesion = (isset($_POST['NUMEROSESION'])) ? $_POST['NUMEROSESION'] : "";
$ciclo = (isset($_POST['PERIODOACADEMICO'])) ? $_POST['PERIODOACADEMICO'] : "";
$tipoTutoria = (isset($_POST['TIPOTUTORIA'])) ? $_POST['TIPOTUTORIA'] : "";
$facultad = (isset($_POST['FACULTAD'])) ? $_POST['FACULTAD'] : "";
$programa = (isset($_POST['PROGRAMA'])) ? $_POST['PROGRAMA'] : "";
$curso = (isset($_POST['NOMBREDELCURSO'])) ? $_POST['NOMBREDELCURSO'] : "";
$profesor = (isset($_POST['PROFESORRESPONSABLE'])) ? $_POST['PROFESORRESPONSABLE'] : "";
$tematica = (isset($_POST['TEMATICA'])) ? $_POST['TEMATICA'] : "";
$modalidad = (isset($_POST['MODALIDAD'])) ? $_POST['MODALIDAD'] : "";
$metodologia = (isset($_POST['METODOLOGIA'])) ? $_POST['METODOLOGIA'] : "";
$fechatutoria = (isset($_POST['FECHATUTORIA'])) ? $_POST['FECHATUTORIA'] : "";
$lugar = (isset($_POST['LUGAR'])) ? $_POST['LUGAR'] : "";
$doc_doc = (isset($_POST['DOCUMENTOP'])) ? $_POST['DOCUMENTOP'] : "";
$id_grupo = (isset($_POST['ID_GRUPO'])) ? $_POST['ID_GRUPO'] : "";

$estudiantesData = (isset($_POST['ESTUDIANTES'])) ? json_decode($_POST['ESTUDIANTES'], true) : [];

$Consulta_firma = "select * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
$Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
$Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];
// Primera inserción:
if (
    empty($id_sesion) || empty($ciclo) || empty($tipoTutoria) || empty($facultad) || empty($programa) || empty($curso) || empty($profesor)
    || empty($tematica) || empty($modalidad) || empty($metodologia) || empty($fechatutoria)
    || empty($lugar) || empty($doc_doc) || empty($id_grupo)
) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$dbi->StartTrans(); // Inicia la transacción

$insercion = "INSERT INTO ACADEMICO.GRUPOS_SESIONES_TUTORIAS(ID_SESIONES_GRUPALES, NUMEROSESION, PERIODOACADEMICO, TIPOTUTORIA, FACULTAD, PROGRAMA, NOMBREDELCURSO, PROFESORRESPONSABLE, TEMATICA, MODALIDAD, METODOLOGIA, FECHATUTORIA, LUGAR, DOCUMENTOPROFESOR, GRUPO) 
VALUES((SELECT MAX(ID_SESIONES_GRUPALES)+1 AS ID FROM ACADEMICO.GRUPOS_SESIONES_TUTORIAS), '$id_sesion', '$ciclo', '$tipoTutoria', '$facultad', '$programa', '$curso', '$profesor', '$tematica', '$modalidad', '$metodologia', TO_DATE('$fechatutoria', 'DD/MM/YYYY HH24:MI'), '$lugar', '$doc_doc', '$id_grupo')";

$resultadoInsercion = $dbi->Execute($insercion);

if (!$resultadoInsercion) {
    $dbi->FailTrans(); 
    echo json_encode(array('error' => 'Error al crear la sesión: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
    exit;
}
// Segunda inserción y envío de correos:
if (!empty($estudiantesData) && is_array($estudiantesData)) {
    foreach ($estudiantesData as $estudiante) {
        if (is_array($estudiante) && isset($estudiante['DOCUMENTO_EST'])) {
            $doc_est = $estudiante['DOCUMENTO_EST'];
            
            // Obtener datos de estudiantes y profesores
            $Consulta_est = "SELECT PRIMERNOMBRE || ' ' || SEGUNDONOMBRE AS NOMBRE_EST, CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est'";
            $Consulta_profe = "SELECT NOMBREPROFESOR, CORREOPROFESOR FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_doc'";

            $Ejecutar_Consulta2 = $dbi->Execute($Consulta_est);
            $Ejecutar_Consulta1 = $dbi->Execute($Consulta_profe);

            if ($Ejecutar_Consulta2 && $Ejecutar_Consulta1) {
                $Valor_NombEst = $Ejecutar_Consulta2->fields["NOMBRE_EST"];
                $Valor_CorreoEst = $Ejecutar_Consulta2->fields["CORREOINSTITUCIONAL"];
                $Valor_NombProf = $Ejecutar_Consulta1->fields["NOMBREPROFESOR"];
                $Valor_CorreoProf = $Ejecutar_Consulta1->fields["CORREOPROFESOR"];

                $mailr = 'tutoriasacademicas@usbbog.edu.co';

                if ($Valor_NombEst && $Valor_CorreoEst) {
                    $asunto = "Programación sesión de tutoría";
                    $text = "
                    <style>
                    #tableA{
                    border: 1px solid black;
                              border-collapse: collapse;
                              padding:10px;
                              text-align:left
                    }
            
                     #filaA{
                        border: 1px solid black;
                              border-collapse: collapse;
                              padding:10px;
                              text-align:left
                    }
            
                     #columnaA{
                        border: 1px solid black;
                              border-collapse: collapse;
                              padding:10px;
                              text-align:left
                    }
                    </style>
                
                    <p>Respetado estudiante <b>$Valor_NombEst</b>.</p>
                    <p>Cordial saludo de paz y bien, </p>
                    <p>Este mensaje es para confirmar su inscripción en la tutoría grupal de <b>$curso</b> con el profesor(a) <b>$Valor_NombProf</b>.</p>
            
            
                    <table id='tableA' style='width:100%;'>
                    <tr id='filaA'>
                        <td id='columnaA' colspan='2'><b>Información de la tutoría grupal</b></td>
                        
                    </tr>
                    <tr id='filaA'>
                        <td id='columnaA'><b>Numero de sesión</b></td>
                        <td id='columnaA'><b>$id_sesion</b></td>
                    </tr>
                    <tr id='filaA'> 
                        <td  id='columnaA'><b>Tipo de tutoría</b></td>
                        <td id='columnaA'><b>$tipoTutoria</b></td>
                    </tr>
            
                    <tr id='filaA'>
                        <td  id='columnaA' ><b>Nombre del curso</b></td>
                        <td  id='columnaA' ><b>$curso</b></td>
                    </tr>
                    <tr id='filaA'>
                        <td id='columnaA'  ><b>Profesor responsable</b></td>
                        <td id='columnaA' ><b>$profesor</b></td>
                    </tr>
                    
                    <tr id='filaA'>
                        <td id='columnaA' ><b>Temática</b></td>
                        <td id='columnaA' ><b>$tematica</b></td>
                    </tr>
                    <tr id='filaA' >
                        <td id='columnaA' ><b>Modalidad</b></td>
                        <td id='columnaA' ><b>$modalidad</b></td>
                    </tr>
            
                    <tr id='filaA'>
                        <td id='columnaA' ><b>Fecha de la tutoría</b></td>
                        <td id='columnaA' ><b>$fechatutoria</b></td>
                    </tr>
            
                    <tr id='filaA' >
                        <td id='columnaA' ><b>Lugar</b></td>
                        <td id='columnaA' ><b>$lugar</b></td>
                    </tr>
                    </table>
            
            
                    <p>Sí tiene alguna inquietud, por favor comunicarse al correo del profesor <b>$Valor_CorreoProf</b>.</p>
            
                    Cordialmente,
                    <br>
                    <br>
                    </body>
                    ".$Valor_firma ; 
    
                    form_mail($Valor_CorreoEst, $asunto, $text, $mailr);
                } else {
                    echo json_encode(array('error' => 'No se pudieron obtener datos de estudiantes para enviar el correo'));
                }
            } else {
                echo json_encode(array('error' => 'Error en las consultas para obtener datos de estudiantes y profesores'));
            }
            
        } else {
            echo json_encode(array('error' => '$estudiante no tiene las propiedades necesarias o no es un array válido'));
            error_log("$estudiante no tiene las propiedades necesarias o no es un array válido");
            $dbi->FailTrans(); 
            exit;
        }
    }
} else {
    echo json_encode(array('error' => '$estudiantesData no es un array válido'));
    error_log("estudiantesData no es un array válido");
    $dbi->FailTrans(); 
    exit;
}
$dbi->CompleteTrans(); 

$dbi->close();

function form_mail($sPara, $sAsunto, $sTexto1, $sDe)
{
    if ($sDe) $sCabeceras = "From:" . $sDe . "\n";
    else $sCabeceras = "";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    mail($sPara, $sAsunto, $sTexto1, $sCabeceras);
}
