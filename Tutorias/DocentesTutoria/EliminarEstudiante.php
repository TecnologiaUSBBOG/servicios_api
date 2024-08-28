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


if (isset($_POST['ID_GRUPO']) && isset($_POST['DOC_DOC']) && isset($_POST['DOC_EST'])) {
    $doc_est = $_POST['DOC_EST'];
    $idGrupo = $_POST['ID_GRUPO'];
    $doc_doc = $_POST['DOC_DOC'];


    if (!is_numeric($doc_est)) {
        echo json_encode(array('error' => 'Estudaniante no es un número válido'));
        exit;
    }

    $idGrupo = intval($idGrupo);
   

    $sql_eliminar_grupo = "DELETE FROM ACADEMICO.GRUPOS_ESTUDIANTES_TUTORIAS WHERE GRUPO = '$idGrupo' AND DOCUMENTOPROFESOR='$doc_doc' AND CEDULA='$doc_est'";
    $result_eliminar_grupo = $dbi->Execute($sql_eliminar_grupo);

    if ($result_eliminar_grupo === false) {
        echo json_encode(array('error' => 'Error al eliminar el grupo'));
        exit;
    } else {
        echo json_encode(array('success' => 'Estudiante eliminado exitosamente'));
    }
}
$dbi -> close();
?>