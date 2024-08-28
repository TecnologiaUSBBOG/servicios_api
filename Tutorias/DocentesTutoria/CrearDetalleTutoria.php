<?php
// DMCH
include_once "../../../../bases_datos/adodb/adodb.inc.php";
include_once "../../../../bases_datos/usb_defglobales.inc";

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

$id_sesion = (isset($_POST['NUMEROSESION'])) ? $_POST['NUMEROSESION'] : "";
$name_est = (isset($_POST['NOMBRE_EST'])) ? $_POST['NOMBRE_EST'] : "";
$doc_est = (isset($_POST['DOCUMENTO_EST'])) ? $_POST['DOCUMENTO_EST'] : "";
$cod_est = (isset($_POST['CODIGO_EST'])) ? $_POST['CODIGO_EST'] : "";
$asistencia = (isset($_POST['ASISTENCIA'])) ? $_POST['ASISTENCIA'] : "";
$actividad = (isset($_POST['ACTIVIDAD'])) ? $_POST['ACTIVIDAD'] : "";
$acuerdo = (isset($_POST['ACUERDO'])) ? $_POST['ACUERDO'] : "";
$fecha_inicio_tuto = (isset($_POST['FECH_INICIO'])) ? $_POST['FECH_INICIO'] : "";
$fecha_fin_tuto = (isset($_POST['FECH_FIN'])) ? $_POST['FECH_FIN'] : "";
$doc_doc = (isset($_POST['DOC_DOC'])) ? $_POST['DOC_DOC'] : "";
$ciclo = (isset($_POST['CICLO'])) ? $_POST['CICLO'] : "";
$curso = (isset($_POST['CURSO'])) ? $_POST['CURSO'] : "";

if (
    empty($id_sesion) || empty($ciclo) || empty($name_est) || empty($doc_est) || empty($cod_est) || empty($asistencia) || empty($actividad)
    || empty($acuerdo) || empty($fecha_inicio_tuto) || empty($fecha_fin_tuto) || empty($doc_doc))
{
    error_log("Algun dato está vacio", 0);
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

if($fecha_fin_tuto < $fecha_inicio_tuto){
    error_log("La Fecha de finalizacion no puede ser menor a la de inicio.", 0);
    echo json_encode(array('error' => 'La Fecha de finalizacion no puede ser menor a la de inicio.'));
    exit;
}

$insercion = "INSERT INTO ACADEMICO.DETALLADO_TUTORIAS_TB(ID_DETALLADO, NOMBREESTUDIANTE, DOCUMENTO, CODIGO, ACTIVIDADREALIZADA, ASISTENCIA, CALIFICACIONESTUDIANTE, SESION, DOCUMENTOP, ACUERDOSYCOMPROMISOS, INICIO_TUTORIA, FINAL_TUTORIA, PERIODOACADEMICO) 
                            VALUES((academico.secuencia_detallado_tutorias_tb.nextval), '$name_est', 
                            '$doc_est', '$cod_est', '$actividad', '$asistencia', 'No calificada', '$id_sesion', '$doc_doc', '$acuerdo', 
                            '$fecha_inicio_tuto', TO_TIMESTAMP('$fecha_fin_tuto', 'DD/MM/YYYY HH24:MI'), '$ciclo')";

$resultadoInsercion = $dbi->Execute($insercion);

if ($resultadoInsercion) {
    echo json_encode(array('succes' => 'Sesión creada correctamente'));
} else {
    echo json_encode(array('error' => 'Erro al crear la sesión: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
}

$sql_id = "SELECT MAX(ID_DETALLADO) AS ID_DET FROM ACADEMICO.DETALLADO_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est'";
$resultado_busqueda = $dbi->Execute($sql_id);
$id_det_tuto = $resultado_busqueda->fields['ID_DET'];

$Consulta_est = "SELECT CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est'";
$Ejecutar_Consulta2 = $dbi->Execute($Consulta_est);
$fquery = $Ejecutar_Consulta2->recordCount();
$email_est = $Ejecutar_Consulta2->fields["CORREOINSTITUCIONAL"];


$Consulta_profe = "SELECT NOMBREPROFESOR FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_doc'";
$Ejecutar_Consulta1 = $dbi->Execute($Consulta_profe);
$fquery = $Ejecutar_Consulta1->recordCount();
$name_profe = $Ejecutar_Consulta1->fields["NOMBREPROFESOR"];

$Consulta_firma = "select * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
$Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
$Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];

$mailr = 'tutoriasacademicas@usbbog.edu.co';

if ($resultadoInsercion && $asistencia == 'Si') {
    $asunto = "Calificación sesión de tutoria";

    $text = "
    <html>
    <p>Respetado estudiante <b>$name_est</b>.</p>
    <p>Cordial saludo de paz y bien, </p>
    <p>Este mensaje es para calificar la tutoría del curso <b>$curso</b> con el profesor(a) <b>$name_profe</b>.</p>
    <p>Puede acceder al formulario de calificación de tutoría haciendo clic <a href='http://apps.usbbog.edu.co:8080/prod/usbbogota/r/tutor%C3%ADas-acad%C3%A9micas-estudiantes/calificaci%C3%B3n-de-tutor%C3%ADa?ID_UNICO=$id_det_tuto'> aquí.</a></p>

    </br>
    Cordialmente,
    <br>
    <br>
    
    </body>
</html>	
        ".$Valor_firma;
    $correof = $email_est;


    form_mail($correof, $asunto, $text, $mailr);
}

function form_mail($sPara, $sAsunto, $sTexto1, $sDe)
{
    if ($sDe) $sCabeceras = "From:" . $sDe . "\n";
    else $sCabeceras = "";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    mail($sPara, $sAsunto, $sTexto1, $sCabeceras);
}

function form_mail1($sPara, $sAsunto, $sTexto, $sDe)
{
    $bHayFicheros = 0;
    $sCabeceraTexto = "";
    $sAdjuntos = "";

    if ($sDe) $sCabeceras = "From:" . $sDe . "\n";
    else $sCabeceras = "";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

    foreach ($_FILES as $vAdjunto) {
        if ($bHayFicheros == 0) {
            $bHayFicheros = 1;
            $sCabeceras .= "Content-type: multipart/mixed;";
            $sCabeceras .= "boundary=\"--_Separador-de-mensajes_--\"\n";

            $sCabeceraTexto = "----_Separador-de-mensajes_--\n";
            //$sCabeceraTexto .= "Content-type: text/plain;charset=iso-8859-1\n"; 
            $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n";

            $sTexto = $sCabeceraTexto . $sTexto;
        }
        if ($vAdjunto["size"] > 0) {
            $sAdjuntos .= "\n\n----_Separador-de-mensajes_--\n";
            $sAdjuntos .= "Content-type: " . $vAdjunto["type"] . ";name=\"" . $vAdjunto["name"] . "\"\n";;
            $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n";
            $sAdjuntos .= "Content-disposition: attachment;filename=\"" . $vAdjunto["name"] . "\"\n\n";

            $oFichero = fopen($vAdjunto["tmp_name"], 'r');
            $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"]));
            $sAdjuntos .= chunk_split(base64_encode($sContenido));
            fclose($oFichero);
        }
    }
    if ($bHayFicheros)
        $sTexto .= $sAdjuntos . "\n\n----_Separador-de-mensajes_----\n";

    return (mail($sPara, $sAsunto, $sTexto, $sCabeceras));
}


$dbi->close();
