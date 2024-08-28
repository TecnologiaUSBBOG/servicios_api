<?php
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
$doc_est = (isset($_POST['ESTUDIANTES'])) ? $_POST['ESTUDIANTES'] : "";

error_log($id_sesion);
error_log($ciclo );
error_log($tipoTutoria);
error_log($facultad );
error_log($programa);
error_log($curso );
error_log($profesor );
error_log($tematica );
error_log($modalidad);  
error_log($metodologia );
error_log($fechatutoria );
error_log($lugar);
error_log($doc_est);
error_log($doc_doc);

// Primera inserción:
if (
    empty($id_sesion) || empty($ciclo) || empty($tipoTutoria) || empty($facultad) || empty($programa) || empty($curso) || empty($profesor)
    || empty($tematica) || empty($modalidad) || empty($metodologia) || empty($fechatutoria)
    || empty($lugar) || empty($doc_doc) || empty($doc_est)
) {
    echo json_encode(array('error' => 'Faltan parámetros'));
    exit;
}

$insercion = "UPDATE ACADEMICO.PROGRAMACION_TUTORIAS_TB SET FACULTAD='$facultad',PROGRAMA='$programa',NOMBREDELCURSO='$curso',TEMATICA='$tematica', MODALIDAD='$modalidad', FECHATUTORIA=TO_DATE('$fechatutoria', 'DD/MM/YYYY HH24:MI'),LUGAR='$lugar' WHERE NUMEROSESION='$id_sesion' AND DOCUMENTO='$doc_est' AND DOCUMENTOP='$doc_doc' AND PERIODOACADEMICO='$ciclo'";

$resultadoInsercion = $dbi->Execute($insercion);
//error_log($insercion);
$Consulta_firma = "select * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
$Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
$Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];

if ($resultadoInsercion) {
    echo json_encode(array('succes' => 'Sesión creada correctamente'));
} else {
    echo json_encode(array('error' => 'Erro al crear la sesión: ' . $dbi->ErrorMsg()));
    error_log("ERRO INSERCIÓN SQL: " . $dbi->ErrorMsg(), 0);
}

$Consulta_est = "SELECT PRIMERNOMBRE || ' ' || SEGUNDONOMBRE AS NOMBRE_EST, CORREOINSTITUCIONAL FROM ACADEMICO.ESTUDIANTES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_est'";

$Ejecutar_Consulta2 = $dbi->Execute($Consulta_est);
$fquery = $Ejecutar_Consulta2->recordCount();
$Valor_NombEst = $Ejecutar_Consulta2->fields["NOMBRE_EST"];
$Valor_CorreoEst = $Ejecutar_Consulta2->fields["CORREOINSTITUCIONAL"];


$Consulta_profe = "SELECT NOMBREPROFESOR, CORREOPROFESOR FROM ACADEMICO.PROFESORES_TUTORIAS_TB WHERE DOCUMENTO = '$doc_doc'";

$Ejecutar_Consulta1 = $dbi->Execute($Consulta_profe);
$fquery = $Ejecutar_Consulta1->recordCount();
$Valor_NombProf = $Ejecutar_Consulta1->fields["NOMBREPROFESOR"];
$Valor_CorreoProf = $Ejecutar_Consulta1->fields["CORREOPROFESOR"];

$mailr = 'tutoriasacademicas@usbbog.edu.co';

if ($resultadoInsercion) {
    $asunto = "RE-Programación sesión de tutoría";

    $text = "
    <html>

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
          <p>Este mensaje es para reprogramacion de su tutoría <b>$curso</b> con el profesor(a) <b>$Valor_NombProf</b>.</p>


        <table id='tableA' style='width:100%;'>
            <tr  id='filaA' >
                <td colspan='2'>Información de la tutoría</td>
            </tr>
            <tr id='filaA'>
                <td id='columnaA'><b>Numero de sesión:</b></td>
                <td id='columnaA' ><b>$id_sesion</b></td>
            </tr>
            <tr id='filaA' > 
                <td id='columnaA'><b>Tipo de tutoría:</b></td>
                <td id='columnaA'><b>$tipoTutoria</b></td>
            </tr>

            <tr  id='filaA'>
                <td id='columnaA'><b>Nombre del curso:</b></td>
                <td id='columnaA'><b>$curso</b></td>
            </tr>
            <tr >
                <td id='columnaA'><b>Profesor responsable:</b></td>
                <td id='columnaA'><b>$profesor</b></td>
            </tr>
            <tr  id='filaA'>
                <td id='columnaA'><b>Temática:</b></td>
                <td id='columnaA'><b>$tematica</b></td>
            </tr>
            <tr  id='filaA' >
                <td id='columnaA'><b>Modalidad:</b></td>
                <td id='columnaA'><b>$modalidad</b></td>
            </tr>    
            <tr id='filaA' >
                <td id='columnaA'><b>Fecha de la tutoría:</b></td>
                <td id='columnaA'><b>$fechatutoria</b></td>
            </tr>
            <tr id='filaA' >
                <td id='columnaA'><b>Lugar:</b></td>
                <td id='columnaA'><b>$lugar</b></td>
            </tr>
        </table>

        <p>Sí tiene alguna inquietud por favor comunicarse al correo del profesor: <b>$Valor_CorreoProf</b></p>
    
    </br>
    Cordialmente,
    <br>
    <br>
    
    </body>
</html>	
        ".$Valor_firma;
    $correof = $Valor_CorreoEst;


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




?>