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



$fecha_inicio_tuto = (isset($_POST['FECHA_INICIO'])) ? $_POST['FECHA_INICIO'] : "";
$fecha_fin_tuto = (isset($_POST['FECHA_FIN'])) ? $_POST['FECHA_FIN'] : "";
$actividad = (isset($_POST['ACTIVIDAD'])) ? $_POST['ACTIVIDAD'] : "";
$acuerdo = (isset($_POST['ACUERDOS'])) ? $_POST['ACUERDOS'] : "";
$asistencia = (isset($_POST['ASISTENCIA'])) ? $_POST['ASISTENCIA'] : "";
$id_sesion = (isset($_POST['NUMEROSESION'])) ? $_POST['NUMEROSESION'] : "";
$id_grupo = (isset($_POST['ID_GRUPO'])) ? $_POST['ID_GRUPO'] : "";
$doc_doc = (isset($_POST['DOC_DOC'])) ? $_POST['DOC_DOC'] : "";

if (
    empty($fecha_inicio_tuto) || empty($fecha_fin_tuto) || empty($actividad) ||
    empty($acuerdo) || empty($asistencia) || empty($id_sesion) || empty($id_grupo) || empty($doc_doc)
) {
    error_log("Algun dato está vacío", 0);
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

if($fecha_fin_tuto < $fecha_inicio_tuto){
    error_log("Las fechas no coinciden.", 0);
    echo json_encode(array('error' => 'La Fecha de finalizacion no puede ser menor a la de inicio.'));
    exit;
}
die();
$asistencia_data = json_decode($asistencia, true);

if ($asistencia_data === null) {
    echo json_encode(array('error' => 'Error al decodificar la asistencia JSON.'));
    exit;
}

foreach ($asistencia_data as $estudiante) {
    $cedula = $estudiante['CEDULA'];
    $nombres = $estudiante['NOMBRES'];
    $asistencia_estudiante = $estudiante['ASISTENCIA'];

    $Consulta_est = "SELECT CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$cedula'";
    $Ejecutar_Consulta2 = $dbi->Execute($Consulta_est);
    $fquery = $Ejecutar_Consulta2->recordCount();
    $email_est = $Ejecutar_Consulta2->fields["CORREOINSTITUCIONAL"];

    if (filter_var($email_est, FILTER_VALIDATE_EMAIL) && $asistencia_estudiante == 'Si') {
        $insercion = "INSERT INTO ACADEMICO.GRUPOS_DETALLADO_TUTORIAS VALUES (
                        (SELECT COUNT(ID_DETALLADO_GRUPOS_TUTO)+1 AS ID_DET_GRU_TUTO FROM ACADEMICO.GRUPOS_DETALLADO_TUTORIAS),
                        '$fecha_inicio_tuto', TO_TIMESTAMP('$fecha_fin_tuto', 'DD/MM/YYYY HH24:MI'),
                        (SELECT PRIMERNOMBRE ||' '|| SEGUNDONOMBRE ||' '|| PRIMERAPELLIDO ||' '|| SEGUNDOAPELLIDO AS NOMBRE FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$cedula'),
                        (SELECT CODIGOESTUDIANTIL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$cedula'),
                        '$cedula', '$actividad', '$acuerdo', '$asistencia_estudiante', 'No calificada', 0, 'No comentado', '$id_sesion', '$id_grupo')";

        $resultadoInsercion = $dbi->Execute($insercion);

        $sql_id_insertado = "SELECT ID_DETALLADO_GRUPOS_TUTO, NOMBREESTUDIANTE,
            (SELECT NOMBREDELCURSO FROM ACADEMICO.GRUPOS_SESIONES_TUTORIAS WHERE NUMEROSESION = '$id_sesion' AND  GRUPO = '$id_grupo') NOMBRECURSO,
            (SELECT NOMBREPROFESOR||' '||APELLIDOSPROFESOR FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_doc') NOMBREPROFE,
            (SELECT CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$cedula ') CORREO
            FROM ACADEMICO.GRUPOS_DETALLADO_TUTORIAS B WHERE SESION = '$id_sesion' AND ASISTENCIA = 'Si' AND GRUPO = '$id_grupo'";

        $resultado_busqueda = $dbi->Execute($sql_id_insertado);

        $id_det_gru_tuto = $resultado_busqueda->fields['ID_DETALLADO_GRUPOS_TUTO'];
        $curso = $resultado_busqueda->fields['NOMBRECURSO'];
        $profe = $resultado_busqueda->fields['NOMBREPROFE'];


        error_log("ULTIMO ID INSERTADO: ". $ultimo_id_insertado, 0);


        if (!$resultadoInsercion) {
            echo json_encode(array('error' => 'Error al insertar datos del estudiante: ' . $dbi->ErrorMsg()));
            error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
            exit;
        }
        $Consulta_firma = "SELECT * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
        $Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
        $Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];

        $asunto = "Calificación sesión de tutoría";
        $mensaje = "         
            <html>
            <p>Respetado estudiante <b>$nombres</b>.</p>
            <p>Cordial saludo de paz y bien, </p>
            <p>Este mensaje es para calificar la tutoría del curso <b>$curso</b> con el profesor(a) <b>$profe</b>.</p>
            <p>Puedes acceder al formulario de calificación de tutoría haciendo clic <a href='http://apps.usbbog.edu.co:8080/prod/usbbogota/r/tutor%C3%ADas-acad%C3%A9micas-estudiantes/calificaci%C3%B3n-de-tutor%C3%ADa?ID_UNICO_GRUPOS=$id_det_gru_tuto'>aquí. </a></p>
            <p>Tenga en cuenta que la calificación en totalmente anónima, y solo se usará para fines especificos.</p>
            </br>
            Cordialmente,
            <br>
            <br>
            
            </body>
            </html>".$Valor_firma;

        $correo_tutoria = 'tutoriasacademicas@usbbog.edu.co';
        form_mail($email_est, $asunto, $mensaje, $correo_tutoria);
    }
}

echo json_encode(array('success' => 'Sesión creada correctamente'));
$dbi->close();

// Función para enviar correo electrónico
function form_mail($sPara, $sAsunto, $sTexto, $sDe)
{
    $sCabeceras = "From:" . $sDe . "\n";
    $sCabeceras .= "MIME-version: 1.0\n";
    $sCabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    mail($sPara, $sAsunto, $sTexto, $sCabeceras);
}
