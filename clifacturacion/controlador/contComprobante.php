<?php
date_default_timezone_set("America/Lima");
include __DIR__ . "/../vendor/autoload.php";
include_once __DIR__ . "/../modelo/mdlPropiedad.php";
include_once '../controlador/Algoritmos.php';
include_once '../modelo/mdlCabecera.php';
error_reporting(E_ERROR | E_PARSE);

$mdlPropiedad = new mdlPropiedad();
$propiedad = $mdlPropiedad->verPropiedad2("TIME_TOKEN");
define("TIME_TOKEN", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("KEY_TOKEN");
define("KEY_TOKEN", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("IGV");
define("IGV", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("WBSV_ENV_PRO");
define("WBSV_ENV_PRO", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("WBSV_ENV_PRU");
define("WBSV_ENV_PRU", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("WBSV_CON_PRO");
define("WBSV_CON_PRO", "");
$propiedad = $mdlPropiedad->verPropiedad2("USERNAME_SUNAT");
define("USERNAME_SUNAT", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("PASSWORD_SUNAT");
define("PASSWORD_SUNAT", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_BOL");
define("URL_ENV_BOL", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_FAC");
define("URL_ENV_FAC", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_NTC");
define("URL_ENV_NTC", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_NTD");
define("URL_ENV_NTD", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_RBB");
define("URL_ENV_RBB", $propiedad["valor_propiedad"]);
$propiedad = $mdlPropiedad->verPropiedad2("URL_ENV_CBB");
define("URL_ENV_CBB", $propiedad["valor_propiedad"]);


session_start();
///PARA FE
include '../modelo/mdlUsuario.php';
include '../modelo/mdlEmpresa.php';
$mdlEmpresa = new mdlEmpresa();
$mdlUsuario = new mdlUsuario();
$usuario = $mdlUsuario->verUsuario(2);
$empresa = $mdlEmpresa->verEmpresa($usuario["id_empresa"]);
$_SESSION["usuario"] = $usuario;
$_SESSION["empresa"] = $empresa;
$_SESSION["token"] = "";
$WSDL_AUTORIZACION = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'WSDL_AUTORIZACION'", array()))->valor_propiedad;
$_SESSION["Propiedad"]["WSDL_AUTORIZACION"] = $WSDL_AUTORIZACION;
$LIMITE_DE_LISTAS = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'LIMITE_DE_LISTAS'", array()))->valor_propiedad;
if(empty($LIMITE_DE_LISTAS) || $LIMITE_DE_LISTAS<=0){
    $LIMITE_DE_LISTAS = 20;
}
$LIMITE_DE_PAGINACION = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'LIMITE_DE_PAGINACION'", array()))->valor_propiedad;
if(empty($LIMITE_DE_PAGINACION) || $LIMITE_DE_PAGINACION<=0){
    $LIMITE_DE_PAGINACION = 9;
}
$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"] = $LIMITE_DE_LISTAS;
$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"] = $LIMITE_DE_PAGINACION;
///
$funcion = $_GET["funcion"];

switch ($funcion){
    case "tablaComprobantes":
        include '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $npag = $_GET["npag"];
        $fecComp = $_GET["fecComp"];
        $_SESSION["Filtros"]["tablaEmpresas"] = array(
            "user"=>$user,
            "nombre"=>$nombre,
            "nrodoc"=>$nrodoc,
            "tipo"=>$tipo,
            "estado"=>$estado
        );
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes2($fecini,$fecfin,$id_empresa,$nombre,$estado,$tipodoc,$npag,$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"],$fecComp);
        $n = $comprobantes[1];
        $comprobantes = $comprobantes[0];
        $Npaginas= intval(($n)/$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]+(($n%$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]==0)?0:1));
        $i = 1;
        $limit = $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"];
        $tabla = array();
        $detalles = array();
        $clases = array();
        $w=0;
        foreach ($comprobantes as $comprobante) {
            $comprobante["data_solicitud"] = json_decode($comprobante["data_solicitud"],true);
            $tabla[$w][] = ($limit*($npag-1))+$i;$i++;
            if($comprobante["tipo_documento"]=="E"){
                if($comprobante["data_solicitud"]["tiporesumen"]=="1"){
                    $tabla[$w][] = "DECLARACION DE BOLETAS";
                }elseif($comprobante["data_solicitud"]["tiporesumen"]=="3"){
                    $tabla[$w][] = "ANULACION DE BOLETAS";
                }elseif($comprobante["data_solicitud"]["tiporesumen"]=="2"){
                    $tabla[$w][] = "MODIFICACION DE BOLETAS";
                }
            }else{
                $tabla[$w][] = ($comprobante["tipo_documento"]=="A")?("COMUNICACION DE BAJAS"):(($comprobante["tipo_documento"]=="B")?("BOLETA"):(($comprobante["tipo_documento"]=="C")?("NOTA DE CREDITO"):(($comprobante["tipo_documento"]=="D")?("NOTA DE DEBITO"):(($comprobante["tipo_documento"]=="E")?("RESUMEN DE BOLETAS"):(($comprobante["tipo_documento"]=="F")?("FACTURA"):("GUIA DE REMISION"))))));
            }
            $tabla[$w][] = $comprobante["serie"]."-".str_pad($comprobante["correlativo"],8,"0",STR_PAD_LEFT);
            if($comprobante["tipo_documento"]=="A" || $comprobante["tipo_documento"]=="E"){
                $tabla[$w][] = empty($comprobante["ticket_solicitud"])?"-":$comprobante["ticket_solicitud"];
                $tabla[$w][] = (strlen($comprobante["data_solicitud"]["fechareferencia"])>0)?date_format(date_create($comprobante["data_solicitud"]["fechareferencia"]), "d/m/Y"):"-";
            }else{
                $tabla[$w][] = empty($comprobante["doc_cliente"])?"-":$comprobante["doc_cliente"];
                $tabla[$w][] = empty($comprobante["nombre_cliente"])?"-":$comprobante["nombre_cliente"];
                $tabla[$w][] = $comprobante["total_doc"];
            }
            //$tabla[$w][] = date_format(date_create($comprobante["fechahora_solicitud"]), "d/m/Y H:i:s");
            $tabla[$w][] = date_format(date_create($comprobante["data_solicitud"]["fechaemision"]), "d/m/Y");
            $tabla[$w][] = (strlen($comprobante["fechahora_envio"])>0)?date_format(date_create($comprobante["fechahora_envio"]), "d/m/Y H:i:s"):"-";
            $tabla[$w][] = (strlen($comprobante["fechahora_respuesta"])>0)?date_format(date_create($comprobante["fechahora_respuesta"]), "d/m/Y H:i:s"):"-";
            if($comprobante["tipo_documento"]=="A" || $comprobante["tipo_documento"]=="E"){
                if($comprobante["estado_solicitud"]=="T" || $comprobante["estado_solicitud"]=="C"){
                    $tabla[$w][] = "AUN NO COMPROBADO";
                    $clases[] = ' class="yellow" ';
                }elseif($comprobante["estado_solicitud"]=="U"){
                    $tabla[$w][] = "COMPROBADO Y ACEPTADO";
                    $clases[] = ' class="green" ';
                }elseif($comprobante["estado_solicitud"]=="V"){
                    $tabla[$w][] = "COMPROBADO Y RECHAZADO";
                    $clases[] = ' class="red" ';
                }elseif($comprobante["estado_solicitud"]=="R"){
                    $tabla[$w][] = "AUN NO ENVIADO";
                    $clases[] = ' class="yellow" ';
                }else{
                    $tabla[$w][] = $comprobante["estado_solicitud"];
                    $clases[] = '';
                }
            }else{
                if($comprobante["estado_solicitud"]=="R"){
                    $tabla[$w][] = "AUN NO DECLARADO";
                    $clases[] = ' class="yellow" ';
                }elseif($comprobante["estado_solicitud"]=="E" || $comprobante["estado_solicitud"]=="P"){
                    $tabla[$w][] = "NO REGISTRADO";
                    $clases[] = ' class="pink" ';
                }elseif($comprobante["estado_solicitud"]=="M" || $comprobante["estado_solicitud"]=="T"){
                    $tabla[$w][] = "DECLARADO Y ACTIVO";
                    $clases[] = ' class="green" ';
                }elseif($comprobante["estado_solicitud"]=="B"){
                    $tabla[$w][] = "DECLARADO Y ANULADO";
                    $clases[] = ' class="orange" ';
                }elseif($comprobante["estado_solicitud"]=="I"){
                    $tabla[$w][] = "DECLARADO E INCORRECTO";
                    $clases[] = ' class="red" ';
                }elseif($comprobante["estado_solicitud"]=="S" || $comprobante["estado_solicitud"]=="C"){
                    $tabla[$w][] = "DECLARADO SIN COMPROBAR";
                    $clases[] = ' class="blue" ';
                }else{
                    $tabla[$w][] = $comprobante["estado_solicitud"];
                    $clases[] = ' class="" ';
                }
                //$tabla[$w][] = ($comprobante["estado_solicitud"]=="R")?("AUN NO DECLARADO"):(($comprobante["estado_solicitud"]=="M" || $comprobante["estado_solicitud"]=="T")?("DECLARADO Y ACTIVO"):(($comprobante["estado_solicitud"]=="B")?("DECLARADO Y ANULADO"):($comprobante["estado_solicitud"]=="I")?("DECLARADO E INCORRECTO"):("-")));
            }
            //$tabla[$w][] = '<button class="btn btn-floating purple" onclick="modalDocumentos(\''.$comprobante["id_solicitud"].'\');"><i class="material-icons">attach_file</i></button>';
            if($comprobante["tipo_documento"]=="A" || $comprobante["tipo_documento"]=="E"){
                $tabla[$w][] = '<button class="btn btn-floating purple" onclick="modalComprobantes(\''.$comprobante["id_solicitud"].'\');"><i class="material-icons">list</i></button>';
                $tabla[$w][] = '<button class="btn btn-floating blue" onclick="modalErrores(\''.$comprobante["id_solicitud"].'\');"><i class="material-icons">list</i></button>';
                if($comprobante["estado_solicitud"]=="T" || $comprobante["estado_solicitud"]=="C"){
                    $tabla[$w][] = '<button class="btn btn-floating red" onclick="AlertaEliminar(\'¿ENVIAR TICKET?\',\'../controlador/contComprobante.php?funcion=consultarStatus\',\'id_solicitud='.$comprobante["id_solicitud"].'&ticket='.$comprobante["ticket_solicitud"].'\',\'buscar();\');"><i class="material-icons">autorenew</i></button>';
                }else{
                    $tabla[$w][] = '';
                }
            }else{
                if(($comprobante["estado_solicitud"]=="R" || $comprobante["estado_solicitud"]=="E" || $comprobante["estado_solicitud"]=="P") && ($comprobante["tipo_documento"]=="F" || $comprobante["tipo_documento"]=="B")){
                    if($comprobante["tipo_documento"]=="F"){
                        //$tabla[$w][] = '<button class="btn btn-floating green" onclick="redireccionar(\'../vista/frmFactura.php?modo=E\',\'id_solicitud='.$comprobante["id_solicitud"].'\');"><i class="material-icons">edit</i></button>';
                    }elseif($comprobante["tipo_documento"]=="B"){
                        //$tabla[$w][] = '<button class="btn btn-floating green" onclick="redireccionar(\'../vista/frmBoleta.php?modo=E\',\'id_solicitud='.$comprobante["id_solicitud"].'\');"><i class="material-icons">edit</i></button>';
                    }
                }else{
                    //$tabla[$w][] = '';
                }
                if(($comprobante["estado_solicitud"]=="S" || $comprobante["estado_solicitud"]=="C") && ($comprobante["tipo_documento"]=="F" || $comprobante["tipo_documento"]=="B" || $comprobante["tipo_documento"]=="C" || $comprobante["tipo_documento"]=="D")){
                    $tabla[$w][] = '<button class="btn btn-floating purple" onclick="AlertaEliminar(\'¿COMPROBAR ESTADO?\',\'../controlador/contComprobante.php?funcion=comprobarCDR\',\'id_solicitud='.$comprobante["id_solicitud"].'\',\'buscar();\');"><i class="material-icons">autorenew</i></button>';
                }else{
                    $tabla[$w][] = '';
                }
                $tabla[$w][] = '<button class="btn btn-floating blue" onclick="modalErrores(\''.$comprobante["id_solicitud"].'\');"><i class="material-icons">list</i></button>';
                $tabla[$w][] = '<button class="btn btn-floating red" onclick="window.open(\'../controlador/contComprobante.php?funcion=generarPDF&id_solicitud='.$comprobante["id_solicitud"].'\');"><i class="material-icons">description</i></button>';
                $tabla[$w][] = '<button class="btn btn-floating red" onclick="imprimir(\''.$comprobante["id_solicitud"].'\');"><i class="material-icons">print</i></button>';
                if($comprobante["tipo_documento"]!="G"){
                    $tabla[$w][] = '<button class="btn btn-floating blue" onclick="redireccionar(\'frmEmail.php?idsolicitud='.$comprobante["id_solicitud"].'\');"><i class="material-icons">email</i></button>';
                }else{
                    $tabla[$w][] = '';
                }
            }
            //$tabla[$w][] = '<button class="btn btn-floating red" onclick="AlertaEliminar(\'¿ESTAS SEGURO QUE DESEAS ELIMINAR EL EMPRESA?\',\'../controlador/contEmpresa.php?funcion=eliminarEmpresa\',\'id_empresa='.$empresa["id_empresa"].'\',\'buscar();\')"><i class="material-icons">delete_forever</i></button>';
            $datos = array(
                array("EMPRESA",$empresa["user_empresa"]),
            );
            $detalles[] = str_replace("+","%20",urlencode(json_encode($datos)));
            $w++;
        }
        $parametros = $_GET;
        $href = "../controlador/contComprobante.php?";
        foreach ($parametros as $key => $value) {
            if($key!="npag" && $key!="_"){
                $href.= $key."=".$value."&";
            }
        }
        echo json_encode(array(
            "correcto"=>true,
            "datos"=>$tabla,
            "clases"=>$clases,
            "detalles"=>$detalles,
            "npag"=>$npag,
            "Npaginas"=>$Npaginas,
            "Npaginacion"=>$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"],
            "href"=>$href
        ));
        break;
    case "tablaComprobantes2":
        include '../modelo/mdlSolicitud.php';
        $id_solicitud = $_GET["id_solicitud"];
        $npag = $_GET["npag"];
        $mdlSolicitud = new mdlSolicitud();
        $solicitud = $mdlSolicitud->verSolicitud($id_solicitud);
        $data_solicitud = json_decode($solicitud['data_solicitud']);
        $detalles = $data_solicitud->detalles;
        $comprobantes = array();
        foreach ($detalles as $key => $value) {
            $comprobantes[] = $mdlSolicitud->verSolicitud($value->id);
        }
        //echo json_encode($comprobantes);exit;
        $n = count($comprobantes);
        $Npaginas= intval(($n)/$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]+(($n%$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]==0)?0:1));
        $comprobantes = array_chunk($comprobantes, $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]);
        $comprobantes = $comprobantes[$npag-1];
        $i = 1;
        $limit = $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"];
        $tabla = array();
        $detalles = array();
        $clases = array();
        $w=0;
        foreach ($comprobantes as $comprobante) {
            $comprobante["data_solicitud"] = json_decode($comprobante["data_solicitud"],true);
            $tabla[$w][] = ($limit*($npag-1))+$i;$i++;
            $tabla[$w][] = ($comprobante["tipo_documento"]=="A")?("COMUNICACION DE BAJAS"):(($comprobante["tipo_documento"]=="B")?("BOLETA"):(($comprobante["tipo_documento"]=="C")?("NOTA DE CREDITO"):(($comprobante["tipo_documento"]=="D")?("NOTA DE DEBITO"):(($comprobante["tipo_documento"]=="E")?("RESUMEN DE BOLETAS"):(($comprobante["tipo_documento"]=="F")?("FACTURA"):("-"))))));
            $tabla[$w][] = $comprobante["serie"]."-".str_pad($comprobante["correlativo"],8,"0",STR_PAD_LEFT);
            
            $tabla[$w][] = empty($comprobante["doc_cliente"])?"-":$comprobante["doc_cliente"];
            $tabla[$w][] = empty($comprobante["nombre_cliente"])?"-":$comprobante["nombre_cliente"];
            $tabla[$w][] = $comprobante["total_doc"];
            //$tabla[$w][] = date_format(date_create($comprobante["fechahora_solicitud"]), "d/m/Y H:i:s");
            $tabla[$w][] = date_format(date_create($comprobante["data_solicitud"]["fechaemision"]), "d/m/Y");
            $tabla[$w][] = (strlen($comprobante["fechahora_envio"])>0)?date_format(date_create($comprobante["fechahora_envio"]), "d/m/Y H:i:s"):"-";
            $tabla[$w][] = (strlen($comprobante["fechahora_respuesta"])>0)?date_format(date_create($comprobante["fechahora_respuesta"]), "d/m/Y H:i:s"):"-";
                if($comprobante["estado_solicitud"]=="R"){
                    $tabla[$w][] = "AUN NO DECLARADO";
                    $clases[] = ' class="yellow" ';
                }elseif($comprobante["estado_solicitud"]=="E" || $comprobante["estado_solicitud"]=="P"){
                    $tabla[$w][] = "NO REGISTRADO";
                    $clases[] = ' class="pink" ';
                }elseif($comprobante["estado_solicitud"]=="M" || $comprobante["estado_solicitud"]=="T"){
                    $tabla[$w][] = "DECLARADO Y ACTIVO";
                    $clases[] = ' class="green" ';
                }elseif($comprobante["estado_solicitud"]=="B"){
                    $tabla[$w][] = "DECLARADO Y ANULADO";
                    $clases[] = ' class="orange" ';
                }elseif($comprobante["estado_solicitud"]=="I"){
                    $tabla[$w][] = "DECLARADO E INCORRECTO";
                    $clases[] = ' class="red" ';
                }elseif($comprobante["estado_solicitud"]=="S" || $comprobante["estado_solicitud"]=="C"){
                    $tabla[$w][] = "DECLARADO SIN COMPROBAR";
                    $clases[] = ' class="blue" ';
                }else{
                    $tabla[$w][] = $comprobante["estado_solicitud"];
                    $clases[] = ' class="" ';
                }

            $datos = array(
                array("EMPRESA",$empresa["user_empresa"]),
            );
            $detalles[] = str_replace("+","%20",urlencode(json_encode($datos)));
            $w++;
        }
        $parametros = $_GET;
        $href = "../controlador/contComprobante.php?";
        foreach ($parametros as $key => $value) {
            if($key!="npag" && $key!="_"){
                $href.= $key."=".$value."&";
            }
        }
        echo json_encode(array(
            "correcto"=>true,
            "datos"=>$tabla,
            "clases"=>$clases,
            "detalles"=>$detalles,
            "npag"=>$npag,
            "Npaginas"=>$Npaginas,
            "Npaginacion"=>$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"],
            "href"=>$href
        ));
        break;
    case "tablaErrores":
        include '../modelo/mdlError.php';
        $id_solicitud = $_GET["id_solicitud"];
        $npag = $_GET["npag"];
        $mdlError = new mdlError();
        $Errores = $mdlError->listarErrores($id_solicitud);
        //echo json_encode($Errores);exit;
        //echo json_encode($comprobantes);exit;
        $n = count($Errores);
        $Npaginas= intval(($n)/$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]+(($n%$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]==0)?0:1));
        $Errores = array_chunk($Errores, $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"]);
        $Errores = $Errores[$npag-1];
        $i = 1;
        $limit = $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"];
        $tabla = array();
        $detalles = array();
        $clases = array();
        $w=0;
        foreach ($Errores as $error) {
            $tabla[$w][] = ($limit*($npag-1))+$i;$i++;
            $tabla[$w][] = (strlen($error["fecha_error"])>0)?date_format(date_create($error["fecha_error"]), "d/m/Y H:i:s"):"-";
            $tabla[$w][] = empty($error["codigo_error"])?"-":$error["codigo_error"];
            $tabla[$w][] = empty($error["tipo_error"])?"-":$error["tipo_error"];
            $tabla[$w][] = empty($error["descripcion_error"])?"-":$error["descripcion_error"];
            
            $datos = array(
                array("EMPRESA",$empresa["user_empresa"]),
            );
            $detalles[] = str_replace("+","%20",urlencode(json_encode($datos)));
            $w++;
        }
        $parametros = $_GET;
        $href = "../controlador/contComprobante.php?";
        foreach ($parametros as $key => $value) {
            if($key!="npag" && $key!="_"){
                $href.= $key."=".$value."&";
            }
        }
        echo json_encode(array(
            "correcto"=>true,
            "datos"=>$tabla,
            "clases"=>$clases,
            "detalles"=>$detalles,
            "npag"=>$npag,
            "Npaginas"=>$Npaginas,
            "Npaginacion"=>$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"],
            "href"=>$href
        ));
        break;
    case "enviarFactura":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $idventa=$_GET["idventa"];
    
         $rst=$objMovimiento->obtenerDataSQL("select d.*,c.code as moneda,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,(select code from people where id=d.person_id) as codigovendedor
            from documents d 
            inner join people p on p.id=d.client_id
            inner join currencies c on c.id=d.currency_id
            where d.id=".$idventa);
        $detalle = $rst->fetchObject();
        $comentario = json_decode($detalle->detail);
        
        $declarar = "";
        $mdlSerie = new mdlSerie();
        //$id_serie = $_POST["id_serie"];
        $id_serie=1;
        $serie = $mdlSerie->verSerie($id_serie);
        if(empty($serie)){
            throw new Exception("NO HA SELECCIONADO EL NUMERO DE SERIE");
        }
        //$numerofactura = substr($detalle->numero,1,strlen($detalle->numero)); 01/01/2018
        $numerofactura = substr($detalle->number,1,12);
        $_POST["numfac"]=substr($detalle->number, 5,8);
        $serie["numero_serie"]=substr($detalle->number, 1,3);
        $_POST["totalDocumento"]=$detalle->total;
        $fechaemision = substr($detalle->date_time,0,10);
        $horaemision = substr($detalle->date_time,11,8);
        if($detalle->client_id>0){
            if($detalle->tipodocumento=="6"){
                $nombre = (trim($detalle->business_name));
            }else{
                $nombre = (trim($detalle->surname_father." ".$detalle->surname_mother." ".$detalle->name));
            }
            
            $direccion = (trim($detalle->address));
            $ruc = trim($detalle->ruc);
            $tipodoc = $detalle->tipodocumento;
            $codUbigeo = "0000";
            
        }else{
            $codUbigeo = "0000";
            $tipodoc = 1;
            $nombre = "VARIOS";
            $direccion = "";
            $ruc = "";
        }
        $moneda = $detalle->moneda;
        $descuentototal = "0";
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        $cuotas = array();
        //$formapago=$detalle->type;
        $formapago = $comentario->declaration;
        
        
        $detalles = array();
        $rs1=$objMovimiento->obtenerDataSQL("select dm.*,p.name as producto,p.igv as igvproducto,u.code as codigounidad,u.name as unidad,p.type,p.code as codigoproducto,p.presentation,b.name as marca 
            from document_details dm
            inner join products p on p.id=dm.product_id
            inner join units u on u.id=dm.unit_id
            left join brands b on b.id=p.brand_id
            where dm.document_id=".$idventa." order by dm.id asc");
        $total = 0;
        while($dat=$rs1->fetchObject()){
            //$descripcion = trim($dat->producto." ".$dat->presentation." ".$dat->marca);
            $detail = json_decode($dat->detail);
            $descripcion = $detail->producto;
            if($detail->declarar=="S"){
                $rs2 = $objMovimiento->obtenerDataSQL("select l.*,ps.serie,pm.product_serie_id 
                    from product_moves pm
                    left join lots l on l.id=pm.lot_id
                    left join product_series ps on ps.id=pm.product_serie_id
                    where pm.document_detail_id=".$dat->id);
                if($rs2->rowCount()>0){
                    if($dat->type=="R"){
                        $descripcion.=" LOTE: ";
                        while($dat2=$rs2->fetchObject()){
                            $descripcion.=$dat2->lot." | F.V.: ".$dat2->due_date."\n";
                        }
                    }elseif($dat->type=="M" || $dat->type=="E" || $dat->type=="C"){
                        $descripcion.=" \nSERIE: ";
                        while($dat2=$rs2->fetchObject()){
                            $descripcion.=$dat2->serie."\n";
                        }
                    }
                }
                $detalles[] = array(
                    "tipodetalle"=>"V",
                    "codigo"=>$dat->codigoproducto,
                    "unidadmedida"=>$dat->codigounidad,
                    "nombreunidadmedida"=>$dat->unidad,
                    "cantidad"=>$dat->quantity,
                    "descripcion"=>($descripcion),
                    "precioventaunitarioxitem"=>$dat->sell_price,
                    "descuentoxitem"=>"0",
                    "tipoigv"=>10,//$dat->igvproducto,
                    "tasaisc"=>"0",
                    "aplicacionisc"=>"",
                    "precioventasugeridoxitem"=>"",
                );
                $total += $dat->sell_price*$dat->quantity;
            }
        }
        $_POST["totalDocumento"] = $total;
        if($formapago == "D"){
            $rs1=$objMovimiento->obtenerDataSQL("select dp.*
            from document_payments dp
            where dp.document_id=".$idventa);
            while($dat=$rs1->fetchObject()){
                $cuotas[] = array(
                    "monto"=>$dat->total_amount,
                    "fecha"=>$dat->schedule_date,
                );    
            }
        }

        $detraccion = 0;
        $cuentadetraccion='';
        $codbiendetraccion='';
        if($comentario->detraccion!=""){
            $detraccion=$comentario->porcentajedetraccion;
            $codbiendetraccion=str_pad($comentario->detraccion,3,"0", STR_PAD_LEFT);
            $cuentadetraccion = $comentario->cuentabn;
        }

        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numerofactura"=>$numerofactura,
                "fechaemision"=>$fechaemision,
                "horaemision"=>$horaemision,
                "usuario"=>$nombre,
                "codubigeo"=>$codUbigeo,
                "tipodoc"=>$tipodoc,
                "ruc"=>$ruc,
                "moneda"=>$moneda,
                "descuentototal"=>$descuentototal,
                "percepcion"=>$percepcion,
                "aplicacionpercepcion"=>$aplicacionpercepcion,
                "documentosanexos"=>$documentosanexos,
                "detalles"=>$detalles,
                "formapago" =>$formapago,
                "cuotas" =>$cuotas,
                "vendedor"=>$detalle->codigovendedor,
                "orden"=>$comentario->orden,
                "detraccion" => $detraccion,
                "codbiendetraccion"=>$codbiendetraccion,
                "cuentadetraccion"=>$cuentadetraccion,
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendBill", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "F", $serie["numero_serie"], $_POST["numfac"], $ruc, $nombre, $_POST["totalDocumento"],$direccion);
            $mdlSerie->actualizarSerie2($id_serie);
            $token="";
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "token"=>$token,
                "seriefactura"=>$serie["numero_serie"],
                "doc"=>$ruc,
                "nombre"=>$nombre,
                "direccion"=>$direccion,
                "total"=>$_POST["totalDocumento"],
                "correlativofactura"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            //ini_set("soap.wsdl_cache_enabled", "0");
            $cliente2 = new nusoap_client(URL_ENV_FAC);
            //$cliente2->setEndpoint(URL_ENV_FAC.'Endpoint');
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("enviarFactura", array("ruc"=>$user_wsdl,"password" => $pass_wsdl,"json" => $datos_enviar));
            //throw new Exception($result);
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception($result);
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }

            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud_local);

            $tipo_documento = $objSolicitud["tipo_documento"];
            $nombre_documento = $objSolicitud["nombre_solicitud"];
            if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
                Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE LA FACTURA: ".$numerofactura
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL REGISTRAR LA FACTURA: ".$numerofactura,
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarBoleta":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $idventa=$_GET["idventa"];
    
        $rst=$objMovimiento->obtenerDataSQL("select d.*,c.code as moneda,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,(select code from people where id=d.person_id) as codigovendedor
            from documents d 
            inner join people p on p.id=d.client_id
            inner join currencies c on c.id=d.currency_id
            where d.id=".$idventa);
        $detalle = $rst->fetchObject();
        
        $mdlSerie = new mdlSerie();
        
        $declarar = "";
        //$id_serie = $_POST["id_serie"];
        $id_serie = 2;
        $serie = $mdlSerie->verSerie($id_serie);
        if(empty($serie)){
            throw new Exception("NO HA SELECCIONADO EL NUMERO DE SERIE");
        }
        //$numeroboleta = substr($detalle->numero,1,strlen($detalle->numero));
        $numeroboleta = substr($detalle->number,1,12);
        $serie["numero_serie"]=substr($detalle->number, 1,3);
        $_POST["numfac"]=substr($detalle->number, 5,8);
        $_POST["totalDocumento"]=$detalle->total;
        $fechaemision = substr($detalle->date_time,0,10);
        $horaemision = substr($detalle->date_time,11,8);
        if($detalle->client_id>0){
            if($detalle->tipodocumento=="6"){
                $nombre = (trim($detalle->business_name));
                $dni = trim($detalle->ruc);
            }else{
                $nombre = (trim($detalle->surname_father." ".$detalle->surname_mother." ".$detalle->name));
                $dni = trim($detalle->dni);
            }
            $direccion = (trim($detalle->address));
            $tipodoc = $detalle->tipodocumento;
            $codUbigeo = "0000";
            
        }else{
            $codUbigeo = "0000";
            $tipodoc = 1;
            $nombre = "VARIOS";
            $direccion = "";
            $dni = "";
        }
        $moneda = $detalle->moneda;
        $descuentototal = "0";
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        
        $detalles = array();
        $rs1=$objMovimiento->obtenerDataSQL("select dm.*,p.name as producto,p.igv as igvproducto,u.code as codigounidad,u.name as unidad,p.type,p.code as codigoproducto,p.presentation,b.name as marca 
            from document_details dm
            inner join products p on p.id=dm.product_id
            inner join units u on u.id=dm.unit_id
            left join brands b on b.id=p.brand_id
            where dm.document_id=".$idventa);
        while($dat=$rs1->fetchObject()){
            //$descripcion = trim($dat->producto." ".$dat->presentation." ".$dat->marca);
            $detail = json_decode($dat->detail);
            $descripcion = $detail->producto;
            if($detail->declarar=="S"){
                $rs2 = $objMovimiento->obtenerDataSQL("select l.*,ps.serie,pm.product_serie_id 
                    from product_moves pm
                    left join lots l on l.id=pm.lot_id
                    left join product_series ps on ps.id=pm.product_serie_id
                    where pm.document_detail_id=".$dat->id);
                if($rs2->rowCount()>0){
                    if($dat->type=="R"){
                        $descripcion.=" LOTE: ";
                        while($dat2=$rs2->fetchObject()){
                            $descripcion.=$dat2->lot." | F.V.: ".$dat2->due_date."\n";
                        }
                    }elseif($dat->type=="M" || $dat->type=="E" || $dat->type=="C"){
                        $descripcion.=" \nSERIE: ";
                        while($dat2=$rs2->fetchObject()){
                            $descripcion.=$dat2->serie."\n";
                        }
                    }
                }

                $detalles[] = array(
                    "tipodetalle"=>"V",
                    "codigo"=>$dat->codigoproducto,
                    "unidadmedida"=>$dat->codigounidad,
                    "nombreunidadmedida"=>$dat->unidad,
                    "cantidad"=>$dat->quantity,
                    "descripcion"=>(trim($descripcion)),
                    "precioventaunitarioxitem"=>$dat->sell_price,
                    "descuentoxitem"=>"0",
                    "tipoigv"=>10,//$dat->igvproducto,
                    "tasaisc"=>"0",
                    "aplicacionisc"=>"",
                    "precioventasugeridoxitem"=>"",
                );
                $total += $dat->sell_price*$dat->quantity;
            }
        }
        $_POST["totalDocumento"] = $total;
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numeroboleta"=>$numeroboleta,
                "fechaemision"=>$fechaemision,
                "horaemision"=>$horaemision,
                "usuario"=>$nombre,
                "codubigeo"=>$codUbigeo,
                "tipodoc"=>$tipodoc,
                "dni"=>$dni,
                "moneda"=>$moneda,
                "descuentototal"=>$descuentototal,
                "percepcion"=>$percepcion,
                "aplicacionpercepcion"=>$aplicacionpercepcion,
                "documentosanexos"=>$documentosanexos,
                "detalles"=>$detalles,
                "vendedor"=>$detalle->codigovendedor,
                "orden"=>$comentario->orden,
            );
            //print_r($factura);
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendBill", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "B", $serie["numero_serie"], $_POST["numfac"], $dni, $nombre, $_POST["totalDocumento"],$direccion);
            $mdlSerie->actualizarSerie2($id_serie);
            $token = "";
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                
            $datos_enviar = array(
                "token"=>$token,
                "serieboleta"=>$serie["numero_serie"],
                "correlativoboleta"=>$_POST["numfac"],
                "doc"=>$dni,
                "nombre"=>$nombre,
                "direccion"=>$direccion,
                "total"=>$POST["totalDocumento"],
                "correlativoboleta"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);

            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client(URL_ENV_BOL);
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $result = $cliente2->call("enviarBoleta", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception(json_encode($result));
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }

            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud_local);

            $tipo_documento = $objSolicitud["tipo_documento"];
            $nombre_documento = $objSolicitud["nombre_solicitud"];
            if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
                Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE LA BOLETA: ".$numeroboleta
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL REGISTRAR LA BOLETA: ".$numeroboleta,
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarNotaCredito":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $idventa=$_GET["idventa"];
    
        $rst=$objMovimiento->obtenerDataSQL("select d.*,c.code as moneda,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,(select code from people where id=d.person_id) as codigovendedor,d2.number as numeroreferencia,re.code as codigomotivo,re.name as motivo
            from documents d 
            inner join documents d2 on d2.id=d.document_id
            inner join people p on p.id=d.client_id
            inner join currencies c on c.id=d.currency_id
            inner join reasons re on re.id=d.reason_id
            where d.id=".$idventa);
        $detalle = $rst->fetchObject();

        $mdlSerie = new mdlSerie();
        $id_serie = 4;
        $serie = $mdlSerie->verSerie($id_serie);
        if(empty($serie)){
            throw new Exception("NO HA SELECCIONADO EL NUMERO DE SERIE");
        }

        $numeronotacredito = substr($detalle->number, 1, 12);
        $serie["numero_serie"]=substr($detalle->number, 1,3);
        $_POST["numfac"]=substr($detalle->number, 5,8);
        $_POST["totalDocumento"]=$detalle->total;
        $fechaemision = substr($detalle->date_time,0,10);
        $horaemision = substr($detalle->date_time,11,8);
        if($detalle->client_id>0){
            if($detalle->tipodocumento=="6"){
                $nombre = (trim($detalle->business_name));
                $doc = trim($detalle->ruc);
            }else{
                $nombre = (trim($detalle->surname_father." ".$detalle->surname_mother." ".$detalle->name));
                $doc = trim($detalle->dni);
            }
            $direccion = (trim($detalle->address));
            $tipodoc = $detalle->tipodocumento;
            $codUbigeo = "0000";
            
        }else{
            $codUbigeo = "0000";
            $tipodoc = 1;
            $nombre = "VARIOS";
            $direccion = "";
            $doc = "";
        }


        $moneda = $detalle->moneda;
        $numreferencia = $detalle->numeroreferencia;
        $tipomotivo = $detalle->codigomotivo;
        $motivo = $detalle->motivo;
        $descuentototal = 0;
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        $detalles = array();
        $rs1=$objMovimiento->obtenerDataSQL("select dm.*,p.name as producto,p.igv as igvproducto,u.code as codigounidad,u.name as unidad,p.type,p.code as codigoproducto,p.presentation,b.name as marca 
            from document_details dm
            inner join products p on p.id=dm.product_id
            inner join units u on u.id=dm.unit_id
            left join brands b on b.id=p.brand_id
            where dm.document_id=".$idventa);
        while($dat=$rs1->fetchObject()){
            //$descripcion = trim($dat->producto." ".$dat->presentation." ".$dat->marca);
            $detail = json_decode($dat->detail);
            $descripcion = $detail->producto;
            $rs2 = $objMovimiento->obtenerDataSQL("select l.*,ps.serie,pm.product_serie_id 
                from product_moves pm
                left join lots l on l.id=pm.lot_id
                left join product_series ps on ps.id=pm.product_serie_id
                where pm.document_detail_id=".$dat->id);
            if($rs2->rowCount()>0){
                if($dat->type=="R"){
                    $descripcion.=" LOTE: ";
                    while($dat2=$rs2->fetchObject()){
                        $descripcion.=$dat2->lot." | F.V.: ".$dat2->due_date."\n";
                    }
                }elseif($dat->type=="M" || $dat->type=="E" || $dat->type=="C"){
                    $descripcion.=" \nSERIE: ";
                    while($dat2=$rs2->fetchObject()){
                        $descripcion.=$dat2->serie."\n";
                    }
                }
            }
            $detalles[] = array(
                "tipodetalle"=>"V",
                "codigo"=>$dat->codigoproducto,
                "unidadmedida"=>$dat->codigounidad,
                "nombreunidadmedida"=>$dat->unidad,
                "cantidad"=>$dat->quantity,
                "descripcion"=>trim($descripcion),
                "precioventaunitarioxitem"=>($detalle->codigomotivo=="03"?0:$dat->sell_price),
                "descuentoxitem"=>"0",
                "tipoigv"=>10,//$dat->igvproducto,
                "tasaisc"=>"0",
                "aplicacionisc"=>"",
                "precioventasugeridoxitem"=>"",
            );
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numeronotacredito"=>$numeronotacredito,
                "fechaemision"=>$fechaemision,
                "tipo"=>$tipomotivo,
                "motivo"=>$motivo,
                "numeroreferencia"=>$numreferencia,
                "usuario"=>$nombre,
                "doc"=>$doc,
                "moneda"=>$moneda,
                "descuentototal"=>$descuentototal,
                "percepcion"=>$percepcion,
                "aplicacionpercepcion"=>$aplicacionpercepcion,
                "documentosanexos"=>$documentosanexos,
                "detalles"=>$detalles,
                "formapago"=>"C"
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendBill", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "C", $serie["numero_serie"], $_POST["numfac"], $doc, $nombre, $_POST["totalDocumento"],$direccion);
            $mdlSerie->actualizarSerie2($id_serie);
            /*$token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }*/
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "token"=>$token,
                "serienota"=>$serie["numero_serie"],
                "correlativonota"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client(URL_ENV_NTC);
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/notacredito2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $result = $cliente2->call("enviarNotaCredito", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception(json_encode($result));
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarNotaDebito":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        $mdlSerie = new mdlSerie();
        $id_serie = $_POST["id_serie"];
        $serie = $mdlSerie->verSerie($id_serie);
        if(empty($serie)){
            throw new Exception("NO HA SELECCIONADO EL NUMERO DE SERIE");
        }
        $numeronotadebito = $serie["numero_serie"]."-".$_POST["numfac"];
        $fechaemision = date("Y-m-d");
        $nombre = $_POST["nombre"];
        $doc = $_POST["doc"];
        $moneda = $_POST["moneda"];
        $numreferencia = $_POST["numreferencia"];
        $tipomotivo = $_POST["tipomotivo"];
        $motivo = $_POST["motivo"];
        $descuentototal = $_POST["descuentoglobal"];
        $percepcion = "";
        $aplicacionpercepcion = "";
        $documentosanexos = array();
        $tipodetalle = $_POST["tipodetalle"];
        if(empty($tipodetalle)){
            $tipodetalle = array();
        }
        $unidades = $_POST["unidades"];
        $tipoigv = $_POST["tipoigv"];
        $descuentoxitem = $_POST["descuentoxitem"];
        $codigo = $_POST["codigo"];
        $cantidad = $_POST["cantidad"];
        $descripcion = $_POST["descripcion"];
        $precio = $_POST["precio"];
        $detalles = array();
        foreach ($tipodetalle as $key => $value) {
            $detalles[] = array(
                "tipodetalle"=>$tipodetalle[$key],
                "codigo"=>$codigo[$key],
                "unidadmedida"=>$unidades[$key],
                "cantidad"=>$cantidad[$key],
                "descripcion"=>$descripcion[$key],
                "precioventaunitarioxitem"=>$precio[$key],
                "descuentoxitem"=>$descuentoxitem[$key],
                "tipoigv"=>$tipoigv[$key],
                "tasaisc"=>"0",
                "aplicacionisc"=>"",
                "precioventasugeridoxitem"=>"",
            );
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numeronotadebito"=>$numeronotadebito,
                "fechaemision"=>$fechaemision,
                "tipo"=>$tipomotivo,
                "motivo"=>$motivo,
                "numeroreferencia"=>$numreferencia,
                "usuario"=>$nombre,
                "doc"=>$doc,
                "moneda"=>$moneda,
                "descuentototal"=>$descuentototal,
                "percepcion"=>$percepcion,
                "aplicacionpercepcion"=>$aplicacionpercepcion,
                "documentosanexos"=>$documentosanexos,
                "detalles"=>$detalles
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendBill", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "B", $serie["numero_serie"], $_POST["numfac"]);
            $mdlSerie->actualizarSerie2($id_serie);
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            $datos_enviar = array(
                "token"=>$token,
                "serienota"=>$serie["numero_serie"],
                "correlativonota"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/notadebito2_1.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/notadebito2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $result = $cliente2->call("enviarNotaDebito", array("json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception(json_encode($result));
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                        //http://localhost/facturacion/bilservice/billService.xml
                        $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                                        [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                                        'trace' => TRUE , 
                                        'soap_version' => SOAP_1_1,
                                        'soap_defencoding' => 'UTF-8' ] );
                        $usuario_sunat = "";
                        $password_sunat = "";
                        $empresa = $_SESSION["empresa"];
                        if($empresa["modo_autenticacion"]=="E"){
                            $usuario_sunat = USERNAME_SUNAT;
                            $password_sunat = PASSWORD_SUNAT;
                        }elseif($empresa["modo_autenticacion"]=="P"){
                            $usuario_sunat = $empresa["username_sunat"];
                            $password_sunat = $empresa["password_sunat"];
                        }
                        //throw new Exception("USUARIOS: ". json_encode(array($usuario_sunat,$password_sunat)));
                        $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                            <wsse:UsernameToken>
                                <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                                <wsse:Password>' . $password_sunat . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>';
                        $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
                        $file_ZIP = file_get_contents($filename_zip);
                        $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                        $result = $cliente_SUNAT->__soapCall('sendBill', $argumentos, null, $headers);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                        if ($cliente_SUNAT->fault) {
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                'id_solicitud'=>$id_solicitud,
                            );
                            $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                            throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                        } else {
                            if(is_soap_fault($cliente_SUNAT)){
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                            } else {
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                                $fileR_ZIP = $result->applicationResponse;
                                $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                                file_put_contents($filenameR_zip, $fileR_ZIP);
                                $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                                $fileR_ZIP = file_get_contents($filenameR_zip);
                                $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);
                                
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                                    "nombre_documento"=>$nombre_documento,
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $datos_enviar = json_encode($datos_enviar);
                                
                                $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud, "", "T");
                                if ($cliente2->fault) {
                                    throw new Exception($result);
                                } else {
                                    $error = $cliente2->getError();
                                    if ($error) {
                                        throw new Exception($error);
                                    } else {
                                        $result = json_decode($result);
                                        if($result->code=="0"){
                                            $_SESSION["token"] = $result->mensaje;
                                        }else{
                                            throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarResumenBoletas":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        $mdlSerie = new mdlSerie();
        $id_serie = $_POST["id_serie"];
        $serie = $mdlSerie->verSerie($id_serie);
        if(empty($serie)){
            $nombreSerie = $_POST["serie"];
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "E",$nombreSerie);
            if(empty($serie)){
                $mdlSerie->insertarSerie($nombreSerie, intval($_POST["numfac"])-1,$_SESSION["empresa"]["id_empresa"],"E");
            }
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "E",$nombreSerie);
            $id_serie = $serie["id_serie"];
        }
        $numeroboleta = $serie["numero_serie"]."-".$_POST["numfac"];
        $fechaemision = date("Y-m-d");
        $tipoResumen = $_POST["tipoResumen"];
        $fechareferencia = $_POST["fecref"];
        $moneda = $_POST["moneda"];
        $tipodetalle = $_POST["tipodoc"];
        $idDetalle = $_POST["idDetalle"];
        $idDetalleServidor = $_POST["idDetalleServidor"];
        $serieDetalle = $_POST["serieDetalle"];
        $correlativo = $_POST["correlativo"];
        $dni = $_POST["dni"];
        $total = $_POST["total"];
        $detalles = array();
        foreach ($tipodetalle as $key => $value) {
            $detalles[] = array(
                "id"=>$idDetalle[$key],
                "idservidor"=>$idDetalleServidor[$key],
                "tipo"=>$tipodetalle[$key],
                "numero"=>$serieDetalle[$key],
                "correlativo"=>$correlativo[$key],
                "dni"=>$dni[$key],
                "total"=>$total[$key],
            );
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numeroboleta"=>$numeroboleta,
                "tiporesumen"=>$tipoResumen,
                "fechaemision"=>$fechaemision,
                "fechareferencia"=>$fechareferencia,
                "moneda"=>$moneda,
                "detalles"=>$detalles
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendSummary", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "E", $serie["numero_serie"], $_POST["numfac"]);
            $mdlSerie->actualizarSerie2($id_serie);
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            $datos_enviar = array(
                "token"=>$token,
                "serieboleta"=>$serie["numero_serie"],
                "correlativoboleta"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/resumenboletas2.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/resumenboletas2.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $result = $cliente2->call("enviarResumenBoletas", array("json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception(json_encode($result));
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                        //http://localhost/facturacion/bilservice/billService.xml
                        set_time_limit(120);
                        $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                                        [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                                        'trace' => TRUE , 
                                        'soap_version' => SOAP_1_1,
                                        'soap_defencoding' => 'UTF-8' ] );
                        $usuario_sunat = "";
                        $password_sunat = "";
                        $empresa = $_SESSION["empresa"];
                        if($empresa["modo_autenticacion"]=="E"){
                            $usuario_sunat = USERNAME_SUNAT;
                            $password_sunat = PASSWORD_SUNAT;
                        }elseif($empresa["modo_autenticacion"]=="P"){
                            $usuario_sunat = $empresa["username_sunat"];
                            $password_sunat = $empresa["password_sunat"];
                        }
                        $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                            <wsse:UsernameToken>
                                <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                                <wsse:Password>' . $password_sunat . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>';
                        $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
                        $file_ZIP = file_get_contents($filename_zip);
                        $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                        $result = $cliente_SUNAT->__soapCall('sendSummary', $argumentos, null, $headers);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                        if ($cliente_SUNAT->fault) {
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                'id_solicitud'=>$id_solicitud,
                            );
                            $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                            throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                        } else {
                            if(is_soap_fault($cliente_SUNAT)){
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                            } else {
                                $ticketRespuesta = $result->ticket;
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C", $ticketRespuesta);
                                
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    "ticketRespuesta"=>$ticketRespuesta,
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $datos_enviar = json_encode($datos_enviar);
                                
                                $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                                if ($cliente2->fault) {
                                    throw new Exception($result);
                                } else {
                                    $error = $cliente2->getError();
                                    if ($error) {
                                        throw new Exception($error);
                                    } else {
                                        $result = json_decode($result);
                                        if($result->code=="0"){
                                            $_SESSION["token"] = $result->mensaje;
                                        }else{
                                            throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        throw new Exception(json_encode($result));
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstResumenBoletas.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarResumenFacturas":
        include_once '../modelo/mdlSolicitud.php';
        $idDetalles = $_POST["idDetalle"];
        try{
            if(count($idDetalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            
            $mdlSolicitud = new mdlSolicitud();
            
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/factura2_1.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            
            $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, [ 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' => TRUE , 'soap_version' => SOAP_1_1,'soap_defencoding' => 'UTF-8' ] );
            $usuario_sunat = "";
            $password_sunat = "";
            $empresa = $_SESSION["empresa"];
            if($empresa["modo_autenticacion"]=="E"){
                $usuario_sunat = USERNAME_SUNAT;
                $password_sunat = PASSWORD_SUNAT;
            }elseif($empresa["modo_autenticacion"]=="P"){
                $usuario_sunat = $empresa["username_sunat"];
                $password_sunat = $empresa["password_sunat"];
            }
            //throw new Exception("USUARIOS: ". json_encode(array($usuario_sunat,$password_sunat)));
            $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <wsse:UsernameToken>
                    <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                    <wsse:Password>' . $password_sunat . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>';
            $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
            
            $respuesta = "";
            foreach ($idDetalles as $key => $id_solicitud_local) {
                set_time_limit(120);
                $solicitud = $mdlSolicitud->verSolicitud($id_solicitud_local);
                $id_solicitud = $solicitud["id_solicitud_servidor"];
                $nombre_documento = $solicitud["nombre_solicitud"];
                $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                $file_ZIP = file_get_contents($filename_zip);
                $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", date("Y-m-d\TH:i:s"));
                $result = $cliente_SUNAT->__soapCall('sendBill', $argumentos, null, $headers);
                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                error_log("\nDECLARACION DE FACTURA: IDSOLICITUD LOCAL ".$id_solicitud_local."\n");
                error_log(print_r($result,true));
                if ($cliente_SUNAT->fault) {
                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        'id_solicitud'=>$id_solicitud,
                    );
                    $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                    //throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                    $respuesta .= "ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa))."\n";
                } else {
                    if(is_soap_fault($cliente_SUNAT)){
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", "", date("Y-m-d\TH:i:s"));
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                        $datos_enviar = array(
                            "token"=>$_SESSION["token"],
                            'id_solicitud'=>$id_solicitud,
                        );
                        $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                        //throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                        $respuesta .= "ERROR 2: ". json_encode($result->faultstring)."\n";
                    } else {
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", "", date("Y-m-d\TH:i:s"));
                        $fileR_ZIP = $result->applicationResponse;
                        $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                        file_put_contents($filenameR_zip, $fileR_ZIP);
                        $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                        
                        $respuesta = Algoritmos::ComprobarRespuestaXML(__DIR__ ."/../ficheros/R-".$nombre_documento."xml");
                        //echo json_encode($respuesta);exit();
                        if($respuesta["codigo"]=="0"){
                            $fileR_ZIP = file_get_contents($filenameR_zip);
                            $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);
                            
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                                "nombre_documento"=>$nombre_documento,
                                'id_solicitud'=>$id_solicitud,
                            );
                            $datos_enviar = json_encode($datos_enviar);

                            $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                            if ($cliente2->fault) {
                                //throw new Exception($result);
                                $respuesta .= $result."\n";
                            } else {
                                $error = $cliente2->getError();
                                if ($error) {
                                    //throw new Exception($error);
                                    $respuesta .= $error."\n";
                                } else {
                                    $result = json_decode($result);
                                    if($result->code=="0"){
                                        $_SESSION["token"] = $result->mensaje;
                                    }else{
                                        //throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                        $respuesta .= $result->mensaje." EN LA LINEA: ".$result->line."\n";
                                    }
                                }
                            }
                        }else{
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I","","",$respuesta["codigo"]);
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                'id_solicitud'=>$id_solicitud,
                                "errorcode"=>$respuesta["codigo"]
                            );
                            $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                            //throw new Exception("ERROR 3: ". $respuesta["detalle"]);
                            $respuesta .= "ERROR 3: ". $respuesta["detalle"]."\n";
                        }
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            error_log($e->getMessage());
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA CON SUNAT, VUELVE A INTENTARLO MAS TARDE",
                "line"=>$e->getLine(),
                "file"=>$e->getFile(),
                "ejecutar"=>'cargarTablaResumenes();'
            ));
        }
        break;
    case "enviarResumenBoletas2":
        include_once '../modelo/mdlSolicitud.php';
        $idDetalles = $_POST["idDetalle"];
        try{
            if(count($idDetalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            
            $mdlSolicitud = new mdlSolicitud();
            
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/factura2_1.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            
            $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, [ 'cache_wsdl' => WSDL_CACHE_NONE, 'trace' => TRUE , 'soap_version' => SOAP_1_1,'soap_defencoding' => 'UTF-8' ] );
            $usuario_sunat = "";
            $password_sunat = "";
            $empresa = $_SESSION["empresa"];
            if($empresa["modo_autenticacion"]=="E"){
                $usuario_sunat = USERNAME_SUNAT;
                $password_sunat = PASSWORD_SUNAT;
            }elseif($empresa["modo_autenticacion"]=="P"){
                $usuario_sunat = $empresa["username_sunat"];
                $password_sunat = $empresa["password_sunat"];
            }
            //throw new Exception("USUARIOS: ". json_encode(array($usuario_sunat,$password_sunat)));
            $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <wsse:UsernameToken>
                    <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                    <wsse:Password>' . $password_sunat . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>';
            $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
            
            $respuesta = "";
            foreach ($idDetalles as $key => $id_solicitud_local) {
                set_time_limit(120);
                $solicitud = $mdlSolicitud->verSolicitud($id_solicitud_local);
                $id_solicitud = $solicitud["id_solicitud_servidor"];
                $nombre_documento = $solicitud["nombre_solicitud"];
                $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                $file_ZIP = file_get_contents($filename_zip);
                $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", date("Y-m-d\TH:i:s"));
                $result = $cliente_SUNAT->__soapCall('sendBill', $argumentos, null, $headers);
                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                error_log("\nDECLARACION DE BOLETAS: IDSOLICITUD LOCAL ".$id_solicitud_local."\n");
                error_log(print_r($result,true));
                if ($cliente_SUNAT->fault) {
                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        'id_solicitud'=>$id_solicitud,
                    );
                    $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                    //throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                    $respuesta .= "ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa))."\n";
                } else {
                    if(is_soap_fault($cliente_SUNAT)){
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", "", date("Y-m-d\TH:i:s"));
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                        $datos_enviar = array(
                            "token"=>$_SESSION["token"],
                            'id_solicitud'=>$id_solicitud,
                        );
                        $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                        //throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                        $respuesta .= "ERROR 2: ". json_encode($result->faultstring)."\n";
                    } else {
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", "", date("Y-m-d\TH:i:s"));
                        $fileR_ZIP = $result->applicationResponse;
                        $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                        file_put_contents($filenameR_zip, $fileR_ZIP);
                        $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                        
                        $respuesta = Algoritmos::ComprobarRespuestaXML(__DIR__ ."/../ficheros/R-".$nombre_documento."xml");
                        //echo json_encode($respuesta);exit();
                        if($respuesta["codigo"]=="0"){
                            $fileR_ZIP = file_get_contents($filenameR_zip);
                            $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);
                            
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                                "nombre_documento"=>$nombre_documento,
                                'id_solicitud'=>$id_solicitud,
                            );
                            $datos_enviar = json_encode($datos_enviar);

                            $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                            if ($cliente2->fault) {
                                //throw new Exception($result);
                                $respuesta .= $result."\n";
                            } else {
                                $error = $cliente2->getError();
                                if ($error) {
                                    //throw new Exception($error);
                                    $respuesta .= $error."\n";
                                } else {
                                    $result = json_decode($result);
                                    if($result->code=="0"){
                                        $_SESSION["token"] = $result->mensaje;
                                    }else{
                                        //throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                        $respuesta .= $result->mensaje." EN LA LINEA: ".$result->line."\n";
                                    }
                                }
                            }
                        }else{
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I","","",$respuesta["codigo"]);
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                'id_solicitud'=>$id_solicitud,
                                "errorcode"=>$respuesta["codigo"]
                            );
                            $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                            //throw new Exception("ERROR 3: ". $respuesta["detalle"]);
                            $respuesta .= "ERROR 3: ". $respuesta["detalle"]."\n";
                        }
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            error_log($e->getMessage());
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA CON SUNAT, VUELVE A INTENTARLO MAS TARDE",
                "line"=>$e->getLine(),
                "file"=>$e->getFile(),
                "ejecutar"=>'cargarTablaResumenes();'
            ));
        }
        break;
    case "enviarResumenBoletasAnuladas":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        include_once '../modelo/mdlError.php';
        $mdlError = new mdlError();$err = false;
        $mdlSerie = new mdlSerie();
        $id_serie = $_POST["id_serie"];
        if(trim($id_serie)!=""){
            $serie = $mdlSerie->verSerie($id_serie);
        }else{
        //if(empty($serie)){
            $nombreSerie = $_POST["serie"];
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "E",$nombreSerie);
            if(empty($serie)){
                $mdlSerie->insertarSerie($nombreSerie, intval($_POST["numfac"])-1,$_SESSION["empresa"]["id_empresa"],"E");
            }
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "E",$nombreSerie);
            $id_serie = $serie["id_serie"];
        }
        $numeroboleta = $serie["numero_serie"]."-".$_POST["numfac"];
        $fechaemision = date("Y-m-d");
        $tipoResumen = $_POST["tipoResumen"];
        $fechareferencia = $_POST["fecref"];
        $moneda = $_POST["moneda"];
        $tipodetalle = $_POST["tipodoc"];
        $idDetalle = $_POST["idDetalle"];
        $idDetalleServidor = $_POST["idDetalleServidor"];
        $serieDetalle = $_POST["serieDetalle"];
        $correlativo = $_POST["correlativo"];
        $dni = $_POST["dni"];
        $total = $_POST["total"];
        $numeroReferencia = $_POST["numeroReferencia"];
        $tipoReferencia = $_POST["tipoReferencia"];
        $detalles = array();
        foreach ($tipodetalle as $key => $value) {
            $detalles[] = array(
                "id"=>$idDetalle[$key],
                "idservidor"=>$idDetalleServidor[$key],
                "tipo"=>$tipodetalle[$key],
                "numero"=>$serieDetalle[$key],
                "correlativo"=>$correlativo[$key],
                "dni"=>$dni[$key],
                "total"=>$total[$key],
                "numeroReferencia"=>$numeroReferencia[$key],
                "tipoReferencia"=>$tipoReferencia[$key],
            );
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numeroboleta"=>$numeroboleta,
                "tiporesumen"=>$tipoResumen,
                "fechaemision"=>$fechaemision,
                "fechareferencia"=>$fechareferencia,
                "moneda"=>$moneda,
                "detalles"=>$detalles
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendSummary", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "E", $serie["numero_serie"], $_POST["numfac"]);
            $mdlSerie->actualizarSerie2($id_serie);
            $token = "";
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "token"=>$token,
                "serieboleta"=>$serie["numero_serie"],
                "correlativoboleta"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            $cliente2 = new nusoap_client(URL_ENV_RBB);
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/resumenboletas2.php");
            $error = $cliente2->getError();
            if ($error) {
                $mdlError->insertarError($id_solicitud_local,date("d-m-Y H:i:s"),'-',json_encode($error),'SERVIDOR');$err = true;
                throw new Exception("OCURRIO UN PROBLEMA CON EL SERVIDOR, INTENTELO DE NUEVO");
            }
            $result = $cliente2->call("enviarResumenBoletas", array("ruc"=>$user_wsdl,"password"=>$pass_wsdl,"json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                $mdlError->insertarError($id_solicitud_local,date("d-m-Y H:i:s"),'-',$result,'SERVIDOR');$err = true;
                throw new Exception("OCURRIO UN PROBLEMA CON EL SERVIDOR, INTENTELO DE NUEVO");
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    $mdlError->insertarError($id_solicitud_local,date("d-m-Y H:i:s"),'-',json_encode($error),'SERVIDOR');$err = true;
                    throw new Exception("OCURRIO UN PROBLEMA CON EL SERVIDOR, INTENTELO DE NUEVO");
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        
                        
                    }else{
                        $mdlError->insertarError($id_solicitud_local,date("d-m-Y H:i:s"),$result->code,$result->mensaje,'SERVIDOR');$err = true;
                        throw new Exception(json_encode($result->mensaje." EN LA LINEA: ".$result->line));
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            if(($id_solicitud_local != null || $id_solicitud_local != "")  && $err == false ){
                $mdlError->insertarError($id_solicitud_local,date("d-m-Y H:i:s"),$e->getCode(),$e->getMessage(),'-');
            }
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarComunicacionBajas":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        $mdlSerie = new mdlSerie();
        $id_serie = $_POST["id_serie"];
        if($id_serie!=""){
            $serie = $mdlSerie->verSerie($id_serie);
        }else{
            $serie = "";
        }
        if(empty($serie)){
            $nombreSerie = $_POST["serie"];
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "A",$nombreSerie);
            if(empty($serie)){
                $mdlSerie->insertarSerie($nombreSerie, intval($_POST["numfac"])-1,$_SESSION["empresa"]["id_empresa"],"A");
            }
            $serie = $mdlSerie->verSeries3($_SESSION["empresa"]["id_empresa"], "A",$nombreSerie);
            $id_serie = $serie["id_serie"];
        }
        $numerobaja = $serie["numero_serie"]."-".$_POST["numfac"];
        $fechaemision = date("Y-m-d");
        $fechareferencia = $_POST["fecref"];
        $tipodetalle = $_POST["tipodocumento"];
        $serieDetalle = $_POST["serieDetalle"];
        $correlativo = $_POST["correlativo"];
        $motivo = $_POST["motivo"];
        $idDetalles = $_POST["idDetalle"];
        $idDetalleServidor = $_POST["idDetalleServidor"];
        $detalles = array();
        foreach ($idDetalles as $key => $value) {
            if($value>0){
                $detalles[] = array(
                    "id"=>$idDetalles[$key],
                    "idservidor"=>$idDetalleServidor[$key],
                    "tipo"=>$tipodetalle[$key],
                    "numero"=>$serieDetalle[$key],
                    "correlativo"=>$correlativo[$key],
                    "motivo"=>$motivo[$key],
                );
            }
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $factura = array(
                "numerobaja"=>$numerobaja,
                "fechaemision"=>$fechaemision,
                "fechareferencia"=>$fechareferencia,
                "detalles"=>$detalles
            );
            $mdlSolicitud = new mdlSolicitud();
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendSummary", json_encode($factura), $_SESSION["empresa"]["ruc_empresa"], "", "A", $serie["numero_serie"], $_POST["numfac"]);
            $mdlSerie->actualizarSerie2($id_serie);
            /*$token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }*/
            $token = "";
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                
            $datos_enviar = array(
                "token"=>$token,
                "seriebaja"=>$serie["numero_serie"],
                "correlativobaja"=>$_POST["numfac"],
                "comprobante"=> json_encode($factura)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client(URL_ENV_CBB);
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/comunicacionbajas2.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $result = $cliente2->call("enviarComunicacionBajas", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception(json_encode($result));
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        /*$_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                        //http://localhost/facturacion/bilservice/billService.xml
                        $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                                        [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                                        'trace' => TRUE , 
                                        'soap_version' => SOAP_1_1,
                                        'soap_defencoding' => 'UTF-8' ] );
                        $usuario_sunat = "";
                        $password_sunat = "";
                        $empresa = $_SESSION["empresa"];
                        if($empresa["modo_autenticacion"]=="E"){
                            $usuario_sunat = USERNAME_SUNAT;
                            $password_sunat = PASSWORD_SUNAT;
                        }elseif($empresa["modo_autenticacion"]=="P"){
                            $usuario_sunat = $empresa["username_sunat"];
                            $password_sunat = $empresa["password_sunat"];
                        }
                        //throw new Exception("FIN DEL FICHERO");
                        $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                            <wsse:UsernameToken>
                                <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                                <wsse:Password>' . $password_sunat . '</wsse:Password>
                            </wsse:UsernameToken>
                        </wsse:Security>';
                        $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
                        $file_ZIP = file_get_contents($filename_zip);
                        $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                        $result = $cliente_SUNAT->__soapCall('sendSummary', $argumentos, null, $headers);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                        if ($cliente_SUNAT->fault) {
                            $datos_enviar = array(
                                "token"=>$_SESSION["token"],
                                'id_solicitud'=>$id_solicitud,
                            );
                            $result2 = $cliente2->call("recibirError", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                            throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                        } else {
                            if(is_soap_fault($cliente_SUNAT)){
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $result2 = $cliente2->call("recibirError", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                                throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                            } else {
                                $ticketRespuesta = $result->ticket;
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C", $ticketRespuesta);
                                
                                $datos_enviar = array(
                                    "token"=>$_SESSION["token"],
                                    "ticketRespuesta"=>$ticketRespuesta,
                                    'id_solicitud'=>$id_solicitud,
                                );
                                $datos_enviar = json_encode($datos_enviar);
                                
                                $result = $cliente2->call("recibirRespuesta", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                                if ($cliente2->fault) {
                                    throw new Exception($result);
                                } else {
                                    $error = $cliente2->getError();
                                    if ($error) {
                                        throw new Exception($error);
                                    } else {
                                        $result = json_decode($result);
                                        if($result->code=="0"){
                                            $_SESSION["token"] = $result->mensaje;
                                        }else{
                                            throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                        }
                                    }
                                }
                            }
                        }*/
                    }else{
                        throw new Exception(json_encode($result));
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComunicacionBajas.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "consultarStatus":
        include_once '../modelo/mdlSolicitud.php';
        $ticket = $_POST["ticket"];
        $id_solicitud = $_POST["id_solicitud"];
        try{
            $mdlSolicitud = new mdlSolicitud();
            $solicitud = $mdlSolicitud->verSolicitud($id_solicitud);
            $tipo_documento = $solicitud["tipo_documento"];
            $nombre_documento = $solicitud["nombre_solicitud"];
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("getStatus", json_encode(array("ticket"=>$ticket,"id_solicitud"=>$id_solicitud)), $_SESSION["empresa"]["ruc_empresa"], "", "G", $solicitud["serie"], $solicitud["correlativo"]);
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/getstatus2.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/getstatus.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R");
            //http://localhost/facturacion/bilservice/billService.xml
            $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                            [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                            'trace' => TRUE , 
                            'soap_version' => SOAP_1_1,
                            'soap_defencoding' => 'UTF-8' ] );
            $usuario_sunat = "";
            $password_sunat = "";
            $empresa = $_SESSION["empresa"];
            if($empresa["modo_autenticacion"]=="E"){
                $usuario_sunat = USERNAME_SUNAT;
                $password_sunat = PASSWORD_SUNAT;
            }elseif($empresa["modo_autenticacion"]=="P"){
                $usuario_sunat = $empresa["username_sunat"];
                $password_sunat = $empresa["password_sunat"];
            }
            //throw new Exception("FIN DEL FICHERO");
            $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <wsse:UsernameToken>
                    <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                    <wsse:Password>' . $password_sunat . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>';
            $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
            $file_ZIP = file_get_contents($filename_zip);
            $argumentos = [['ticket' => $ticket]];
            $mdlSolicitud->actualizarSolicitud($id_solicitud, "", date("Y-m-d H:i:s"));
            $result = $cliente_SUNAT->__soapCall('getStatus', $argumentos, null, $headers);
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
            if ($cliente_SUNAT->fault) {
                $datos_enviar = array(
                    "token"=>$_SESSION["token"],
                    'id_solicitud'=>$id_solicitud,
                );
                $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
            } else {
                if(is_soap_fault($cliente_SUNAT)){
                    $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        'id_solicitud'=>$id_solicitud,
                    );
                    //$result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                    throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                } else {
                    //error_log(print_r($cliente_SUNAT,true));
                    $statusRespuesta = $result->status;
                    $statusCode = $statusRespuesta->statusCode;
                    $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "R-".$nombre_documento);
                    $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C", $statusCode);
                    $fileR_ZIP = $statusRespuesta->content;
                    $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                    file_put_contents($filenameR_zip, $fileR_ZIP);
                    $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                    $fileR_ZIP = file_get_contents($filenameR_zip);
                    $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);

                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                        "nombre_documento"=>$nombre_documento,
                        "ticketRespuesta" => $ticket,
                        "id_solicitud" => $solicitud["id_solicitud_servidor"],
                        "statusCode" => $statusCode,
                    );
                    $datos_enviar = json_encode($datos_enviar);

                    $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                    $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                    if ($cliente2->fault) {
                        throw new Exception($result);
                    } else {
                        $error = $cliente2->getError();
                        if ($error) {
                            throw new Exception($error);
                        } else {
                            $result = json_decode($result);
                            if($result->code=="0"){
                                $_SESSION["token"] = $result->mensaje;
                                if($statusCode=="0"){
                                    $mdlSolicitud->actualizarSolicitud($id_solicitud, "", "", date("Y-m-d\TH:i:s"));
                                    $solicitud = $mdlSolicitud->verSolicitud($id_solicitud);
                                    $mdlSolicitud->actualizarSolicitud2($id_solicitud, "", "U");
                                    if($tipo_documento == "E"){
                                        $comprobante = json_decode($solicitud["data_solicitud"],true);
                                        $detalles = $comprobante["detalles"];
                                        //throw new Exception("PRUEBA ".count($detalles));
                                        foreach ($detalles as $key => $value) {
                                            $idDetalle = $value["id"];
                                            $serieDetalle = $value["numero"];
                                            $corrDetalle = intval($value["correlativo"]);
                                            if($idDetalle>0){
                                                if($comprobante["tiporesumen"]=="1"){
                                                    $result = $mdlSolicitud->actualizarSolicitud($idDetalle, "", $solicitud["fechahora_envio"], date("Y-m-d\TH:i:s"), "M", array());
                                                }elseif($comprobante["tiporesumen"]=="3"){
                                                    $result = $mdlSolicitud->actualizarSolicitud2($idDetalle,"","B");
                                                }else{
                                                    error_log("NO DEFINIDO");
                                                }
                                            }else{
                                                error_log("NO SE ENCUENTRA EL DOCUMENTO : ".$serieDetalle."-".$corrDetalle);
                                                //throw new Exception("NO SE ENCUENTRA EL DOCUMENTO : ".$serieDetalle."-".$corrDetalle);
                                            }
                                        }
                                    }elseif($tipo_documento == "A"){
                                        $comprobante = json_decode($solicitud["data_solicitud"],true);
                                        $detalles = $comprobante["detalles"];
                                        foreach ($detalles as $key => $value) {
                                            $idDetalle = $value["id"];
                                            $serieDetalle = $value["numero"];
                                            $corrDetalle = intval($value["correlativo"]);
                                            if($idDetalle>0){
                                                $result = $mdlSolicitud->actualizarSolicitud2($idDetalle,"","B");
                                            }else{
                                                error_log("NO SE ENCUENTRA EL DOCUMENTO : ".$serieDetalle."-".$corrDetalle);
                                                //throw new Exception("NO SE ENCUENTRA EL DOCUMENTO : ".$serieDetalle."-".$corrDetalle);
                                            }
                                        }
                                    }
                                } else {
                                    $comprobante = json_decode($solicitud["data_solicitud"],true);
                                    error_log("RESUMEN RECHAZADO");
                                    if($statusCode=="99" && $tipo_documento == "E" && $comprobante["tiporesumen"]=="1"){
                                        $mdlSolicitud->actualizarSolicitud($id_solicitud, "", "", date("Y-m-d\TH:i:s"));
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud, "", "V");
                                        $data_solicitud = $solicitud["data_solicitud"];
                                        $respuesta = Algoritmos::ComprobarRespuestaXML(__DIR__ ."/../ficheros/R-".$nombre_documento."xml");
                                        $repetidos2 = array();
                                        if($respuesta["codigo"]=="2282"){
                                            error_log("RESUMEN DE REPETIDOS");
                                            $repetidos = $respuesta["detalle"];
                                            $desde = strpos($repetidos, "[[");
                                            $hasta = strpos($repetidos, "]]");
                                            $repetidos = substr($repetidos, $desde + 1, $hasta - $desde);
                                            $repetidos = explode(", ", $repetidos);
                                            foreach ($repetidos as $repetido) {
                                                $repetido = substr($repetido, 1, strlen($repetido) - 2);
                                                $repetido = explode("-", $repetido);
                                                $repetido2 = array(
                                                    "tipo"=> $repetido[0],
                                                    "tipodoc"=> substr($repetido[1], 0, 1),
                                                    "serie"=> substr($repetido[1], 1, 4),
                                                    "numeracion"=> $repetido[2]
                                                );
                                                $repetidos2[] = $repetido2;
                                            }
                                            $comprobante = json_decode($data_solicitud,true);
                                            $detalles = $comprobante["detalles"];
                                            $ids_detalles = array();
                                            foreach ($repetidos2 as $repetido) {
                                                foreach ($detalles as $detalle) {
                                                    if($detalle["tipo"]==$repetido["tipo"] && $detalle["numero"]==$repetido["serie"] && intval($detalle["correlativo"])==intval($repetido["numeracion"])){
                                                        $id_solicitud_repetida = $detalle["id"];
                                                        break;
                                                    }
                                                }
                                                $ids_detalles[] = $id_solicitud_repetida;
                                            }
                                            foreach ($ids_detalles as $id_solicitud_repetida) {
                                                error_log("SE ACTUALIZA LA SOLICITUD ".$id_solicitud_repetida);
                                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_repetida, "", "M");
                                            }
                                        }
                                        echo json_encode(array(
                                            "correcto"=>true,
                                            "parametros"=>"",
                                            "url"=>"",
                                            "vista"=>"lstResumenBoletas.php",
                                            "mensaje"=>"EL RESUMEN HA SIDO RECHAZADO"
                                        ));
                                    }else{
                                        throw new Exception("AUN EN PROCESO");
                                    }
                                    exit();
                                }
                            }else{
                                throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                            }
                        }
                    }
                }
            }
            $paginaRespuesta = "";
            if($tipo_documento == "A"){
                $paginaRespuesta = "lstComunicacionBajas.php";
            }else{
                $paginaRespuesta = "lstResumenBoletas.php";
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>$paginaRespuesta,
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            error_log($e->getMessage());
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA CON SUNAT, VUELVE A INTENTARLO MAS TARDE",
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "comprobarCDR":
        include_once '../modelo/mdlSolicitud.php';
        $id_solicitud = $_POST["id_solicitud"];
        try{
            $mdlSolicitud = new mdlSolicitud();
            $solicitud = $mdlSolicitud->verSolicitud($id_solicitud);
            $nombre_documento = $solicitud["nombre_solicitud"];
            $comprobante = json_decode($solicitud["data_solicitud"],true);
            if(strlen($comprobante["numerofactura"])>0){
                $tipoDoc = "01";
                $numeracion = $comprobante["numerofactura"];
                $prefijo = "F";
                $cliente2 = new nusoap_client(URL_ENV_FAC);
            }elseif(strlen($comprobante["numeroboleta"])>0){
                $tipoDoc = "03";
                $numeracion = $comprobante["numeroboleta"];
                $prefijo = "B";
                $cliente2 = new nusoap_client(URL_ENV_BOL);
            }elseif(strlen($comprobante["numeronotacredito"])>0){
                $tipoDoc = "07";
                $numeracion = $comprobante["numeronotacredito"];
                $prefijo = substr($comprobante["numeroreferencia"], 0, 1);
                $cliente2 = new nusoap_client(URL_ENV_NTC);
            }elseif(strlen($comprobante["numeronotadebito"])>0){
                $tipoDoc = "08";
                $numeracion = $comprobante["numeronotadebito"];
                $prefijo = substr($comprobante["numeroreferencia"], 0, 1);
                $cliente2 = new nusoap_client(URL_ENV_NTD);
            }
            $numeracion = explode("-", $numeracion);
            $serie = $prefijo.$numeracion[0];
            $numero = intval($numeracion[1]);
            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("getStatusCdr", json_encode(array("ruc"=>$_SESSION["empresa"]["ruc_empresa"],"tipo"=>$tipoDoc,"serie"=>$serie,"numero"=>$numero,"id_solicitud"=>$id_solicitud)), $_SESSION["empresa"]["ruc_empresa"], "", "H", $serie, $numero);
            $token = "";
            /*if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }*/
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception(json_encode($error));
            }
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R");
            $cliente_SUNAT = new SoapClient(WBSV_CON_PRO, 
                            [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                            'trace' => TRUE , 
                            'soap_version' => SOAP_1_1,
                            'soap_defencoding' => 'UTF-8' ] );
            $usuario_sunat = "";
            $password_sunat = "";
            $empresa = $_SESSION["empresa"];
            if($empresa["modo_autenticacion"]=="E"){
                $usuario_sunat = USERNAME_SUNAT;
                $password_sunat = PASSWORD_SUNAT;
            }elseif($empresa["modo_autenticacion"]=="P"){
                $usuario_sunat = $empresa["username_sunat"];
                $password_sunat = $empresa["password_sunat"];
            }
            $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <wsse:UsernameToken>
                    <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                    <wsse:Password>' . $password_sunat . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>';
            $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
            $file_ZIP = file_get_contents($filename_zip);
            $argumentos = [['rucComprobante' => $_SESSION["empresa"]["ruc_empresa"],'tipoComprobante'=>$tipoDoc,'serieComprobante'=>$serie,'numeroComprobante'=>$numero]];
            $result = $cliente_SUNAT->__soapCall('getStatusCdr', $argumentos, null, $headers);
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
            if ($cliente_SUNAT->fault) {
                $datos_enviar = array(
                    "token"=>$_SESSION["token"],
                    'id_solicitud'=>$id_solicitud,
                );
                $result2 = $cliente2->call("recibirError", array("ruc"=>$user_wsdl,"password"=>$pass_wsdl,"json" => $datos_enviar));
                throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
            } else {
                if(is_soap_fault($cliente_SUNAT)){
                    $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        'id_solicitud'=>$id_solicitud,
                    );
                    $result2 = $cliente2->call("recibirError", array("ruc"=>$user_wsdl,"password"=>$pass_wsdl,"json" => $datos_enviar));
                    throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                } else {
                    $statusRespuesta = $result->statusCdr;
                    //error_log(print_r(array($statusRespuesta->statusCode,$statusRespuesta->statusMessage),true));
                    $statusCode = $statusRespuesta->statusCode;
                    $statusMessage = $statusRespuesta->statusMessage;
                    $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "R-".$nombre_documento);
                    $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T", $statusCode);
                    $fileR_ZIP = $statusRespuesta->content;
                    $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                    file_put_contents($filenameR_zip, $fileR_ZIP);
                    $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);

                    $respuesta = Algoritmos::ComprobarRespuestaXML(__DIR__ ."/../ficheros/R-".$nombre_documento."xml");
                    
                    $id_solicitud_local = $solicitud["id_solicitud"];
                    $id_solicitud = $solicitud["id_solicitud_servidor"];

                    $mdlSolicitud->actualizarSolicitud($id_solicitud_local, "", "", date("Y-m-d H:i:s"));

                    if($respuesta["codigo"]=="0"){
                        $fileR_ZIP = file_get_contents($filenameR_zip);
                        $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);

                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                        $datos_enviar = array(
                            "token"=>$_SESSION["token"],
                            "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                            "nombre_documento"=>$nombre_documento,
                            'id_solicitud'=>$id_solicitud,
                        );
                        $datos_enviar = json_encode($datos_enviar);

                        $result = $cliente2->call("recibirRespuesta", array("ruc"=>$user_wsdl,"password"=>$pass_wsdl,"json" => $datos_enviar));
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "T");
                        if ($cliente2->fault) {
                            throw new Exception($result);
                        } else {
                            $error = $cliente2->getError();
                            if ($error) {
                                throw new Exception($error);
                            } else {
                                $result = json_decode($result);
                                if($result->code=="0"){
                                    $_SESSION["token"] = $result->mensaje;
                                }else{
                                    throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                }
                            }
                        }
                    }else{
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I","","",$respuesta["codigo"]);
                        $datos_enviar = array(
                            "token"=>$_SESSION["token"],
                            'id_solicitud'=>$id_solicitud,
                            "errorcode"=>$respuesta["codigo"]
                        );
                        $result2 = $cliente2->call("recibirError", array("ruc"=>$user_wsdl,"password"=>$pass_wsdl,"json" => $datos_enviar));
                        throw new Exception("ERROR 3: ". $respuesta["detalle"]);
                    }
                    
                    $datos_enviar = array(
                        "token"=>$_SESSION["token"],
                        "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                        "nombre_documento"=>$nombre_documento,
                        "id_solicitud" => $solicitud["id_solicitud_servidor"],
                    );
                    $datos_enviar = json_encode($datos_enviar);
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "generarPDF":
        include '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $mdlSolicitud = new mdlSolicitud();
        $id_solicitud = $_GET["id_solicitud"];
        $idventa=$_GET["idventa"];
        if($idventa!=""){
            $rst=$objMovimiento->obtenerDataSQL("select d.*,c.code as moneda,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,d2.number as numeroguia 
            from documents d 
            inner join people p on p.id=d.client_id
            left join currencies c on c.id=d.currency_id
            left join documents d2 on d2.document_id=d.id and d2.document_type_id=16
            where d.id=".$idventa);
            $detalle = $rst->fetchObject();
            $objSolicitud = $mdlSolicitud->verSolicitud3(substr($detalle->number,0,1),substr($detalle->number, 1,3),substr($detalle->number,5,8));
            $guia = $detalle->numeroguia;
            $objSolicitud["observaciones"] = json_decode($detalle->detail, true)["comment"];
        }else{
            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud);
            $guia = "";
        }
        $tipo_documento = $objSolicitud["tipo_documento"];
        $nombre_documento = $objSolicitud["nombre_solicitud"];
        //if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
            Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
        //}
        if($tipo_documento == "G"){
            $dataSolicitud = json_decode($objSolicitud["data_solicitud"],true);
            $ubigeoini = $objMovimiento->obtenerDataSQL("select * from districts where code='".$dataSolicitud["ubigeoini"]."'")->fetchObject();
            $depini = $objMovimiento->obtenerDataSQL("select * from departments where code='".$ubigeoini->department_id."'")->fetchObject();
            $proini = $objMovimiento->obtenerDataSQL("select * from provinces where code='".$ubigeoini->province_id."'")->fetchObject();
            $ubigeofin = $objMovimiento->obtenerDataSQL("select * from districts where code='".$dataSolicitud["ubigeofin"]."'")->fetchObject();
            $depfin = $objMovimiento->obtenerDataSQL("select * from departments where code='".$ubigeofin->department_id."'")->fetchObject();
            $profin = $objMovimiento->obtenerDataSQL("select * from provinces where code='".$ubigeofin->province_id."'")->fetchObject();
            Algoritmos::GenerarPDFGuia($nombre_documento,__DIR__ ."/../ficheros/".$nombre_documento."xml",$_SESSION["empresa"],$tipo_documento,$objSolicitud,false,$ubigeoini->name.", ".$proini->name.", ".$depini->name,$ubigeofin->name.", ".$profin->name.", ".$depfin->name,$comentario);
        }else{
            Algoritmos::GenerarPDF($nombre_documento,__DIR__ ."/../ficheros/".$nombre_documento."xml",$_SESSION["empresa"],$tipo_documento,$objSolicitud,$guia);
        }
        break;
    case "generarPDF2":
        include '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $mdlSolicitud = new mdlSolicitud();
        $id_solicitud = $_GET["id_solicitud"];
        $idventa=$_GET["idventa"];
        if($idventa!=""){
            $rst=$objMovimiento->obtenerDataSQL("select d.*,c.code as moneda,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,d2.number as numeroguia 
            from documents d 
            inner join people p on p.id=d.client_id
            left join currencies c on c.id=d.currency_id
            left join documents d2 on d2.document_id=d.id
            where d.id=".$idventa);
            $detalle = $rst->fetchObject();
            $objSolicitud = $mdlSolicitud->verSolicitud3(substr($detalle->number,1,1),substr($detalle->number, 1,3),substr($detalle->number,5,8));
            $guia = $detalle->numeroguia;
        }else{
            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud);
            $guia = "";
        }
        $tipo_documento = $objSolicitud["tipo_documento"];
        $nombre_documento = $objSolicitud["nombre_solicitud"];
        //if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
            Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
        //}
        Algoritmos::GenerarPDF($nombre_documento,__DIR__ ."/../ficheros/".$nombre_documento."xml",$_SESSION["empresa"],$tipo_documento,$objSolicitud,$guia);
        break;
    case "numeroALetras":
        include_once '../modelo/NumeroTexto.php';
        $importe_total_venta = $_GET["importe"];
        $numeroTexto = new NumeroTexto($importe_total_venta);
        $decimales = intval(round($importe_total_venta,2)*100);
        $decimales = $decimales - intval(round($importe_total_venta,2))*100;
        $decimales = intval($decimales);
        if($decimales==0){
            $decimales = '00';
        }else{
            if($decimales<10){
                $decimales = '0'.strval($decimales);
            }else{
                $decimales = strval($decimales);
            }
        }
        echo json_encode(strtoupper($numeroTexto->convertirLetras($importe_total_venta)).' CON '.$decimales.'/100 ');
        break;
    case "tablaResumenes":
        include '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $id_empresa = $_GET["id_empresa"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $npag = $_GET["npag"];
        $_SESSION["Filtros"]["tablaEmpresas"] = array(
            "user"=>$user,
            "nombre"=>$nombre,
            "nrodoc"=>$nrodoc,
            "tipo"=>$tipo,
            "estado"=>$estado
        );
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes3($fecini,$id_empresa,$estado,$tipodoc);
        $n = "1";
        $Npaginas= 1;
        $i = 1;
        $limit = $_SESSION["Propiedad"]["LIMITE_DE_LISTAS"];
        $tabla = array();
        $detalles = array();
        $clases = array();
        $w=0;
        //</td><td><i class="material-icons" onclick="eliminarDetalle(\'fila_'+numero+'\');">clear</i></td></tr>';
        foreach ($comprobantes as $comprobante) {
            $tabla[$w][] = '<input type="hidden" name="idDetalle[]" value="'.$comprobante["id_solicitud"].'">'.$i;$i++;
            $tabla[$w][] = '<input type="hidden" name="serieDetalle[]" placeholder="001" maxlength="3" value="'.$comprobante["serie"].'">'.$comprobante["serie"];
            if($comprobante["tipo_documento"]=="F"){
                $tabla[$w][] = '<input class="tipodocCuerpo" type="hidden" value="01" name="tipodoc[]">'."-";
            }elseif($comprobante["tipo_documento"]=="B"){
                $tabla[$w][] = '<input class="tipodocCuerpo" type="hidden" value="03" name="tipodoc[]">'."-";
            }elseif($comprobante["tipo_documento"]=="C"){
                $tabla[$w][] = '<input class="tipodocCuerpo" type="hidden" value="07" name="tipodoc[]">'."-";
            }elseif($comprobante["tipo_documento"]=="D"){
                $tabla[$w][] = '<input class="tipodocCuerpo" type="hidden" value="08" name="tipodoc[]">'."-";
            }
            $tabla[$w][] = '<input type="hidden" name="correlativo[]" maxlength="8" placeholder="00001595" value="'.str_pad($comprobante["correlativo"],8,"0",STR_PAD_LEFT).'">'.str_pad($comprobante["correlativo"],8,"0",STR_PAD_LEFT);
            $tabla[$w][] = '<input type="hidden" name="dni[]" maxlength="8" placeholder="72312487" value="'.(empty($comprobante["doc_cliente"])?"-":$comprobante["doc_cliente"]).'">'.(empty($comprobante["doc_cliente"])?"-":$comprobante["doc_cliente"]);
            $tabla[$w][] = '<input type="hidden" step="0.01" min="0" name="total[]" value="'.$comprobante["total_doc"].'">'.$comprobante["total_doc"];
            $datos = array(
            );
            $detalles[] = str_replace("+","%20",urlencode(json_encode($datos)));
            $w++;
        }
        $parametros = $_GET;
        $href = "controlador/contComprobante.php?";
        foreach ($parametros as $key => $value) {
            if($key!="npag" && $key!="_"){
                $href.= $key."=".$value."&";
            }
        }
        echo json_encode(array(
            "correcto"=>true,
            "datos"=>$tabla,
            "clases"=>$clases,
            "detalles"=>$detalles,
            "npag"=>$npag,
            "Npaginas"=>$Npaginas,
            "Npaginacion"=>$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"],
            "href"=>$href
        ));
        break;
    case "listarComunicacion":
        include '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $id_empresa = $_GET["id_empresa"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $tipodoc = explode(",", $tipodoc);
        $tipodoc2 = array();
        foreach($tipodoc as $tipo){
            if($tipo=="01"){
                $tipo = "F";
            }elseif($tipo=="03"){
                $tipo = "B";
            }elseif($tipo=="07"){
                $tipo = "C";
            }
            $tipodoc2[] = "'".$tipo."'";
        }
        $tipodoc = implode(",", $tipodoc2);
        $estado = explode(",", $estado);
        $estado2 = array();
        foreach($estado as $est){
            $estado2[] = "'".$est."'";
        }
        $estado = implode(",", $estado2);
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes3($fecini,$id_empresa,$estado,$tipodoc);
        echo json_encode($comprobantes);
        break;
    case "generarPLE":
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        Algoritmos::GenerarPLESunat($fecini, $fecfin, $id_empresa);
        break;
    case "generarResumenComprobantes":
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        $tipodoc = $_GET["tipodoc"];
        Algoritmos::GenerarResumenComprobantes($fecini, $fecfin, $id_empresa, $tipodoc);
        break;
    case "actualizarEstadoServidor":
        include_once '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $_SESSION["Filtros"]["tablaEmpresas"] = array(
            "user"=>$user,
            "nombre"=>$nombre,
            "nrodoc"=>$nrodoc,
            "tipo"=>$tipo,
            "estado"=>$estado
        );
        try{
            $mdlSolicitud = new mdlSolicitud();
            $comprobantes = $mdlSolicitud->listarSolicitudes2($fecini,$fecfin,$id_empresa,$nombre,$estado,$tipodoc,0,0);
            $n = $comprobantes[1];
            $comprobantes = $comprobantes[0];
            $solicitudes = array();
            foreach ($comprobantes as $comprobante) {
                $solicitudes[] = array(
                    "id_solicitud_servidor"=>$comprobante["id_solicitud_servidor"],
                    "id_solicitud"=>$comprobante["id_solicitud"],
                    "estado_solicitud"=>$comprobante["estado_solicitud"],
                );
            }
            $token = $_SESSION["token"];
            if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                $token = $_SESSION["token"];
            }
            $datos_enviar = array(
                "token"=>$token,
                "solicitudes"=>$solicitudes
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/wsdl/actualizaestado.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("recibirEstados", array("json" => $datos_enviar));
            //throw new Exception($result);
            if ($cliente2->fault) {
                throw new Exception($result);
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE ACTUALIZO CORRECTAMENTE LOS ESTADOS"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL ACTUALIZAR LOS ESTADOS",
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "actualizarEstadoServidor2":
        include_once '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $_SESSION["Filtros"]["tablaEmpresas"] = array(
            "user"=>$user,
            "nombre"=>$nombre,
            "nrodoc"=>$nrodoc,
            "tipo"=>$tipo,
            "estado"=>$estado
        );
        try{
            $mdlSolicitud = new mdlSolicitud();
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "fechainicio"=>$fecini,
                "fechafinal"=>$fecfin
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("http://157.245.85.164/facturacion/wsdl/wsdl_comprobantes.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("descargar", array("ruc" =>$user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
            //throw new Exception($result);
            if ($cliente2->fault) {
                throw new Exception($result);
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    $comprobantes = $result->comprobantes;
                    //print_r($comprobantes);die();
                    foreach ($comprobantes as $comprobante) {
                        if($comprobante->tipo_documento=="B"){
                            if($comprobante->estado_solicitud=="C"){//CORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat, "M", array(),"S");
                            }elseif($comprobante->estado_solicitud=="B"){//BAJA
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud,"","B","","","","S");
                            }elseif($comprobante->estado_solicitud=="I"){//INCORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"",array(),"S");
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud, "", "I","","",$comprobante->error_code,"S",$comprobante->error_text);
                            }
                        }elseif($comprobante->tipo_documento=="F"){
                            if($comprobante->estado_solicitud=="I"){//INCORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"",array(),"S");
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud, "", "I","","",$comprobante->error_code,"S",$comprobante->error_text);
                            }elseif($comprobante->estado_solicitud=="B"){//BAJA
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud,"","B","","","","S");
                            }elseif($comprobante->estado_solicitud=="C"){//CORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"T",array(),"S");
                            }
                        }elseif($comprobante->tipo_documento=="G"){
                            if($comprobante->estado_solicitud=="I"){//INCORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"",array(),"S");
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud, "", "I","","",$comprobante->error_code,"S",$comprobante->error_text);
                            }elseif($comprobante->estado_solicitud=="B"){//BAJA
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud,"","B","","","","S");
                            }elseif($comprobante->estado_solicitud=="C"){//CORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"T",array(),"S");
                            }
                        }elseif($comprobante->tipo_documento=="C"){
                            if($comprobante->estado_solicitud=="I"){//INCORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"",array(),"S");
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud, "", "I","","",$comprobante->error_code,"S",$comprobante->error_text);
                            }elseif($comprobante->estado_solicitud=="B"){//BAJA
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud,"","B","","","","S");
                            }elseif($comprobante->estado_solicitud=="C"){//CORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_enviosunat, $comprobante->fechahora_respuestasunat,"T",array(),"S");
                            }
                        }
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE ACTUALIZO CORRECTAMENTE LOS ESTADOS"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL ACTUALIZAR LOS ESTADOS",
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarPendienteBoleta":
        try{
            include_once '../modelo/mdlSerie.php';
            include_once '../modelo/mdlSolicitud.php';
            $mdlSolicitud = new mdlSolicitud();
            $comprobantes = $mdlSolicitud->listarSolicitudes2('','',0,'','P,E','B',1,100);
            $comprobantes = $comprobantes[0];
            foreach ($comprobantes as $comprobante) {
                /*$token = $_SESSION["token"];
                if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                    $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                    $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                    $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                    $token = $_SESSION["token"];
                }*////////
                $token = "";
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                    
                $datos_enviar = array(
                    "token"=>$token,
                    "serieboleta"=>$comprobante["serie"],
                    "correlativoboleta"=>$comprobante["correlativo"],
                    "doc"=>$comprobante["doc_cliente"],
                    "nombre"=>$comprobante["nombre_cliente"],
                    "direccion"=>$comprobante["direccion_cliente"],
                    "total"=>$comprobante["total_doc"],
                    "comprobante"=> $comprobante["data_solicitud"]
                );
                $numeroboleta=$comprobante["correlativo"];
                $id_solicitud_local=$comprobante["id_solicitud"];
                $datos_enviar = json_encode($datos_enviar);
                //throw new Exception($datos_enviar);
                $cliente2 = new nusoap_client(URL_ENV_BOL);
                //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/boleta2_1.php");
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception(json_encode($error));
                }
                $result = $cliente2->call("enviarBoleta", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
                if ($cliente2->fault) {
                    throw new Exception(json_encode($result));
                } else {
                    $error = $cliente2->getError();
                    if ($error) {
                        throw new Exception($error);
                    } else {
                        $result = json_decode($result);
                        if($result->code=="0"){
                            $_SESSION["token"] = $result->mensaje;
                            $file_ZIP_BASE64 = $result->fileZIPBASE64;
                            $nombre_documento = $result->nombre_documento;
                            $id_solicitud = $result->id_solicitud;
                            $file_ZIP = base64_decode($file_ZIP_BASE64);
                            $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                            file_put_contents($filename_zip, $file_ZIP);
                            $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                            //http://localhost/facturacion/bilservice/billService.xml
                            if($declarar=="S"){
                                $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                                                [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                                                'trace' => TRUE , 
                                                'soap_version' => SOAP_1_1,
                                                'soap_defencoding' => 'UTF-8' ] );
                                $usuario_sunat = "";
                                $password_sunat = "";
                                $empresa = $_SESSION["empresa"];
                                if($empresa["modo_autenticacion"]=="E"){
                                    $usuario_sunat = USERNAME_SUNAT;
                                    $password_sunat = PASSWORD_SUNAT;
                                }elseif($empresa["modo_autenticacion"]=="P"){
                                    $usuario_sunat = $empresa["username_sunat"];
                                    $password_sunat = $empresa["password_sunat"];
                                }
                                //throw new Exception("USUARIOS: ". json_encode(array($usuario_sunat,$password_sunat)));
                                $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                    <wsse:UsernameToken>
                                        <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                                        <wsse:Password>' . $password_sunat . '</wsse:Password>
                                    </wsse:UsernameToken>
                                </wsse:Security>';
                                $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
                                $file_ZIP = file_get_contents($filename_zip);
                                $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                                $result = $cliente_SUNAT->__soapCall('sendBill', $argumentos, null, $headers);
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                                if ($cliente_SUNAT->fault) {
                                    $datos_enviar = array(
                                        "token"=>$_SESSION["token"],
                                        'id_solicitud'=>$id_solicitud,
                                    );
                                    $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                    throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                                } else {
                                    if(is_soap_fault($cliente_SUNAT)){
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                                        $datos_enviar = array(
                                            "token"=>$_SESSION["token"],
                                            'id_solicitud'=>$id_solicitud,
                                        );
                                        $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                        throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                                    } else {
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                                        $fileR_ZIP = $result->applicationResponse;
                                        $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                                        file_put_contents($filenameR_zip, $fileR_ZIP);
                                        $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                                        $fileR_ZIP = file_get_contents($filenameR_zip);
                                        $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);
                                        
                                        $datos_enviar = array(
                                            "token"=>$_SESSION["token"],
                                            "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                                            "nombre_documento"=>$nombre_documento,
                                            'id_solicitud'=>$id_solicitud,
                                        );
                                        $datos_enviar = json_encode($datos_enviar);
                                        
                                        $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud, "", "T");
                                        if ($cliente2->fault) {
                                            throw new Exception($result);
                                        } else {
                                            $error = $cliente2->getError();
                                            if ($error) {
                                                throw new Exception($error);
                                            } else {
                                                $result = json_decode($result);
                                                if($result->code=="0"){
                                                    $_SESSION["token"] = $result->mensaje;
                                                }else{
                                                    throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            throw new Exception($result->mensaje);
                        }
                    }
                }
            
                echo json_encode(array(
                    "correcto"=>true,
                    "parametros"=>"",
                    "url"=>"",
                    "vista"=>"",
                    "ejecutar"=>"",
                    "mensaje"=>"SE REGISTRO CORRECTAMENTE LA BOLETA: ".$numeroboleta
                )); 
            }
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL REGISTRAR LA BOLETA: ".$numeroboleta,
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarPendienteFactura":
        try{
            include_once '../modelo/mdlSerie.php';
            include_once '../modelo/mdlSolicitud.php';
            $mdlSolicitud = new mdlSolicitud();
            $comprobantes = $mdlSolicitud->listarSolicitudes2('','',0,'','P,E','F',1,100);
            $comprobantes = $comprobantes[0];
            foreach ($comprobantes as $comprobante) {
                /*$token = $_SESSION["token"];
                if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                    $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                    $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                    $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                    $token = $_SESSION["token"];
                }*/
                $token = "";
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $datos_enviar = array(
                    "token"=>$token,
                    "seriefactura"=>$comprobante["serie"],
                    "correlativofactura"=>$comprobante["correlativo"],
                    "doc"=>$comprobante["doc_cliente"],
                    "nombre"=>$comprobante["nombre_cliente"],
                    "direccion"=>$comprobante["direccion_cliente"],
                    "total"=>$comprobante["total_doc"],
                    "comprobante"=> $comprobante["data_solicitud"]
                );
                $datos_enviar = json_encode($datos_enviar);
                $numerofactura=$comprobante["correlativo"];
                $id_solicitud_local=$comprobante["id_solicitud"];
                //throw new Exception($datos_enviar);
                $cliente2 = new nusoap_client(URL_ENV_FAC);
                //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                }
                $result = $cliente2->call("enviarFactura", array("ruc"=>$user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                //throw new Exception($result);
                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
                if ($cliente2->fault) {
                    throw new Exception($result);
                } else {
                    $error = $cliente2->getError();
                    if ($error) {
                        throw new Exception($error);
                    } else {
                        $result = json_decode($result);
                        if($result->code=="0"){
                            $_SESSION["token"] = $result->mensaje;
                            $file_ZIP_BASE64 = $result->fileZIPBASE64;
                            $nombre_documento = $result->nombre_documento;
                            $id_solicitud = $result->id_solicitud;
                            $file_ZIP = base64_decode($file_ZIP_BASE64);
                            $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                            file_put_contents($filename_zip, $file_ZIP);
                            $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                            //http://localhost/facturacion/bilservice/billService.xml
                            if($declarar=="S"){
                                $cliente_SUNAT = new SoapClient(WBSV_ENV_PRO, 
                                                [ 'cache_wsdl' => WSDL_CACHE_NONE, 
                                                'trace' => TRUE , 
                                                'soap_version' => SOAP_1_1,
                                                'soap_defencoding' => 'UTF-8' ] );
                                $usuario_sunat = "";
                                $password_sunat = "";
                                $empresa = $_SESSION["empresa"];
                                if($empresa["modo_autenticacion"]=="E"){
                                    $usuario_sunat = USERNAME_SUNAT;
                                    $password_sunat = PASSWORD_SUNAT;
                                }elseif($empresa["modo_autenticacion"]=="P"){
                                    $usuario_sunat = $empresa["username_sunat"];
                                    $password_sunat = $empresa["password_sunat"];
                                }
                                //throw new Exception("USUARIOS: ". json_encode(array($usuario_sunat,$password_sunat)));
                                $WSHeader = '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                                    <wsse:UsernameToken>
                                        <wsse:Username>' . $usuario_sunat . '</wsse:Username>
                                        <wsse:Password>' . $password_sunat . '</wsse:Password>
                                    </wsse:UsernameToken>
                                </wsse:Security>';
                                $headers = new SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd', 'Security', new SoapVar($WSHeader, XSD_ANYXML));
                                $file_ZIP = file_get_contents($filename_zip);
                                $argumentos = [['fileName' => $nombre_documento.'zip', 'contentFile' => $file_ZIP]];
                                $result = $cliente_SUNAT->__soapCall('sendBill', $argumentos, null, $headers);
                                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "S");
                                if ($cliente_SUNAT->fault) {
                                    $datos_enviar = array(
                                        "token"=>$_SESSION["token"],
                                        'id_solicitud'=>$id_solicitud,
                                    );
                                    $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                    throw new Exception("ERROR ".$result["faultcode"].": ".$result["faultstring"]." ". json_encode(array($usuario_sunat, $password_sunat,$empresa)));
                                } else {
                                    if(is_soap_fault($cliente_SUNAT)){
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "I");
                                        $datos_enviar = array(
                                            "token"=>$_SESSION["token"],
                                            'id_solicitud'=>$id_solicitud,
                                        );
                                        $result2 = $cliente2->call("recibirError", array("json" => $datos_enviar));
                                        throw new Exception("ERROR 2: ". json_encode($result->faultstring));
                                    } else {
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "C");
                                        $fileR_ZIP = $result->applicationResponse;
                                        $filenameR_zip = __DIR__ ."/../ficheros/R-".$nombre_documento."zip";
                                        file_put_contents($filenameR_zip, $fileR_ZIP);
                                        $filenameR_xml = Algoritmos::DescomprimirFichero("R-".$nombre_documento, $filenameR_zip);
                                        $fileR_ZIP = file_get_contents($filenameR_zip);
                                        $fileR_ZIP_BASE64 = base64_encode($fileR_ZIP);
                                        
                                        $datos_enviar = array(
                                            "token"=>$_SESSION["token"],
                                            "fileR_ZIP_BASE64"=> $fileR_ZIP_BASE64,
                                            "nombre_documento"=>$nombre_documento,
                                            'id_solicitud'=>$id_solicitud,
                                        );
                                        $datos_enviar = json_encode($datos_enviar);
                                        
                                        $result = $cliente2->call("recibirRespuesta", array("json" => $datos_enviar));
                                        $mdlSolicitud->actualizarSolicitud2($id_solicitud, "", "T");
                                        if ($cliente2->fault) {
                                            throw new Exception($result);
                                        } else {
                                            $error = $cliente2->getError();
                                            if ($error) {
                                                throw new Exception($error);
                                            } else {
                                                $result = json_decode($result);
                                                if($result->code=="0"){
                                                    $_SESSION["token"] = $result->mensaje;
                                                }else{
                                                    throw new Exception($result->mensaje." EN LA LINEA: ".$result->line);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            throw new Exception($result->mensaje);
                        }
                    }
                }
                echo json_encode(array(
                    "correcto"=>true,
                    "parametros"=>"",
                    "url"=>"",
                    "vista"=>"",
                    "ejecutar"=>"",
                    "mensaje"=>"SE REGISTRO CORRECTAMENTE LA FACTURA: ".$numerofactura
                ));
            }

            $comprobantes = $mdlSolicitud->listarSolicitudes2('','',0,'','P,E','C',1,100);
            $comprobantes = $comprobantes[0];
            foreach ($comprobantes as $comprobante) {
                /*$token = $_SESSION["token"];
                if(empty($_SESSION["token"]) || strlen(trim($_SESSION["token"]))==0 || !Algoritmos::ComprobarTOKENAutorizacion($token)){
                    $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                    $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                    $_SESSION["token"] = Algoritmos::ObtenerTOKENAutorizacion($user_wsdl, $pass_wsdl);
                    $token = $_SESSION["token"];
                }*/
                $token = "";
                $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
                $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
                $datos_enviar = array(
                    "token"=>$token,
                    "serienota"=>$comprobante["serie"],
                    "correlativonota"=>$comprobante["correlativo"],
                    "comprobante"=> $comprobante["data_solicitud"]
                );
                $datos_enviar = json_encode($datos_enviar);
                $numerofactura=$comprobante["correlativo"];
                $id_solicitud_local=$comprobante["id_solicitud"];
                //throw new Exception($datos_enviar);
                $cliente2 = new nusoap_client(URL_ENV_NTC);
                //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                }
                $result = $cliente2->call("enviarNotaCredito", array("ruc" => $user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
                //throw new Exception($result);
                $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
                if ($cliente2->fault) {
                    throw new Exception($result);
                } else {
                    $error = $cliente2->getError();
                    if ($error) {
                        throw new Exception($error);
                    } else {
                        $result = json_decode($result);
                        if($result->code=="0"){
                            $_SESSION["token"] = $result->mensaje;
                            $file_ZIP_BASE64 = $result->fileZIPBASE64;
                            $nombre_documento = $result->nombre_documento;
                            $id_solicitud = $result->id_solicitud;
                            $file_ZIP = base64_decode($file_ZIP_BASE64);
                            $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                            file_put_contents($filename_zip, $file_ZIP);
                            $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                            
                        }else{
                            throw new Exception($result->mensaje);
                        }
                    }
                }
                echo json_encode(array(
                    "correcto"=>true,
                    "parametros"=>"",
                    "url"=>"",
                    "vista"=>"",
                    "ejecutar"=>"",
                    "mensaje"=>"SE REGISTRO CORRECTAMENTE LA FACTURA: ".$numerofactura
                ));
            }
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL REGISTRAR LA FACTURA: ".$numerofactura,
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviarGuiaRemision":
        include_once '../modelo/mdlSerie.php';
        include_once '../modelo/mdlSolicitud.php';
        require("../cado/cado.php");
        $objMovimiento = new clsAccesoDatos(1,1);
        $idventa=$_GET["idventa"];
    
         $rst=$objMovimiento->obtenerDataSQL("select d.*,p.type as tipodocumento,p.business_name,p.name,p.surname_father,p.surname_mother,p.dni,p.ruc,p.address,
            re.code as codigomotivo,re.name as motivo,dref.number as venta,sh.business_name as razontransportista,
            sh.ruc as ructransportista,sh.type as tipotransportista,sh.dni as dnitransportista,concat(sh.surname_father,' ',sh.surname_mother) as apellidotransportista,sh.name as transportista
            from documents d 
            left join documents dref on dref.id=d.document_id
            inner join people p on p.id=d.client_id
            inner join reasons re on re.id=d.reason_id
            left join people sh on sh.id=d.shipper_id
            where d.id=".$idventa);
        $detalle = $rst->fetchObject();
        $comentario = json_decode($detalle->detail);
        
        $modo = $_POST["modo"];
        
        $serie = array();
        $serie["numero_serie"] = substr($detalle->number, 1,3);
        $_POST["numfac"]=substr($detalle->number, 5,8);
        $numeroguia = substr($detalle->number,1,12);
        $fechaemision = substr($detalle->date_time,0,10);
        $horaemision = substr($detalle->date_time,11,8);

        if($detalle->client_id>0){
            if($detalle->tipodocumento=="6"){
                $nombre = (trim($detalle->business_name));
                $doc = trim($detalle->ruc);
            }elseif($detalle->tipodocumento=="0"){
                $nombre = (trim($detalle->business_name));
                $doc = trim(str_pad($detalle->ruc,15,"0",STR_PAD_LEFT));
            }else{
                $nombre = (trim($detalle->surname_father." ".$detalle->surname_mother." ".$detalle->name));
                $doc = trim($detalle->dni);
            }
            
            $direccion = (trim($detalle->address));
            $tipodoc = $detalle->tipodocumento;
            $codUbigeo = "0000";
            
        }else{
            $codUbigeo = "0000";
            $tipodoc = 1;
            $nombre = "VARIOS";
            $direccion = "";
            $doc = "";
        }          
        $documentosanexos = array();
        
        $rs1=$objMovimiento->obtenerDataSQL("select dm.*,p.name as producto,p.igv as igvproducto,u.code as codigounidad,u.name as unidad,p.code as codigoproducto,p.presentation,b.name as marca,p.type 
            from document_details dm
            inner join products p on p.id=dm.product_id
            inner join units u on u.id=dm.unit_id
            left join brands b on b.id=p.brand_id
            where dm.document_id=".$idventa);
        while($dat=$rs1->fetchObject()){
            //$descripcion = trim($dat->producto." ".$dat->presentation." ".$dat->marca);
            $detail = json_decode($dat->detail);
            $descripcion = $detail->producto;
            $rs2 = $objMovimiento->obtenerDataSQL("select l.*,ps.serie,pm.lot_id,pm.product_serie_id 
                from product_moves pm
                left join lots l on l.id=pm.lot_id
                left join product_series ps on ps.id=pm.product_serie_id
                where pm.guide_detail_id=".$dat->id." and pm.document_id=".$dat->document_id);
            if($rs2->rowCount()>0){
                if($dat->type=="R"){
                    $descripcion.=" LOTE: ";
                    while($dat2=$rs2->fetchObject()){
                        $descripcion.=$dat2->lot." | F.V.: ".$dat2->due_date."\n";
                    }
                }elseif($dat->type=="M" || $dat->type=="E" || $dat->type=="C"){
                    $descripcion.=" \nSERIE: ";
                    while($dat2=$rs2->fetchObject()){
                        $descripcion.=$dat2->serie."\n";
                    }
                }
            }
            $data_detalle = json_decode($dat->detail);
            if(!is_null($data_detalle->unit_id) && $data_detalle->unit_id!=""){
                $data_unidad = $objMovimiento->obtenerDataSQL("select u.code as codigounidad,u.name as unidad 
                    from units u where u.id=".$data_detalle->unit_id)->fetchObject();
                $cantidad = $data_detalle->quantity;
            }else{
                $data_unidad = $objMovimiento->obtenerDataSQL("select u.code as codigounidad,u.name as unidad 
                    from units u where u.id=".$dat->unit_id)->fetchObject();
                $cantidad = $dat->quantity;
            }

            $detalles[] = array(
                "codigo" => $dat->codigoproducto,
                "unidadmedida" => $data_unidad->codigounidad,
                "cantidad" => $cantidad,
                "descripcion" => $descripcion,
                "pesounitario" => 1,
                "nombreunidad" => $data_unidad->unidad,
            );
        }
        try{
            if(count($detalles)==0){
                throw new Exception("NO TIENE NINGUN DETALLE");
            }
            $nom = "";
            if($detalle->tipotransportista==6 || $detalle->tipotransportista==4){
                $nom = $detalle->razontransportista;
                $ape = "-";
                $doc2 = $detalle->ructransportista;
            }else{
                $nom = $detalle->apellidotransportista;
                $ape = $detalle->transportista;
                $doc2 = $detalle->dnitransportista;
            }

            $guia = array(
                "numeroguia" => $numeroguia,
                "fechaemision" => $fechaemision,
                "horaemision" => $horaemision,
                "fechatraslado" => $comentario->transfer_date,
                "tipodoc" => $tipodoc,
                "doc" => $doc,
                "usuario" => $nombre,
                "direccion" => $direccion,
                "modalidad" => $comentario->modalidad,
                "tipomotivo" => $detalle->codigomotivo,
                "motivo" => $detalle->motivo,
                "observaciones" => $comentario->comment,
                "transbordo" => $comentario->transbordo,
                "paquetes" => $comentario->number_packages,
                "unidadpeso" => "KGM",
                "pesototal" => $detalle->weight,
                "ordencompra" => $comentario->orden_compra,
                "numerofactura" => $detalle->venta,
                "transporte" => array(
                    "tipodoc" => $detalle->tipotransportista, 
                    "doc" => $doc2, 
                    "nombre" => $nom, 
                    "apellido" => $ape,
                    "placa" => $comentario->transport_plate,
                    "licencia" =>  $comentario->transport_licencia,
                ),
                "ubigeoini" => $detalle->ubigeo_start_code,
                "direccini" => $comentario->start_address,
                "ubigeofin" => $detalle->ubige_end_code,
                "direccfin" => utf8_encode($comentario->final_address),
                "detalles"=>$detalles
            );
            //print_r($guia);
            $mdlSolicitud = new mdlSolicitud();

            $id_solicitud_local = $mdlSolicitud->insertarSolicitud("sendBill", json_encode($guia), $_SESSION["empresa"]["ruc_empresa"], "", "G", $serie["numero_serie"], $_POST["numfac"], $doc, $nombre, $detalle->weight, $direccion);
            $token="";
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "token"=>$token,
                "serieguia"=>$serie["numero_serie"],
                "correlativoguia"=>$_POST["numfac"],
                "doc"=>$doc,
                "nombre"=>$nombre,
                "direccion"=>$direccion,
                "total"=>$_POST["total_peso"],
                "comprobante"=> json_encode($guia)
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("http://157.245.85.164/facturacion/wsdl/wsdl_guiaremision.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("enviarGuiaRemision", array("ruc"=>$user_wsdl,"password" => $pass_wsdl,"json" => $datos_enviar));
            //throw new Exception($result);
            $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "E");
            if ($cliente2->fault) {
                throw new Exception($result);
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        $_SESSION["token"] = $result->mensaje;
                        $file_ZIP_BASE64 = $result->fileZIPBASE64;
                        $nombre_documento = $result->nombre_documento;
                        $id_solicitud = $result->id_solicitud;
                        $file_ZIP = base64_decode($file_ZIP_BASE64);
                        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
                        file_put_contents($filename_zip, $file_ZIP);
                        $mdlSolicitud->actualizarSolicitud($id_solicitud_local, $nombre_documento);
                        $mdlSolicitud->actualizarSolicitud2($id_solicitud_local, "", "R","",$id_solicitud);
                        //http://localhost/facturacion/bilservice/billService.xml
                    }else{
                        throw new Exception($result->mensaje);
                    }
                }
            }

            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud_local);

            $tipo_documento = $objSolicitud["tipo_documento"];
            $nombre_documento = $objSolicitud["nombre_solicitud"];
            if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
                Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE REGISTRO CORRECTAMENTE LA GUIA: ".$numerofactura
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL REGISTRAR LA GUIA: ".$numerofactura,
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "actualizarEstadoServidorGuia":
        include_once '../modelo/mdlSolicitud.php';
        $fecini = $_GET["fecini"];
        $fecfin = $_GET["fecfin"];
        $id_empresa = $_GET["id_empresa"];
        $nombre = $_GET["nombre"];
        $tipodoc = $_GET["tipodoc"];
        $estado = $_GET["estado"];
        $_SESSION["Filtros"]["tablaEmpresas"] = array(
            "user"=>$user,
            "nombre"=>$nombre,
            "nrodoc"=>$nrodoc,
            "tipo"=>$tipo,
            "estado"=>$estado
        );
        try{
            $mdlSolicitud = new mdlSolicitud();
            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = array(
                "fechainicio"=>$fecini,
                "fechafinal"=>$fecfin
            );
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("http://157.245.85.164/facturacion/wsdl/wsdl_comprobantes.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("descargar", array("ruc" =>$user_wsdl, "password" => $pass_wsdl,"json" => $datos_enviar));
            //throw new Exception($result);
            if ($cliente2->fault) {
                throw new Exception($result);
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {
                    $result = json_decode($result);
                    $comprobantes = $result->comprobantes;
                    //print_r($comprobantes);exit();
                    foreach ($comprobantes as $comprobante) {
                        if($comprobante->tipo_documento=="G"){
                            if($comprobante->estado_solicitud=="C"){//CORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_envio, $comprobante->fechahora_respuestasunat, "M", array(),"S",$comprobante->error_text);
                            }elseif($comprobante->estado_solicitud=="B"){//BAJA
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud,"","B","","","","S");
                            }elseif($comprobante->estado_solicitud=="I"){//INCORRECTO
                                $mdlSolicitud->actualizarSolicitud($comprobante->id_solicitud, "", $comprobante->fechahora_envio, $comprobante->fechahora_respuestasunat,"",array(),"S",$comprobante->error_text);
                                $mdlSolicitud->actualizarSolicitud2($comprobante->id_solicitud, "", "I","","",$comprobante->error_code,"S");
                            }
                        }
                    }
                }
            }
            echo json_encode(array(
                "correcto"=>true,
                "parametros"=>"",
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "mensaje"=>"SE ACTUALIZO CORRECTAMENTE LOS ESTADOS"
            ));
        } catch (Exception $e){
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "ejecutar"=>"",
                "error"=>"OCURRIO UN PROBLEMA AL ACTUALIZAR LOS ESTADOS",
                "errorCode"=>$e->getMessage(),
                "line"=>$e->getLine(),
                "file"=>$e->getFile()
            ));
        }
        break;
    case "enviaremail":
        include '../modelo/mdlSolicitud.php';
        try {
            $emails = $_POST["emails"];
            $id_solicitud = $_POST['id_solicitud'];
            $comentario = $_POST['comentario'];
            //$a = array($email,$id_solicitud,$comentario);
            //echo json_encode($a);exit;
            $mdlSolicitud = new mdlSolicitud();
            $objSolicitud = $mdlSolicitud->verSolicitud($id_solicitud);
            $email = "";
            foreach ($emails as $key => $value) {
                if($email == ""){
                    $email = $value;
                }else{
                    $email = $email.",".$value;
                }
            }
            $tipo_documento = $objSolicitud["tipo_documento"];
            $nombre_documento = $objSolicitud["nombre_solicitud"];
            $datos = json_decode($objSolicitud["data_solicitud"]);
            //if(!file_exists(__DIR__ ."/../ficheros/".$nombre_documento."xml")){
            Algoritmos::DescomprimirFichero($nombre_documento, __DIR__ ."/../ficheros/".$nombre_documento."zip");
            //}
            // Algoritmos::GenerarPDF5($nombre_documento,__DIR__ ."/../ficheros/".$nombre_documento."xml",$_SESSION["empresa"],$tipo_documento,$direccion,$datos->placa);
            //Algoritmos::GenerarPDF5($nombre_documento,__DIR__ ."/../ficheros/".$nombre_documento."xml",$_SESSION["empresa"],$tipo_documento,$objSolicitud);
            $xml_fichero = __DIR__ ."/../ficheros/".$nombre_documento."xml";
            $xml = file_get_contents($xml_fichero);
            $pdf_fichero = __DIR__ ."/../ficheros/".$nombre_documento."pdf";
            $pdf = file_get_contents($pdf_fichero);
            $xml = utf8_encode($xml);
            $pdf = utf8_encode($pdf);
            $datos_paquete = array(
                "pdf"=>$pdf,
                "xml"=>$xml,
                "comentario"=>$comentario,
                "nombre"=>$nombre_documento,
                "correos"=> $email,
                "id"    =>$objSolicitud["id_solicitud"]
            );
            $datos_enviar = array($datos_paquete);

            $user_wsdl = $_SESSION["empresa"]["ruc_empresa"];
            $pass_wsdl = $_SESSION["usuario"]["pass_usuario"];
            $datos_enviar = json_encode($datos_enviar);
            //throw new Exception($datos_enviar);
            $cliente2 = new nusoap_client("https://facturae-garzasoft.com/facturacion/enviaEmail/enviaEmail.php");
            //$cliente2 = new nusoap_client("http://localhost/facturacion/wsdl/factura2_1.php");
            $error = $cliente2->getError();
            if ($error) {
                throw new Exception($error);
            }
            $result = $cliente2->call("enviaEmail", array("ruc"=>$user_wsdl, "password"=>$pass_wsdl, "json" => $datos_enviar));
            if ($cliente2->fault) {
                throw new Exception($cliente2->getError());
            } else {
                $error = $cliente2->getError();
                if ($error) {
                    throw new Exception($error);
                } else {

                    $data = json_decode($result);
                    if($data->code==0){
                        if($data->mensaje == "CORRECTO"){
                            $mensaje = "ENVIADO CORRECTAMENTE";
                        }else{
                            $mensaje = "CORREO NO ENVIADO, INTENTELO DE NUEVO";
                            throw new Exception($mensaje);
                        }
                    }else{
                        throw new Exception($data->mensaje);
                    }
                }
            }
            $cantidad = count($data->enviados);
            echo json_encode(array(
                "correcto"=>true,
                "url"=>"",
                "vista"=>"lstComprobantes.php",
                "parametros"=>"",
                "mensaje"=>$mensaje.","."\n"." ENVIADOS: ".$cantidad."/1"
            ));
        } catch (Exception $e) {
            $data = json_decode($result);
            //$cantidad = count($data->enviados);
            $cantidad = 0;
            echo json_encode(array(
                "correcto"=>false,
                "url"=>"",
                "vista"=>"",
                "error"=>$e->getMessage().","."\n"."ENVIADOS: ".$cantidad."/1"
            ));
        }
        break;
    default :
        echo json_encode(array(
            "correcto"=>false,
            "url"=>"../vista/index.php",
            "vista"=>"",
            "error"=>"NO ES UNA FUNCION VALIDA"
            ));
        header("Location: ../vista/index.php");
        break;
}