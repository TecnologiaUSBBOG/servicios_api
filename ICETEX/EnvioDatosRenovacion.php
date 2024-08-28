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
        echo json_encode(array("status" => "error", "message" => "Error al conectar con la base de datos. "));
        exit;
}*/

try {
        $dbi = NewADOConnection($motor_p);
        $dbi->Connect($base_p, $usuario_p, $contra_p);
} catch (Exception $e) {
        echo json_encode(["error" => "Error en la conexión a la base de datos"]);
        exit;
}

try {
        if (
                isset($_POST["DOCUMENTO"]) && isset($_POST["CICLO"]) &&
                isset($_POST["NOMBRES"]) && isset($_POST["APELLIDOS"]) &&
                isset($_POST["CORREO"]) && isset($_POST["TELEFONO"]) &&
                isset($_POST["COD_PROGRAMA"]) && isset($_POST["PROGRAMA"])
        ) {
                $DOCUMENTO      =   $_POST['DOCUMENTO'];
                $CICLO          =   $_POST['CICLO'];
                $NOMBRES        =   $_POST['NOMBRES'];
                $APELLIDOS      =   $_POST['APELLIDOS'];
                $CORREO         =   $_POST['CORREO'];
                $TELEFONO       =   $_POST['TELEFONO'];
                $COD_PROGRAMA   =   $_POST['COD_PROGRAMA'];
                $PROGRAMA       =   $_POST['PROGRAMA'];

                $GENERAR_ID = "SELECT MAX(ID) + 1 AS ID_PROCESO FROM ACADEMICO.PROCESOS_RENOVACION_TEMP";
                $Ejecutar_Generar_ID = $dbi->Execute($GENERAR_ID);
                $ID_PROCESO = $Ejecutar_Generar_ID->fields["ID_PROCESO"];

                $InsetarDatosRenovacion = "INSERT INTO ACADEMICO.PROCESOS_RENOVACION_TEMP 
                                    VALUES('$ID_PROCESO', '$DOCUMENTO', '$CICLO', '$NOMBRES', '$APELLIDOS', '$CORREO', '$TELEFONO', '$COD_PROGRAMA','$PROGRAMA', SYSDATE,'I','ICETEX','2')";

                $Ejecutar_Consulta = $dbi->Execute($InsetarDatosRenovacion);

                if ($Ejecutar_Consulta) {
                        echo json_encode(array("status" => "success", "message" => "Datos insertados correctamente.", "ID_PROCESO" => $ID_PROCESO));
                } else {
                        echo json_encode(array("status" => "error", "message" => "Error al insertar datos."));
                }
        } else {
                echo json_encode(array("status" => "error", "message" => "Datos incompletos."));
        }
} catch (Exception $e) {
        echo json_encode(array("status" => "error", "message" => "Error: " . $e->getMessage()));
}

$dbi->close();
