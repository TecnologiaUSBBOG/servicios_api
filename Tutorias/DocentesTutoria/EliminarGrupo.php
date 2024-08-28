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


if (isset($_POST['ID_GRUPO']) && isset($_POST['DOC_DOC'])) {
    $idGrupo = $_POST['ID_GRUPO'];
    $doc_doc = $_POST['DOC_DOC'];


    if (!is_numeric($idGrupo)) {
        echo json_encode(array('error' => 'ID_GRUPO no es un número válido'));
        exit;
    }

    $idGrupo = intval($idGrupo);

    $sql_eliminar_grupo = "DELETE FROM ACADEMICO.GRUPOSTUTORIAS WHERE ID_GRUPO = '$idGrupo'";
    $result_eliminar_grupo = $dbi->Execute($sql_eliminar_grupo);

    $sql_eliminar_grupo_data = "DELETE FROM ACADEMICO.GRUPOS_SESIONES_TUTORIAS WHERE DOCUMENTOPROFESOR = '$doc_doc' AND GRUPO = '$idGrupo'";
    $result_eliminar_grupo_data = $dbi->Execute($sql_eliminar_grupo_data);

    if ($result_eliminar_grupo === false && $result_eliminar_grupo_data === false) {
        echo json_encode(array('error' => 'Error al eliminar el grupo'));
        exit;
    } else {
        echo json_encode(array('success' => 'Grupo eliminado exitosamente'));
    }
}
$dbi -> close();