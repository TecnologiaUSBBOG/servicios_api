
<?php
//DMCH
include("../../../bases_datos/adodb/adodb.inc.php");
include("../../../bases_datos/usb_defglobales.inc");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

/*$dbi = NewADOConnection("$motor_p");
$dbi->Connect($base_p, $usuario_p, $contra_p);
if (!$dbi) {
	echo "<br>error <br>";
	exit;
}*/

try {
    $dbi = NewADOConnection($motor_p);
    $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
    echo json_encode(["error" => "Error en la conexión a la base de datos"]);
    exit;
}

$iden = $_POST["DOCUMENTO"];
$nacimiento = $_POST["FECHA_NACIMIENTO"];
$tipo = $_POST["TIPO"];
$usuario_asis = $_POST["USUARIO_ASIS"];
$correo1 = $_POST["CORREO_INSTITUCIONAL"]; 
$correo2 = $_POST["CORREO_PERSONAL"]; 
$fecha_nacimiento = $_POST["FECHA_NACIMIENTO"];
$emplid = $_POST["CODIGO"]; 

$dbi->beginTrans();

$psswd = substr(md5(microtime()), 1, 8);
$psswd = 'C' . $psswd;


$qres1 = "INSERT INTO PS_USB_CAM_PAS_TBL@people_link (NATIONAL_ID_TYPE,NATIONAL_ID,OPRID,PASSWORD,DATETIME1,MAIL_ADDRESS1,process,errormsg) VALUES('$tipo','$iden','$usuario_asis','$psswd',sysdate,' ','N',' ')";

if (!$eqres1 = $dbi->Execute($qres1)) {
echo "error de insert";
$dbi->RollbackTrans();
die();
}

$Consulta_firma = "SELECT * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
$Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
$Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];


$dbi->CommitTrans();

$asunto = "Restablecimiento Contraseña ASIS";
$mailr = 'soporte@academia.usbbog.edu.co';
$text = "
<html>
			<head><title>Restablecimiento de clave - Estudiante  USBBOG</title></head>
			<body>
			<h4>Cordial saludo,</h4>
			<p>Apreciado Estudiante , nos permitimos confirmar sus credenciales de acceso al sistema ASIS:</p>
			<ul>
				<li type='disc'>Usuario: <b>$usuario_asis</b></li>
				<li type='disc'>Contraseña: <b>$psswd</b></li>
			</ul>
			<p>Intente ingresar 10 minutos despues de recibido este correo a través de la dirección https://campus.usbco.edu.co/</p>
			<p>Tenga en cuenta que estas credenciales SON DIFERENTES a las del Correo Institucional. Si tiene algún inconveniente envíe la solicitud al correo soporte@academia.usbbog.edu.co con los siguientes datos:</p>
			<ul>
				<li type='disc'>Número de documento.</li>
				<li type='disc'>Nombre completo.</li>
				<li type='disc'>Programa Académico.</li>
			</ul>
			<p>Gracias por la atención prestada, quedamos atentos a sus inquietudes.</p>
			Cordialmente,
			<br>
			<br>			
			</body>
		</html>".$Valor_firma;

form_mail($correo1, $asunto, $text, $mailr);

$sDe = "Soporte Live@Edu <soporte@academia.usbbog.edu.co>";

$sCabeceras = "From: " . $sDe . "\r\n";
$sCabeceras .= "MIME-version: 1.0\r\n";
$sCabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Función para enviar correo electrónico vía PHP utilizando como SMTP de salida el servidor onchange
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

if (mail($sPara, $sAsunto, $sTexto, $sCabeceras)) {
    echo "Correo enviado con éxito";
} else {
    echo "Error al enviar el correo";
}

$dbi->close();
