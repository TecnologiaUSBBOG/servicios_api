<?php
//DMCH
include("../../../bases_datos/adodb/adodb.inc.php");
include("../../../bases_datos/usb_defglobales.inc");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dbi = NewADOConnection("$motor_p");
$dbi->Connect($base_p, $usuario_p, $contra_p);
if (!$dbi) {
	echo "<br>error <br>";
	exit;
}

if (isset($_POST["identificacion"]) && isset($_POST["fecha"])) {

	$iden = $_REQUEST['identificacion'];
	$nacimiento = $_REQUEST['fecha'];

	$consulta = "SELECT DOCUMENTO from ACADEMICO.excepcion_correo_inst where modalidad = 'Praes' and modalidad = 'PMF'";
	$sql_resultado = $dbi->Execute($consulta);
	$cont = $sql_resultado->recordCount();

	for ($i = 0; $i < $cont; $i++) {
		$praes_iden[$i] = $sql_resultado->fields['DOCUMENTO'];
		$sql_resultado->MoveNext();
	}

	for ($i = 0; $i < count($praes_iden); $i++) {
		if (strcmp($praes_iden[$i], $iden) == 0) {
			$nacimiento = '1900-01-01';
		}
	}

	$query = "SELECT A.USUARIO_ASIS,A.CORREO_INSTITUCIONAL,CORREO_PERSONAL,(select national_id_type tipo from ps_pers_nid@people_link where emplid=A.CODIGO AND PRIMARY_NID='Y') tipo,CODIGO,FECHA_NACIMIENTO  
		FROM academico.correo_inst A WHERE (A.DOCUMENTO = '$iden' OR CODIGO='$iden' ) AND A.FECHA_NACIMIENTO='$nacimiento'";

	$equery = $dbi->Execute($query);

	$fquery = $equery->recordCount();
	if ($fquery > 0) {

		if ($equery && !$equery->EOF) {
			$valor_Usuario_ASIS = $equery->fields[0];
			$valor_CorreoInst = $equery->fields[1];
			$valor_CorrPer = $equery->fields[2];
			$valor_tipo = $equery->fields[3]; 
			$valorCodigo = $equery->fields[4]; 
			$valor_FechaNaci = $equery->fields[5]; 

			$Prueba_JSON["USUARIO_ASIS"] = $valor_Usuario_ASIS;
			$Prueba_JSON["CORREO_INSTITUCIONAL"] = $valor_CorreoInst;
			$Prueba_JSON["CORREO_PERSONAL"] = $valor_CorrPer;
			$Prueba_JSON["TIPO"] = $valor_tipo;
			$Prueba_JSON["DOCUMENTO"] = $iden;
			$Prueba_JSON["CODIGO"] = $valorCodigo;
			$Prueba_JSON["FECHA_NACIMIENTO"] = $valor_FechaNaci;
			echo json_encode($Prueba_JSON);
		} else {
			$Prueba_JSON["USUARIO_ASIS"] =	'';
			$Prueba_JSON["CORREO_INSTITUCIONAL"] =	'';
			$Prueba_JSON["CORREO_PERSONAL"] =	'';
			$Prueba_JSON["TIPO"] = '';
			$Prueba_JSON["FECHA_NACIMIENTO"] = '';
			$Prueba_JSON["CODIGO"] = '';
			$Prueba_JSON["DOCUMENTO"] =	'';
			echo json_encode($Prueba_JSON);
		}
	} else {
		$Prueba_JSON["USUARIO_ASIS"] =	'';
		$Prueba_JSON["CORREO_INSTITUCIONAL"] =	'';
		$Prueba_JSON["CORREO_PERSONAL"] =	'';
		$Prueba_JSON["TIPO"] = '';
		$Prueba_JSON["FECHA_NACIMIENTO"] = '';
		$Prueba_JSON["CODIGO"] = '';
		$Prueba_JSON["DOCUMENTO"] =	'';
		echo json_encode($Prueba_JSON);
	};
} else {
	$Prueba_JSON["USUARIO_ASIS"] =	'';
	$Prueba_JSON["CORREO_INSTITUCIONAL"] =	'';
	$Prueba_JSON["CORREO_PERSONAL"] =	'';
	$Prueba_JSON["TIPO"] = '';
	$Prueba_JSON["FECHA_NACIMIENTO"] = '';
	$Prueba_JSON["CODIGO"] = '';
	$Prueba_JSON["DOCUMENTO"] =	'';
	echo json_encode($Prueba_JSON);
}

$dbi->close();
