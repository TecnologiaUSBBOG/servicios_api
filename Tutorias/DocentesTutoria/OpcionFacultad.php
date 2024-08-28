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


if(isset($_POST['DOC_DOC1'])){

    $facultad = $_POST['FACULTAD'];
$sql_facultad = "SELECT * FROM ACADEMICO.V_VAC_TUT_FACULTADES  ORDER BY
    CASE WHEN FACULTAD='$facultad' THEN 0 ELSE 1 END, FACULTAD , FACULTAD2";

$Ejecutar_Consulta = $dbi->Execute($sql_facultad);
$fquery = $Ejecutar_Consulta->recordCount();

if ($fquery > 0) {
    if ($Ejecutar_Consulta && !$Ejecutar_Consulta->EOF) {
        for ($i = 0; $i < $fquery; $i++) {
            $Valor_D = $Ejecutar_Consulta->fields["FACULTAD"];
            $Valor_R = $Ejecutar_Consulta->fields["FACULTAD2"];
            $resulta["FACULTAD"] = $Valor_D;
            $resulta["FACULTAD2"] = $Valor_R;
            $resus[] = $resulta;
            $Ejecutar_Consulta->MoveNext();
        }
        echo json_encode($resus);
    } else {
        $resulta["FACULTAD"] = "CARGANDO...";
        $resulta["FACULTAD2"] = "NO";
        $resus[] = $resulta;
        echo json_encode($resus);
    }
} else {
    if(isset($_POST['FACULTADESCOGIA'])){
    $resulta["FACULTAD"] = $_POST['FACULTADESCOGIA'];
    $resulta["FACULTAD2"] = $_POST['FACULTADESCOGIA'];
    $resus[] = $resulta;
    echo json_encode($resus);
    }else{
    $resulta["FACULTAD"] = "CARGANDO...";
    $resulta["FACULTAD2"] = "NO";
    $resus[] = $resulta;
    echo json_encode($resus);
    }

}

$dbi->close();
}
?>