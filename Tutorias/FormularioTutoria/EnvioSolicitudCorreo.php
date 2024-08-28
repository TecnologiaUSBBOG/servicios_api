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


$DOCUMENTO = $_POST['DOCUMENTO'];
$NOMB_APELL = $_POST["NOMBREAPELLIDO"];
$CORREO_INST = $_POST["CORREO_INST"];
$TIPO_TUTORIA = $_POST["TIPOTUTORIA"];
$INTEGRANTES = $_POST["INTEGRANTES"];
$CARRERA = $_POST["CARRERA"];
$MATERIA_TUTORIA = $_POST["MATERIA_TUTORIA"];
$DOC_PROFESOR = $_POST["CODIGOPROFESOR"];
$TEMATICA_TUTORIA = $_POST["TEMATICA_TUTORIA"];

$Consulta_ProfesorTutoria_CORRE = "SELECT NOMBREPROFESOR, CORREOPROFESOR FROM ACADEMICO.profesores_tutorias_tb WHERE DOCUMENTO='$DOC_PROFESOR' ";

$Consulta_firma = "select * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";

$Ejecutar_Consulta = $dbi->Execute($Consulta_ProfesorTutoria_CORRE);
$Ejecutar_Consulta1 = $dbi->Execute($Consulta_firma);

$fquery = $Ejecutar_Consulta->recordCount();

$Valor_NombProf = $Ejecutar_Consulta->fields["NOMBREPROFESOR"];
$Valor_firma = $Ejecutar_Consulta1->fields["TEXTO_FIRMA"];

$Valor_CorreoProf = $Ejecutar_Consulta->fields["CORREOPROFESOR"];

$mailr = 'tutoriasacademicas@usbbog.edu.co';

if ($TIPO_TUTORIA == "Grupal") {
    $asunto = "Solicitud de tutoría grupal";

    $text = "
    
    <html>
    <body>
        <p>Respetad@ Profesor@ <b>$Valor_NombProf</b>.</p>

        <p>Cordial saludo de paz y bien, </p>


        <p>Mi nombre es <b>$NOMB_APELL</b> del programa de <b>$CARRERA</b>.</p>

        <p> El motivo de mi correo es para solicitar una tutoría grupal en <b>$MATERIA_TUTORIA</b> con apoyo en la
            temática <b>$TEMATICA_TUTORIA</b>.</p>


        <style>
            #tableA {
                border: 1px solid black;
            }

            #columnaA {
                border: 1px solid black;
            }
        </style>

        <p> Datos del estudiante: </p>
        <table id='tableA'>
            <tr>
                <td id='columnaA'>Correo:</td>
                <td id='columnaA'><b>$CORREO_INST</b></td>
            </tr>
            <tr>
                <td id='columnaA'>Documento del solicitante:</td>
                <td id='columnaA'><b>$DOCUMENTO</b></td>
            </tr>
            <tr>
                <td id='columnaA'>Nombre integrantes:</td>
                <td id='columnaA'><b>$INTEGRANTES</b></td>
            </tr>

        </table>
        </br>
        Cordialmente,
        <br>
        <br>
        
    </body>
    </html>
    ".$Valor_firma;
    $correo4 = $CORREO_INST . ', ' . $Valor_CorreoProf;


    form_mail($correo4, $asunto, $text, $mailr);
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
