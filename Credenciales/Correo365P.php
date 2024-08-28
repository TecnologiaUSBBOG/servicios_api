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


$seg = '*fO[p7uK1Rg*Y;*g';

$iden = $_POST["DOCUMENTO"];
$nacimiento = $_POST["FECHA_NACIMIENTO"];

$tipo = $_POST["TIPO"];
$usuario_asis = $_POST["NOMBRE_ASIS"];
$correo1 = $_POST["CORREO_INSTITUCIONAL"];
$cuenta = $_POST["CORREO_INSTITUCIONAL"];
$correo2 = $_POST["CORREO_PERSONAL"];
$cuenta_per = $_POST["CORREO_PERSONAL"];
$fecha_nacimiento = $_POST["FECHA_NACIMIENTO"];
$emplid = $_POST["CODIGO"];

$password = substr(md5(microtime()), 1, 8);
$password = 'C' . $password;

$ch = curl_init();
// definimos la URL a la que se hace la petición
curl_setopt($ch, CURLOPT_URL, "http://192.168.4.73:8080/correo/correo_prodi.php");
// indicamos el tipo de petición: POST
curl_setopt($ch, CURLOPT_POST, TRUE);
// definimos cada uno de los parámetros
curl_setopt($ch, CURLOPT_POSTFIELDS, "correo_inst=" . $cuenta . "&cont=" . $password . "&correo_per=" . $cuenta_per . "&seg=" . $seg);
// recibimos la respuesta y la guardamos en una variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$remote_server_output = curl_exec($ch);
curl_close($ch);

$Consulta_firma = "SELECT * from academico.T_CAR_FIRMAS  where ID_FIRMA = '11'";
$Ejecutar_Consulta3 = $dbi->Execute($Consulta_firma);
$Valor_firma = $Ejecutar_Consulta3->fields["TEXTO_FIRMA"];


$resul = substr($remote_server_output, 125);

$asunto = "Restablecimiento Contraseña Correo Institucional";
$mailr = 'soporte@academia.usbbog.edu.co';
$text = "
<html>
	<head><title>Restablecimiento de clave -  Profesor USBBOG</title></head>
				<body>
					<h4>Cordial saludo,</h4>
					<p>Apreciado Profesor , nos permitimos confirmar sus credenciales de acceso a Microsoft Office 365:</p>
					<ul>
						<li type='disc'>Correo: <b>$correo1</b></li>
						<li type='disc'>Contraseña: <b>$password</b></li>
					</ul>
					<p>Puede ingresar a través de la siguiente dirección: https://outlook.office365.com/owa/</p>
					<p>Tenga en cuenta que estas credenciales SON DIFERENTES a las del sistemas ASIS. Si tiene algún inconveniente envíe la solicitud al correo soporte@academia.usbbog.edu.co con los siguientes datos:</p>
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
            </html>	".$Valor_firma;

form_mail($correo2, $asunto, $text, $mailr);

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