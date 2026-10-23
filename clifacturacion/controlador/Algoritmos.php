<?php
include __DIR__ . "/../vendor/autoload.php";
include_once __DIR__ . '/../modelo/mdlDistrito.php';
include_once __DIR__ . '/../modelo/mdlUnidad.php';
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Anouar\Fpdf\Fpdf;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Algoritmos {
    
    public static function ValidarUsuario($usuario,$tipo_usuario=null){
        if(empty($usuario)){
            return false;;
        }
        if(strlen(trim($usuario["user_usuario"]))==0 && strlen(trim($usuario["pass_usuario"]))==0){
            return false;
        }
        if(empty($usuario["persona"])){
            return false;
        }
        if(!empty($tipo_usuario)){
            if($usuario["tipo_usuario"]!=$tipo_usuario){
                return false;
            }
        }
        return true;
    }
    
    public static function FirmaDigital($filename_xml,$uriSignature,$nombre_certificado,$llave_certificado) {
        $domDocument = new DOMDocument();
        $domDocument->load($filename_xml);
        $ReferenceNodeName = 'ExtensionContent';
        $nombre_certificado = __DIR__ ."/../certificados/".$nombre_certificado;
        $info_cert = array();
        if (!$almacen_cert = file_get_contents($nombre_certificado)) {
            throw new Exception("NO SE ENCUENTRA EL FICHERO DEL CERTIFICADO CON EL NOMBRE: ".$nombre_certificado);
        }
        if (!openssl_pkcs12_read($almacen_cert, $info_cert, $llave_certificado)) {
            throw new Exception("NO SE PUEDE LEER EL CERTIFICADO CON LA CONTRASEÑA INDICADA");
        }
        $certificate = $info_cert["cert"];
        $privateKey = $info_cert["pkey"];
        
        $objSign = new RobRichards\XMLSecLibs\XMLSecurityDSig($uriSignature);
        $objSign->setCanonicalMethod(RobRichards\XMLSecLibs\XMLSecurityDSig::C14N);
        $objSign->addReference(
            $domDocument, 
            RobRichards\XMLSecLibs\XMLSecurityDSig::SHA1, 
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
            $options = array('force_uri' => true)
        );
        
        $objKey = new RobRichards\XMLSecLibs\XMLSecurityKey(RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
        $objKey->loadKey($privateKey);
        $elementsByTagName = $domDocument->getElementsByTagName($ReferenceNodeName);
        $item = $elementsByTagName->item(1);
        if(empty($item)){
            throw new Exception("OBJETO NULO");
        }
        $objSign->sign($objKey, $item);
        $objSign->add509Cert($certificate);
        $elementsByTagName = $domDocument->getElementsByTagName("Signature");
        $item = $elementsByTagName->item(0);
        return $domDocument;
    }
    
    public static function ComprimirFichero($nombre_documento,$filename_zip){
        $zip = new ZipArchive();
        if($zip->open($filename_zip,ZIPARCHIVE::CREATE)===true) {
            $zip->addFile(__DIR__ ."/../ficheros/".$nombre_documento."xml",$nombre_documento."xml");
            $zip->close();
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
    }

    public static function DescomprimirFichero($nombre_documento,$filename_zip){
        $zip = new ZipArchive();
        if($zip->open($filename_zip)===true) {
            $zip->extractTo(__DIR__ ."/../ficheros/",array($nombre_documento."xml"));
            $zip->close();
            return __DIR__ ."/../ficheros/".$nombre_documento."xml";
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
    }

    public static function ObtenerTOKENAutorizacion($user_wsdl,$pass_wsdl){
        $cliente = new nusoap_client($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
        //throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
        $error = $cliente->getError();
        if ($error) {
            throw new Exception($error);
        }
        $result = $cliente->call("getAutorizacion", array("ruc"=>$user_wsdl,"password" => $pass_wsdl));
        if ($cliente->fault) {
            throw new Exception($result);
        } else {
            $error = $cliente->getError();
            if ($error) {
                throw new Exception($error);
            } else {
                $result = json_decode($result);
                if($result->code=="0"){
                    $token = $result->mensaje;
                    return $token;
                }else{
                    throw new Exception($result->mensaje);
                }
            }
        }
    }
    
    public static function ComprobarTOKENAutorizacion($token){
        try{
            $cliente = new nusoap_client($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
            //throw new Exception($_SESSION["Propiedad"]["WSDL_AUTORIZACION"]);
            $error = $cliente->getError();
            if ($error) {
                throw new Exception($error);
                return false;
            }
            $result = $cliente->call("comprobarTOKEN", array("token"=>$token));
            if ($cliente->fault) {
                throw new Exception($result);
                return false;
            } else {
                $error = $cliente->getError();
                if ($error) {
                    throw new Exception($error);
                    return false;
                } else {
                    $result = json_decode($result);
                    if($result->code=="0"){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        } catch (Exception $e){
            return false;
        }
    }
    
    public static function GenerarPDF($nombre_documento,$filename_zip,$empresa,$tipo_documento,$objSolicitud = array(),$guia=''){
        //echo json_encode($tipo_documento);exit();
        //$filenameR_zip = __DIR__ ."/../ficheros/S".$id_solicitud."/R-".$nombre_documento."zip";
        /*$pdf = new Anouar\Fpdf\Fpdf();
        $pdf->AddPage('L','A4');
        $pdf->Cell(69,5,utf8_decode("MUNICIPALIDAD PROVINCIAL DE SATIPO"),1,0,'C');
        $pdf->Output('masiva.pdf','D');
        */
        // Variables correspondientes a la factura.
        $RUC = "";       // RUC DE ADQUIRIENTE
        $NombreCliente = "";
        $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
        $Serie = "";     // SERIE DEL DOCUMENTO
        $Numeracion = "";// NUMERACION DEL DOC
        $NomRazSoc = ""; // Nombre o Razón social.
        $FecEmi = "";    // Fecha de emisión.
        $Moneda = "";
        $Domicilio = ""; // Domicilio.
        $CodHash = "";   // Código Hash.
        $TipoDoc = "";   // Tipo de documento.
        $TotGrav = 0;    // Total gravado.
        $TotIGV = 0;     // Total IGV.
        $TotMonto = 0;   // Total importe. 
        $data = json_decode($objSolicitud["data_solicitud"]);
        $hora = $data->horaemision;
        $Orden = $data->orden;
        $Vendedor = $data->vendedor;
        // Variables correspondientes a los productos o servicios de la factura.
        $CodProdServ = array(); // Código.
        $ProdServ = array(); // Descripción.
        $Cant = array(); // Cantidad. 
        $UniMed = array(); // Unidad de medida.
        $Precio = array(); // Precio unitario.
        $Importe = array();  // Importe.
        // Obteniendo datos del archivo .XML (factura electrónica)======================
        if(!file_exists($filename_zip)){
            throw new Exception("ERROR EN LA DESCOMPRESION DEL ARCHIVO");
        }
        $xml = file_get_contents($filename_zip);
        #== Obteniendo datos del archivo .XML 
            $DOM = new DOMDocument('1.0', 'ISO-8859-1');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($xml);
            ### DATOS DE LA FACTURA ####################################################

            //Obteniendo el Numero del documento
            $DocXML = $DOM->getElementsByTagName('ID');
            foreach($DocXML as $Nodo){
                $Serie = $Nodo->nodeValue;
                break;
            }
            $Serie = explode("-", $Serie);
            $Numeracion = $Serie[1];
            $Serie = $Serie[0];
            
            // Obteniendo Fecha de emisión.
            $DocXML = $DOM->getElementsByTagName('IssueDate');
            foreach($DocXML as $Nodo){
                $FecEmi = $Nodo->nodeValue;
                break;
            }
            
            // Obteniend
            $DocXML = $DOM->getElementsByTagName('DocumentCurrencyCode');
            foreach($DocXML as $Nodo){
                $Moneda = $Nodo->nodeValue;
                break;
            }
            
            // Obteniendo Codigo Hash.
            $DocXML = $DOM->getElementsByTagName('DigestValue');
            foreach($DocXML as $Nodo){
                $CodHash = $Nodo->nodeValue;
                break;
            }    

            // Clave del tipo de documento.
            $DocXML = $DOM->getElementsByTagName('InvoiceTypeCode');
            $i=0;
            foreach($DocXML as $Nodo){
                $TipoDoc = $Nodo->nodeValue; 
                break;
            }    

            // Obteniendo datos ADQUIRIENTE.
            $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')->item(0)->getElementsByTagName('ID');
            $i=0;
            foreach($DocXML as $Nodo){
                $RUC = $Nodo->nodeValue; 
                $CodigoDoc = $Nodo->getAttribute("schemeID");
                break;
            }
            
            $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')->item(0)->getElementsByTagName('RegistrationName');
            $i=0;
            foreach($DocXML as $Nodo){
                $NombreCliente = $Nodo->nodeValue;
                break;
            }

            //Datos Ref
            if($tipo_documento=="C"){
                $DocXML = $DOM->getElementsByTagName('ReferenceID');
                foreach($DocXML as $Nodo){
                    $NroRef = $Nodo->nodeValue;
                }
            }  

            // Obteniendo datos de Forma de Pago.
            $formapago = "CONTADO";
            $fechaCuotas = [];
            $montoCuotas = [];
            $fechaultima = $FecEmi;
            if($tipo_documento == "F"){
                $DocXML = $DOM->getElementsByTagName('PaymentTerms');
                if($DocXML->length>0){
                    $FormaPagoXML = $DocXML->item(0)->getElementsByTagName('PaymentMeansID');
                    foreach($FormaPagoXML as $Nodo){
                        $formapago = $Nodo->nodeValue;
                        break;
                    }
                    if(intval($formapago)>0){
                        $FormaPagoXML = $DocXML->item(1)->getElementsByTagName('PaymentMeansID');
                        foreach($FormaPagoXML as $Nodo){
                            $formapago = $Nodo->nodeValue;
                            break;
                        }   
                    }
                    $j = 0;
                    foreach ($DocXML as $key => $Nodo1) {
                        $MontoCuotaXML = $Nodo1->getElementsByTagName('Amount');
                        $FechaCuotaXML = $Nodo1->getElementsByTagName('PaymentDueDate');
                        foreach($FechaCuotaXML as $Nodo){
                            $fechaCuotas[] = $Nodo->nodeValue;
                            $fechaultima = $Nodo->nodeValue;
                            break;
                        }
                        foreach($MontoCuotaXML as $Nodo){
                            if($j > 0){
                                $montoCuotas[] = $Nodo->nodeValue;
                                break;
                            }
                            $j++;
                        }
                    }
                }
            } 

            ### DATOS DEL PRODUCTO O SERVICIO. #########################################
            if($tipo_documento=="F" || $tipo_documento=="B"){
                $InvoiceLineXML = $DOM->getElementsByTagName('InvoiceLine');
            }elseif($tipo_documento=="C"){
                $InvoiceLineXML = $DOM->getElementsByTagName('CreditNoteLine');
            }elseif($tipo_documento=="D"){
                $InvoiceLineXML = $DOM->getElementsByTagName('DebitNoteLine');
            }else{
                throw new Exception("INDEFINIDO DOCUMENTO");
            }
            
            foreach($InvoiceLineXML as $Nodo1){
                
                // Código del producto o servicio.
                $DocXML = $Nodo1->getElementsByTagName('SellersItemIdentification');
                $i=0;
                foreach($DocXML as $Nodo){
                    $CodProdServ[] = $Nodo->nodeValue;
                }    

                // Descripción del producto o servicio.
                $DocXML = $Nodo1->getElementsByTagName('Description');
                $i=0;
                foreach($DocXML as $Nodo){
                    $ProdServ[] = $Nodo->nodeValue;
                }    

                // Cantidad de producto o servicio.
                if($tipo_documento=="F" || $tipo_documento=="B"){
                    $DocXML = $Nodo1->getElementsByTagName('InvoicedQuantity');
                }elseif($tipo_documento=="C"){
                    $DocXML = $Nodo1->getElementsByTagName('CreditedQuantity');
                }elseif($tipo_documento=="D"){
                    $DocXML = $Nodo1->getElementsByTagName('DebitedQuantity');
                }else{
                    throw new Exception("INDEFINIDO DOCUMENTO");
                }
                $i=0;
                foreach($DocXML as $Nodo){
                    $Cant[] = $Nodo->nodeValue;
                    $UniMed[] = $Nodo->getAttribute('unitCode');
                }    

                // Precio unitario. 
                $DocXML = $Nodo1->getElementsByTagName('PricingReference');
                $i=0;
                foreach($DocXML as $Nodo){
                    $Doc2 = $Nodo->getElementsByTagName('PriceAmount');
                    foreach($Doc2 as $Nodo2){
                        $Precio[] = $Nodo2->nodeValue;
                    } 
                }
                
                // Importe.
                $DocXML = $Nodo1->getElementsByTagName('LineExtensionAmount');
                $i=0;
                foreach($DocXML as $Nodo){
                    $Importe[] = $Nodo->nodeValue;
                }    

            }
            
            ### TOTALES DE LA FACTURA ##################################################

            // Total gravado.
            $DocXML = $DOM->getElementsByTagName('PayableAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                if ($i==1){
                    $TotGrav = $Nodo->nodeValue;
                }
                $i++;
            }    

            // Total IGV.
            $DocXML = $DOM->getElementsByTagName('TaxAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                $TotIGV = $Nodo->nodeValue; 
            }    

            // Monto total.
            $DocXML = $DOM->getElementsByTagName('PayableAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                $TotMonto = $Nodo->nodeValue; 
            }    


        // Crear el gráfico con el código de barras. ===================================
        $textoCodBar = $empresa["ruc_empresa"]."|".$TipoDoc."|".$Serie."|".$Numeracion."|".$TotIGV
                ."|".$TotMonto."|".$FecEmi."|".$CodigoDoc."|".$RUC."|".$CodHash;
        $qrCode = new QrCode();
        $qrCode
            ->setText($textoCodBar)
            ->setSize(75)
            ->setPadding(10)
            ->setErrorCorrection('QUARTILE')
            //->setErrorCorrection('HIGH')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($Serie."-".$Numeracion)
            ->setLabelFontSize(10)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $qrCode->save(__DIR__ ."/../ficheros/".$nombre_documento."png");
        
        if(count($objSolicitud)>0){
            $dataSolicitud = json_decode($objSolicitud["data_solicitud"],true);
        }else{
            $dataSolicitud = array();
        }
        
        $pdf = new Fpdf();
        $pdf->AddPage('P','A4');
        $pdf->SetTextColor(0);
        $borde = 0;
        $pdf->SetXY(130, 10);
        $pdf->SetFont('Arial','B',10);
        $alto = 8;
        $pdf->Cell(70, $alto, utf8_decode("RUC: ".$empresa["ruc_empresa"]), $borde, 0, "C");
        $pdf->SetXY(130, $pdf->GetY() + 8);
        $pdf->SetFont('Arial','B',10);
        //$pdf->SetFillColor(0,111,173);#293283
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $alto = 12;
        if($tipo_documento=="F"){
            $pdf->MultiCell(70,$alto,utf8_decode("Factura de Venta Electrónica"),$borde,'C',true);
        }elseif($tipo_documento=="B"){
            $pdf->MultiCell(70,$alto,utf8_decode("Boleta de Venta Electrónica"),$borde,'C',true);
        }elseif($tipo_documento=="C"){
            $pdf->MultiCell(70,$alto,utf8_decode("Nota de Crédito Electrónica"),$borde,'C',true);
        }elseif($tipo_documento=="D"){
            $pdf->MultiCell(70,$alto,utf8_decode("NOTA DE DEBITO ELECTRÓNICA"),$borde,'C',true);
        }else{
            throw new Exception("INDEFINIDO DOCUMENTO");
        }
        $pdf->SetTextColor(0);
        /*if(substr($Serie,1,3)=="001"){
            $pdf->Image("logo-chiclayo.jpg",15,1,104,40);
        }else{
            $pdf->Image("logo-lima.jpg",15,1,104,40);
        }*/
        $pdf->Image("logo.jpeg",15,8,60,30);
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(130, $pdf->GetY());
        $pdf->Cell(70, $alto, utf8_decode("Nro. ".$Serie."-".$Numeracion), $borde, 0, "C");
        //$pdf->Rect(140, 12, 58, 25,'F');
        $pdf->RoundedRect(130, 7, 70, 35,2,'1234');
        
        $pdf->SetXY(75,12);
        $pdf->SetFont('Arial','B',12);
        $alto = 4;
        $pdf->Cell(100, $alto, utf8_decode($empresa["nombre_empresa"]),$borde, 0, "L");
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(75, $pdf->GetY()+6);
        $pdf->SetFont('Arial','',8);
        $alto = 4;
        if(substr($Serie,1,3)=="002"){//CUZCO
            $pdf->MultiCell(40, $alto, utf8_decode($empresa["domiciliofiscal_empresa"]), $borde, "L");
            
            $pdf->SetXY(75, $pdf->GetY());
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(40, $alto, "Telf: 972558640", $borde, 0, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto);
            $pdf->SetFont('Arial','',8);
            $pdf->Multicell(50, $alto, "Email: ventas@ssymilabperu.com   ssymilabperu.com", $borde, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto+1);
        }else{
            $pdf->MultiCell(40, $alto, utf8_decode("CAL.MIGUEL GRAU NRO. 403 (FRENTE A COMERCIAL TIMO) ICA - CHINCHA - CHINCHA ALTA"), $borde, "L");
            
            $pdf->SetXY(75, $pdf->GetY());
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(40, $alto, "Telf: 972558640", $borde, 0, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto);
            $pdf->SetFont('Arial','',8);
            $pdf->Multicell(50, $alto, "Email: ventas@ssymilabperu.com   ssymilabperu.com", $borde, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto+1);
        }
        $pdf->SetXY(15, 47);
        $alto = 5;
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(35, $alto, utf8_decode("Fecha Emision:"), 'R', 0, "C");
        $pdf->Cell(30, $alto, utf8_decode("Forma de Pago:"), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode("Fecha de Vencimiento:"), 'R', 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("Moneda"), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode("Guia Remisión N°"), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode("Asesor"), $borde, 0, "C");
		$pdf->SetXY(15, $pdf->GetY()+$alto);

        $pdf->SetFont('Arial','',8);
        $pdf->Cell(35, $alto, utf8_decode(date_format(date_create($FecEmi." ".$hora), "d/m/Y H:i:s")),'R', 0, "C");
        $pdf->Cell(30, $alto, utf8_decode($formapago), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode(date_format(date_create($fechaultima), "d/m/Y")), 'R', 0, "C");
        $pdf->Cell(15, $alto, utf8_decode("SOLES"), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode($guia), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode($Vendedor), $borde, 0, "C");

        $pdf->RoundedRect(15, 47, 185, 10,2,'1234');
        
        $pdf->SetXY(15, $pdf->GetY()+$alto*2);
        
        $pdf->SetFont('Arial','B',9);
        if($tipo_documento=="F"){
            $pdf->Cell(27, $alto, utf8_decode("RUC"), 'B', 0, "L");
        }else{
            $pdf->Cell(27, $alto, utf8_decode("RUC/DNI"), 'B', 0, "L");
        }
        $pdf->Cell(3, $alto, utf8_decode(":"), 'B', 0, "L");
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(70, $alto, utf8_decode($RUC), 'B', 0, "L");
        if($Orden!=""){
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(15, $alto, utf8_decode("ORDEN"),'B', 0, "L");
            $pdf->Cell(3, $alto, utf8_decode(":"),'B', 0, "L");
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(67, $alto, utf8_decode($Orden), 'B', 0, "L");
        }else{
            if($tipo_documento=="C"){
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(15, $alto, utf8_decode("NRO REF"),'B', 0, "L");
                $pdf->Cell(3, $alto, utf8_decode(":"),'B', 0, "L");
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(67, $alto, utf8_decode($NroRef), 'B', 0, "L");
            }else{
                $pdf->Cell(85, $alto,"", 'B', 0, "L");
            }
        }
        
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',9);
        if($tipo_documento=="F"){
            $pdf->Cell(27, $alto, utf8_decode("Razón Social"), 'B', 0, "L");
        }else{
            $pdf->Cell(27, $alto, utf8_decode("Señor(es)"), 'B', 0, "L");
        }
        $pdf->Cell(3, $alto, utf8_decode(":"), 'B', 0, "L");
        $pdf->SetFont('Arial','',9);
        $alto1 = $pdf->GetMultiCellHeight(155, $alto, utf8_decode($NombreCliente), $borde, "L");
        $pdf->MultiCell(155, $alto, utf8_decode($NombreCliente), 'B', "L");
        
        $pdf->SetXY(15, $pdf->GetY());

        if(mb_strtoupper($formapago) == "CREDITO"){
            $bord = 'B';
        }else{
            $bord = $borde;
        }

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(27, $alto, utf8_decode("Dirección"), $bord, 0, "L");
        $pdf->Cell(3, $alto, utf8_decode(":"), $bord, 0, "L");
        $pdf->SetFont('Arial','',8);
        $alto2 = $pdf->GetMultiCellHeight(155, $alto, utf8_decode($objSolicitud["direccion_cliente"]), $borde, "L");
        $pdf->MultiCell(155, $alto, utf8_decode($objSolicitud["direccion_cliente"]), $bord, "L");

        $pdf->SetXY(15, $pdf->GetY());
        $extraLineas = 0;
        if($tipo_documento == "F" || $tipo_documento == "C"){
            
            if(mb_strtoupper($formapago) == "CREDITO"){
                // $extraLineas++;
                foreach ($fechaCuotas as $key => $value) {
                    $fechavencimientocuota = new DateTime($fechaCuotas[$key]);
                    $fechaemision = new DateTime($FecEmi."");
                    $int = $fechaemision->diff($fechavencimientocuota);
                    $dias = strtotime($fechaCuotas[$key]) - strtotime($FecEmi);
                    $years = floor($dias / (365*60*60*24));
                    $months = floor(($dias - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($dias - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                }
                //$pdf->Cell(28, $alto, mb_strtoupper(utf8_decode($formapago)), $borde, 0, "L");
                $suma =0;
                foreach ($fechaCuotas as $key => $value) {
                    $fechavencimientocuota = date_format(date_create($fechaCuotas[$key]), "d/m/Y");
                    $suma = $suma + $montoCuotas[$key];
                }
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(27, $alto, utf8_decode("Cantidad Cuotas"), $borde, 0, "L");
                $pdf->Cell(3, $alto, utf8_decode(":"), $borde, 0, "L");
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(40, $alto, utf8_decode(count($fechaCuotas)), $borde, 0, "L");

                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(45, $alto, utf8_decode("Monto Neto Pendiente de Pago :"), $borde, 0, "L");
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(28, $alto,  number_format($suma,2), $borde, 0, "C");
                $pdf->Cell(28, $alto, mb_strtoupper(utf8_decode($formapago)) . '(' .$int->days. ' dias)' , $borde, 0, "L");

                /*foreach ($fechaCuotas as $key => $value) {
                    $extraLineas++;
                    $pdf->SetXY(13, $pdf->GetY()+$alto);
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(30, $alto, utf8_decode("  Cuota N° " . ($key + 1)), $borde, 0, "L");
                    $pdf->SetFont('Arial','',9);
                    $pdf->Cell(100, $alto, utf8_decode("Fecha de Vencimiento: " . date_format(date_create($fechaCuotas[$key]), "d/m/Y") . "            Monto: " . number_format($montoCuotas[$key],2)), $borde, 0, "L");
                }*/
                $extraLineas++;

                $pdf->RoundedRect(15, 62, 185, 15 + $extraLineas*$alto,2,'1234');

                $pdf->SetXY(15, $pdf->GetY() + ($extraLineas-1)*$alto);

            }else{
                $pdf->RoundedRect(15, 62, 185, 15+$alto2-$alto,2,'1234');
                //$pdf->Cell(28, $alto, mb_strtoupper(utf8_decode($formapago)), $borde, 0, "L");
            }
            
        }else{
            $pdf->RoundedRect(15, 62, 185, 15,2,'1234');
        }
        $yinicial = $pdf->GetY();
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $yalto = $pdf->GetY();
        $alto = 5;
        //$pdf->SetFillColor(0,111,173);
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10, $alto, utf8_decode("ITEM"), 0, 0, "C",true);
        $pdf->Cell(15, $alto, utf8_decode("CÓDIGO"), 0, 0, "C",true);
        $pdf->Cell(80, $alto, utf8_decode("DESCRIPCIÓN"), 0, 0, "C",true);
        $pdf->Cell(17, $alto, utf8_decode("UNID."), 0, 0, "C",true);
        $pdf->Cell(15, $alto, utf8_decode("CANTIDAD"), 0, 0, "C",true);
        $pdf->Cell(25, $alto, utf8_decode("P. UNITARIO"), 0, 0, "C",true);
        //$pdf->Cell(15, $alto, utf8_decode("P.U."), 1, 0, "C");
        //$pdf->Cell(15, $alto, utf8_decode("Dscto."), 1, 0, "C");
        $pdf->Cell(23, $alto, utf8_decode("TOTAL"), 0, 0, "C",true);
        $pdf->SetFont('Arial','',8);
        $total = 0;
        $totalbruto = 0;
        $borde2 = 0;
        $pdf->Ln();
        $alto = 4;

        $pdf->SetTextColor(0);
        $detalles = $dataSolicitud["detalles"];
        foreach ($ProdServ as $key => $value) {
            $ProdServ[$key] = str_replace("LOTE", "\nLOTE", strtoupper($ProdServ[$key]));
            $alto2 = $pdf->GetMultiCellHeight(80, $alto, utf8_decode($ProdServ[$key]), $borde2, "L");
            $y = $pdf->GetY();
            $pdf->SetX(15);
            $pdf->Cell(10, $alto, utf8_decode($key+1), $borde2, 0, "C");
            $pdf->Cell(15, $alto, utf8_decode($CodProdServ[$key]), $borde2, 0, "C");
            //$pdf->Cell(10, $alto2, utf8_decode($UniMed[$key]), $borde2, 0, "C");
            $pdf->SetX(120);
            $pdf->Cell(17, $alto, utf8_decode($detalles[$key]["nombreunidadmedida"]), $borde2, 0, "C");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Cant[$key],0)), $borde2, 0, "C");
            //$Precio[$key] = ($Importe[$key]/$Cant[$key])*1.18;
            //$pdf->Cell(23, $alto2, utf8_decode(number_format($Precio[$key]/1.18,2,'.','')), $borde2, 0, "R");
            $pdf->Cell(25, $alto, "S/ ".utf8_decode(number_format($Precio[$key],2,'.','')), $borde2, 0, "C");
            //$pdf->Cell(15, $alto2, utf8_decode("0.00"), $borde2, 0, "R");
            //$pdf->Cell(25, $alto2, utf8_decode(number_format($Precio[$key]*$Cant[$key]/1.18,2)), $borde2, 0, "R");
            //DESCUENTO POR ITEM (% SOBRE EL VALOR UNITARIO)
            $descuentoxitem = (isset($detalles[$key]["descuentoxitem"]) && $detalles[$key]["descuentoxitem"] > 0) ? $detalles[$key]["descuentoxitem"] : 0;
            $pdf->Cell(23, $alto, "S/ ".utf8_decode(number_format($detalles[$key]["precioventaunitarioxitem"]*$Cant[$key]*(1 - $descuentoxitem/100),2)), $borde2, 0, "R");
            //$pdf->Cell(22, $alto2, utf8_decode(number_format($Importe[$key],2)), $borde2, 0, "R");
            $totalbruto = $totalbruto + $detalles[$key]["precioventaunitarioxitem"]*$Cant[$key]/1.18;
            $total = $total + $detalles[$key]["precioventaunitarioxitem"]*$Cant[$key]/1.18*(1 - $descuentoxitem/100);
            //$total = $total + $Importe[$key];
            $pdf->SetX(40);
            $pdf->MultiCell(80, $alto, utf8_decode($ProdServ[$key]), $borde2, "L");
        }

        if($extraLineas==0){
            $pdf->RoundedRect(15, $yalto, 185, $y - $yinicial+$alto2+$alto-3,3,'4');
        }else{
            $pdf->RoundedRect(15, $yalto, 185, $y - $yinicial+$alto2+$alto-3,3,'4');
        }

        $y = $pdf->GetY();
        $pdf->SetXY(15, $y+$alto+2);
        $pdf->MultiCell(110,$alto,utf8_decode($objSolicitud["observaciones"]),0,'L');
        $y2 = $pdf->GetY();
        $pdf->SetXY(135, $y+$alto+2);
        $alto = 5;
        //DESCUENTO TOTAL: MOTIVO 02 = MONTO EN SOLES CON IGV; SIN MOTIVO (00) = %. DESCUENTOS SE MUESTRAN SIN IGV
        if(isset($dataSolicitud["descuentototal"]) && $dataSolicitud["descuentototal"] > 0){
            if(isset($dataSolicitud["motivodescuento"]) && $dataSolicitud["motivodescuento"] == "02"){
                $total = $total - $dataSolicitud["descuentototal"]/1.18;
            }else{
                $total = $total * (1 - $dataSolicitud["descuentototal"]/100);
            }
        }
        if(round($totalbruto - $total, 2) > 0){
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(30, $alto, utf8_decode("Descuento:"), 'LTB', 0, "L");
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10, $alto, utf8_decode("S/"), "RTB", 0, "L");
            $pdf->Cell(25, $alto, utf8_decode(number_format($totalbruto - $total,2)), 1, 0, "R");
            $pdf->Ln();
            $pdf->SetX(135);
        }
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30, $alto, utf8_decode("Op. Gravada:"), 'LTB', 0, "L");
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10, $alto, utf8_decode("S/"), "RTB", 0, "L");
        $pdf->Cell(25, $alto, utf8_decode(number_format($total,2)), 1, 0, "R");
        $pdf->Ln();
        $pdf->SetX(135);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30, $alto, utf8_decode("I.G.V.(18%):"), 'LTB', 0, "L");
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10, $alto, utf8_decode("S/"), "RTB", 0, "L");
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*0.18,2)), 1, 0, "R");
        $pdf->Ln();
        /*$pdf->SetX(133);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30, $alto, utf8_decode("Op. Inafecta:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(10, $alto, utf8_decode("S/"), $borde, 0, "L");
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(133);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30, $alto, utf8_decode("Op. Exonerada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(10, $alto, utf8_decode("S/"), $borde, 0, "L");
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();*/
        $pdf->SetX(135);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(30, $alto, utf8_decode("Importe Total"), "LTB", 0, "L");
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10, $alto, utf8_decode("S/"), "RTB", 0, "L");
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*1.18,2)), 1, 0, "R");
        $pdf->Ln();
        if(intval($dataSolicitud["detraccion"])>0){
            $listaDetracciones = array(
                            '012' => '012 - INTERMEDIACION LABORAL Y TERCIARIZACION',
                            '019' => '019 - ARRENDAMIENTO DE BIENES',
                            '020' => '020 - MANTENIMIENTO Y REPARACION DE BIENES MUEBLES',
                            '021' => '021 - MOVIMIENTO DE CARGA',
                            '022' => '022 - OTROS SERVICIOS EMPRESARIALES',
                            '024' => '024 - COMISION MERCANTIL',
                            '025' => '025 - FABRICACION DE BIENES POR ENCARGO',
                            '026' => '026 - SERVICIO DE TRANSPORTE DE PERSONAS ',
                            '030' => '030 - CONTRATOS DE CONSTRUCCION',
                            '037' => '037 - DEMAS SERVICIOS GRAVADOS CON EL IGV' ,
                            );
            $yDetraccion = $pdf->GetY();
            $pdf->SetX(135);
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(30, $alto, utf8_decode("Detracción (".$dataSolicitud["detraccion"]."%)"), 'LTB', 0, "L");
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10, $alto, utf8_decode("S/"), "RTB", 0, "L");
            $pdf->Cell(25, $alto, utf8_decode(number_format($total*1.18*$dataSolicitud["detraccion"]/100,2)), 1, 0, "R");
            $pdf->Ln();
            $pdf->SetX(15);
            $pdf->MultiCell(180,$alto,utf8_decode("Este comprobante está sujeto a detracción (".$listaDetracciones[$dataSolicitud["codbiendetraccion"]]."), Nro. Cuenta Banco de la Nación: ".$dataSolicitud["cuentadetraccion"].". Nro CCI: 01816100016156749302"),0,'L');
            $pdf->SetY($yDetraccion);
        }
        include_once '../modelo/NumeroTexto.php';
        $importe_total_venta = number_format($total*1.18,2,'.','');
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
        $son = strtoupper("SON: ".$numeroTexto->convertirLetras($importe_total_venta)).' CON '.$decimales.'/100 SOLES';
        
        $pdf->SetFont('Arial','B',8);
        $x=$pdf->GetX();
        $y=$pdf->GetY();
        $pdf->SetXY(15,$y-21);
        $pdf->Multicell(185, $alto, $son, 'T', "L");
        $pdf->SetXY(15,$y+$alto);
        if($y2>$pdf->GetY()){
            $pdf->SetXY(15, $y2+$alto+2);
        }
        if(intval($dataSolicitud["detraccion"])>0){
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetX(15);
        }
        $borde=1;
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(185,$alto,"DEPOSITAR A NOMBRE DE SSYMILAB E.I.R.L",1,0,'L');
        $pdf->Ln();
        $alto=5;
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(5, $alto, "", 0, 0, "L");
        $pdf->Cell(45, $alto, "BANCO: BBVA", "LT", 0, "L");
        $pdf->Cell(45, $alto, "BANCO: BBVA", "LT", 0, "L");
        $pdf->Cell(45, $alto, "BANCO: BCP", "LT", 0, "L");
        $pdf->Cell(50, $alto, "BANCO: YAPE", "RLT", 0, "L");
        $pdf->Ln();
        $pdf->SetFont('helvetica','',7);
        $pdf->Cell(5, $alto, "", 0, 0, "L");
        $pdf->Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf->Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf->Cell(45, $alto, "TITULAR: SSYMILAB E.I.R.L.", "L", 0, "L");
        $pdf->Cell(50, $alto, "TITULAR: SSYMILAB E.I.R.L.", "RL", 0, "L");
        $pdf->Ln();
        $pdf->SetFont('helvetica','',7);
        $pdf->Cell(5, $alto, "", 0, 0, "L");
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto/1.5, "NRO CUENTA (SOLES): 0011-0200-0100154444", "L",  "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto/1.5, "NRO CUENTA (DOLARES): 0011-0200-0100157559", "L", "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto/1.5, "NRO CUENTA (SOLES): 285-9877952-0-77", "L", "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(50, $alto, "972558640", "RL", "L");
        $pdf->SetXY($x+45,$y);

        $pdf->Ln($alto+1);
        $pdf->SetFont('helvetica','',7);
        $pdf->Cell(5, $alto, "", 0, 0, "L");
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto, "CCI: 011-200-000100154444-32", "LB", "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto, "CCI: 011-200-000100157559-34", "LB", "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(45, $alto, "CCI: 002-285-009877952077-58", "LB", "L");
        $pdf->SetXY($x+45,$y);
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->MultiCell(50, $alto, "                               ", "LRB", "L");
        $pdf->Ln();
        $pdf->Ln();
        /*$pdf->Cell(2, $alto, "", 0, 0, "L");
        $pdf->Cell(50, $alto, "CTA CAJA PIURA SOLES", $borde, 0, "L");
        $pdf->Cell(40, $alto, "210-01-3211199", $borde, 0, "L");
        $pdf->Ln();*/
        $pdf->Image(__DIR__ ."/../ficheros/".$nombre_documento."png",175,$pdf->GetY());

        $pdf->Output(__DIR__ ."/../ficheros/".$nombre_documento."pdf", 'F'); // Se graba el documento .PDF en el disco duro o unidad de estado sólido.
        //chmod($NomArchPDF,0777);  // Se dan permisos de lectura y escritura.

        $pdf->Output($nombre_documento."pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
        $pdf->Output();
        exit;
    }
    
    public static function GenerarPDFTicket($nombre_documento,$filename_zip,$empresa,$tipo_documento){
        //$filenameR_zip = __DIR__ ."/../ficheros/S".$id_solicitud."/R-".$nombre_documento."zip";
        /*$pdf = new Anouar\Fpdf\Fpdf();
        $pdf->AddPage('L','A4');
        $pdf->Cell(69,5,utf8_decode("MUNICIPALIDAD PROVINCIAL DE SATIPO"),1,0,'C');
        $pdf->Output('masiva.pdf','D');
        */
        // Variables correspondientes a la factura.
        $RUC = "";       // RUC DE ADQUIRIENTE
        $NombreCliente = "";
        $CodigoDoc = ""; // CODIGO DE ADQUIRIENTE
        $Serie = "";     // SERIE DEL DOCUMENTO
        $Numeracion = "";// NUMERACION DEL DOC
        $NomRazSoc = ""; // Nombre o Razón social.
        $FecEmi = "";    // Fecha de emisión.
        $Moneda = "";
        $Domicilio = ""; // Domicilio.
        $CodHash = "";   // Código Hash.
        $TipoDoc = "";   // Tipo de documento.
        $TotGrav = 0;    // Total gravado.
        $TotIGV = 0;     // Total IGV.
        $TotMonto = 0;   // Total importe. 
        // Variables correspondientes a los productos o servicios de la factura.
        $CodProdServ = array(); // Código.
        $ProdServ = array(); // Descripción.
        $Cant = array(); // Cantidad. 
        $UniMed = array(); // Unidad de medida.
        $Precio = array(); // Precio unitario.
        $Importe = array();  // Importe.
        // Obteniendo datos del archivo .XML (factura electrónica)======================
        if(!file_exists($filename_zip)){
            throw new Exception("ERROR EN LA DESCOMPRESION DEL ARCHIVO");
        }
        $xml = file_get_contents($filename_zip);
        #== Obteniendo datos del archivo .XML 
            $DOM = new DOMDocument('1.0', 'ISO-8859-1');
            $DOM->preserveWhiteSpace = FALSE;
            $DOM->loadXML($xml);
            ### DATOS DE LA FACTURA ####################################################

            //Obteniendo el Numero del documento
            $DocXML = $DOM->getElementsByTagName('ID');
            foreach($DocXML as $Nodo){
                $Serie = $Nodo->nodeValue;
                break;
            }
            $Serie = explode("-", $Serie);
            $Numeracion = $Serie[1];
            $Serie = $Serie[0];
            
            // Obteniendo Fecha de emisión.
            $DocXML = $DOM->getElementsByTagName('IssueDate');
            foreach($DocXML as $Nodo){
                $FecEmi = $Nodo->nodeValue;
                break;
            }
            
            // Obteniend
            $DocXML = $DOM->getElementsByTagName('DocumentCurrencyCode');
            foreach($DocXML as $Nodo){
                $Moneda = $Nodo->nodeValue;
                break;
            }
            
            // Obteniendo Codigo Hash.
            $DocXML = $DOM->getElementsByTagName('DigestValue');
            foreach($DocXML as $Nodo){
                $CodHash = $Nodo->nodeValue;
                break;
            }    

            // Clave del tipo de documento.
            $DocXML = $DOM->getElementsByTagName('InvoiceTypeCode');
            $i=0;
            foreach($DocXML as $Nodo){
                $TipoDoc = $Nodo->nodeValue; 
                break;
            }    

            // Obteniendo datos ADQUIRIENTE.
            $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')->item(0)->getElementsByTagName('ID');
            $i=0;
            foreach($DocXML as $Nodo){
                $RUC = $Nodo->nodeValue; 
                $CodigoDoc = $Nodo->getAttribute("schemeID");
                break;
            }
            
            $DocXML = $DOM->getElementsByTagName('AccountingCustomerParty')->item(0)->getElementsByTagName('RegistrationName');
            $i=0;
            foreach($DocXML as $Nodo){
                $NombreCliente = $Nodo->nodeValue;
                break;
            }
                        // Obteniendo datos de Forma de Pago.
            $formapago = "CONTADO";
            $fechaCuotas = [];
            $montoCuotas = [];
            if($tipo_documento == "F" && ($FecEmi>=strtotime("17-09-2008 00:00:00"))){
                $DocXML = $DOM->getElementsByTagName('PaymentTerms');
                if($DocXML){
                    $FormaPagoXML = $DocXML->item(0)->getElementsByTagName('PaymentMeansID');
                    foreach($FormaPagoXML as $Nodo){
                        $formapago = $Nodo->nodeValue;
                        break;
                    }
                    $j = 0;
                    foreach ($DocXML as $key => $Nodo1) {
                        $MontoCuotaXML = $Nodo1->getElementsByTagName('Amount');
                        $FechaCuotaXML = $Nodo1->getElementsByTagName('PaymentDueDate');
                        foreach($FechaCuotaXML as $Nodo){
                            $fechaCuotas[] = $Nodo->nodeValue;
                            break;
                        }
                        foreach($MontoCuotaXML as $Nodo){
                            if($j > 0){
                                $montoCuotas[] = $Nodo->nodeValue;
                                break;
                            }
                            $j++;
                        }
                    }
                }
            } 

            ### DATOS DEL PRODUCTO O SERVICIO. #########################################
            if($tipo_documento=="F" || $tipo_documento=="B"){
                $InvoiceLineXML = $DOM->getElementsByTagName('InvoiceLine');
            }elseif($tipo_documento=="C"){
                $InvoiceLineXML = $DOM->getElementsByTagName('CreditNoteLine');
            }elseif($tipo_documento=="D"){
                $InvoiceLineXML = $DOM->getElementsByTagName('DebitNoteLine');
            }else{
                throw new Exception("INDEFINIDO DOCUMENTO");
            }
            
            foreach($InvoiceLineXML as $Nodo1){
                
                // Código del producto o servicio.
                $DocXML = $Nodo1->getElementsByTagName('PriceTypeCode');
                $i=0;
                foreach($DocXML as $Nodo){
                    $CodProdServ[] = $Nodo->nodeValue;
                }    

                // Descripción del producto o servicio.
                $DocXML = $Nodo1->getElementsByTagName('Description');
                $i=0;
                foreach($DocXML as $Nodo){
                    $ProdServ[] = $Nodo->nodeValue;
                }    

                // Cantidad de producto o servicio.
                if($tipo_documento=="F" || $tipo_documento=="B"){
                    $DocXML = $Nodo1->getElementsByTagName('InvoicedQuantity');
                }elseif($tipo_documento=="C"){
                    $DocXML = $Nodo1->getElementsByTagName('CreditedQuantity');
                }elseif($tipo_documento=="D"){
                    $DocXML = $Nodo1->getElementsByTagName('DebitedQuantity');
                }else{
                    throw new Exception("INDEFINIDO DOCUMENTO");
                }
                $i=0;
                foreach($DocXML as $Nodo){
                    $Cant[] = $Nodo->nodeValue;
                    $UniMed[] = $Nodo->getAttribute('unitCode');
                }    

                // Precio unitario. 
                $DocXML = $Nodo1->getElementsByTagName('PricingReference');
                $i=0;
                foreach($DocXML as $Nodo){
                    $Doc2 = $Nodo->getElementsByTagName('PriceAmount');
                    foreach($Doc2 as $Nodo2){
                        $Precio[] = $Nodo2->nodeValue;
                    } 
                }
                
                // Importe.
                $DocXML = $Nodo1->getElementsByTagName('LineExtensionAmount');
                $i=0;
                foreach($DocXML as $Nodo){
                    $Importe[] = $Nodo->nodeValue;
                }    

            }
            
            ### TOTALES DE LA FACTURA ##################################################

            // Total gravado.
            $DocXML = $DOM->getElementsByTagName('PayableAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                if ($i==1){
                    $TotGrav = $Nodo->nodeValue;
                }
                $i++;
            }    

            // Total IGV.
            $DocXML = $DOM->getElementsByTagName('TaxAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                $TotIGV = $Nodo->nodeValue; 
            }    

            // Monto total.
            $DocXML = $DOM->getElementsByTagName('PayableAmount');
            $i=0;
            foreach($DocXML as $Nodo){
                $TotMonto = $Nodo->nodeValue; 
            }    

      
        // Crear el gráfico con el código de barras. ===================================
        $textoCodBar = $empresa["ruc_empresa"]."|".$TipoDoc."|".$Serie."|".$Numeracion."|".$TotIGV
                ."|".$TotMonto."|".$FecEmi."|".$CodigoDoc."|".$RUC."|".$CodHash;
        $qrCode = new QrCode();
        $qrCode
            ->setText($textoCodBar)
            ->setSize(75)
            ->setPadding(10)
            ->setErrorCorrection('QUARTILE')
            //->setErrorCorrection('HIGH')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabel($Serie."-".$Numeracion)
            ->setLabelFontSize(10)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;
        $qrCode->save(__DIR__ ."/../ficheros/".$nombre_documento."png");
        
        
        $pdf = new Fpdf($orientation='P',$unit='mm', array(80,400));
        $pdf->AddPage();
        //$pdf->AddPage('P','A4');
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','B',10);
        $borde = 0;
        $pdf->SetXY(10, 8);
        $alto = 5;
        if($tipo_documento=="F"){
            $pdf->MultiCell(60,$alto,utf8_decode("FACTURA ELECTRÓNICA"),$borde,'C');
        }elseif($tipo_documento=="B"){
            $pdf->MultiCell(60,$alto,utf8_decode("BOLETA ELECTRÓNICA"),$borde,'C');
        }elseif($tipo_documento=="C"){
            $pdf->MultiCell(60,$alto,utf8_decode("NOTA DE CREDITO ELECTRÓNICA"),$borde,'C');
        }elseif($tipo_documento=="D"){
            $pdf->MultiCell(60,$alto,utf8_decode("NOTA DE DEBITO ELECTRÓNICA"),$borde,'C');
        }else{
            throw new Exception("INDEFINIDO DOCUMENTO");
        }
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial','',8);
        $alto = 6;
        $pdf->Cell(60, $alto, utf8_decode("RUC: ".$empresa["ruc_empresa"]), $borde, 0, "C");
        $pdf->SetXY(10, $pdf->GetY() + $alto);
        $pdf->Cell(60, $alto, utf8_decode($Serie."-".$Numeracion), $borde, 0, "C");
        //$pdf->Rect(10, 8, 60, 21);
        $pdf->Line(5, $pdf->GetY()+$alto+1, 80-5, $pdf->GetY()+$alto+1);
        
        $pdf->SetXY(6, $pdf->GetY()+$alto+4);
        $pdf->SetFont('Arial','B',10);
        $alto = 5;
        $pdf->MultiCell(70, $alto, utf8_decode($empresa["nombre_empresa"]), $borde,  "L");
        $pdf->SetXY(6, $pdf->GetY());
        $pdf->SetFont('Arial','',8);
        $alto = 5;
        $pdf->MultiCell(180, $alto, utf8_decode($empresa["domiciliofiscal_empresa"]), $borde, "L");
        
        $pdf->SetXY(6, $pdf->GetY()+$alto-2);
        $alto = 5;
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30, $alto, utf8_decode("Fecha Emision:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(130, $alto, utf8_decode(date_format(date_create($FecEmi), "d/m/Y")), $borde, 0, "L");
        $pdf->SetXY(6, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30, $alto, utf8_decode("Señor(es):"), $borde,0,  "L");
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell(40, $alto, utf8_decode($NombreCliente), $borde,  "L");
        $pdf->SetXY(6, $pdf->GetY());
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30, $alto, utf8_decode("RUC:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(130, $alto, utf8_decode($RUC), $borde, 0, "L");
        $pdf->SetXY(6, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30, $alto, utf8_decode("Moneda:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(130, $alto, utf8_decode($Moneda), $borde, 0, "L");
        //$pdf->Rect(12, $pdf->GetY()-4*$alto+3, 186, 5*$alto - 1);
        $pdf->Line(5, $pdf->GetY()+$alto+1, 80-5, $pdf->GetY()+$alto+1);

        $pdf->SetXY(6, $pdf->GetY()+2*$alto+1);
        $alto = 5;
        $pdf->SetFont('Arial','B',7);
        
        $pdf->Cell(20, $alto, utf8_decode("Descripción"), 0, 0, "C");
        $pdf->Cell(5, $alto, utf8_decode("UM"), 0, 0, "C");
        $pdf->Cell(9, $alto, utf8_decode("Cant"), 0, 0, "C");
        $pdf->Cell(9, $alto, utf8_decode("V.U."), 0, 0, "C");
        $pdf->Cell(9, $alto, utf8_decode("P.U."), 0, 0, "C");
        $pdf->Cell(9, $alto, utf8_decode("Dsct."), 0, 0, "C");
        $pdf->Cell(11, $alto, utf8_decode("V. V."), 0, 0, "C");
        $pdf->SetFont('Arial','',7);
        $total = 0;
        $borde2 = 0;
        $pdf->Ln();
        foreach ($ProdServ as $key => $value) {
            $pdf->SetFont('Arial','',7);
            $alto2 = $pdf->GetMultiCellHeight(85, $alto-1, utf8_decode($ProdServ[$key]), $borde2, "L");
            $pdf->SetX(6);
            $y = $pdf->GetY();
            
            $pdf->MultiCell(70, $alto, utf8_decode($ProdServ[$key]), $borde2, "L");
            $pdf->SetX(25);
            //$pdf->SetY($y);
            $pdf->SetFont('Arial','',7);
            $pdf->Cell(4, $alto2, utf8_decode($UniMed[$key]), $borde2, 0, "C");
            $pdf->Cell(9, $alto2, utf8_decode(number_format($Cant[$key],2)), $borde2, 0, "R");
            $pdf->Cell(9, $alto2, utf8_decode(number_format($Precio[$key]/1.18,2,'.','')), $borde2, 0, "R");
            $pdf->Cell(9, $alto2, utf8_decode(number_format($Precio[$key],2,'.','')), $borde2, 0, "R");
            $pdf->Cell(9, $alto2, utf8_decode("0.00"), $borde2, 0, "R");
            $pdf->Cell(10, $alto2, utf8_decode(number_format($Precio[$key]*$Cant[$key]/1.18,2)), $borde2, 0, "R");
            $total = $total + $Precio[$key]*$Cant[$key]/1.18;
            $pdf->SetX(6);
            $pdf->SetFont('Arial','',6);
            $pdf->Ln();


            
            /*$pdf->SetXY(15, $pdf->GetY()+$alto);
            $pdf->Cell(10, $alto, utf8_decode($key+1), 1, 0, "C");
            $pdf->Cell(75, $alto, utf8_decode($ProdServ[$key]), 1, 0, "L");
            $pdf->Cell(10, $alto, utf8_decode($UniMed[$key]), 1, 0, "C");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Cant[$key],2)), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key]/1.18,2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode(number_format($Precio[$key],2,'.','')), 1, 0, "R");
            $pdf->Cell(15, $alto, utf8_decode("0.00"), 1, 0, "R");
            $pdf->Cell(25, $alto, utf8_decode(number_format($Precio[$key]*$Cant[$key]/1.18,2)), 1, 0, "R");
            $total = $total + $Precio[$key]*$Cant[$key]/1.18;*/
        }
        

        $y = $pdf->GetY();
        $pdf->SetXY(25, $pdf->GetY()+$alto);
        $alto = 5;
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("Op. Gravada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(25);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("I.G.V.:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*0.18,2)), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(25);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("Op. Inafecta:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(25);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("Op. Exonerada:"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25, $alto, utf8_decode("0.00"), $borde, 0, "R");
        $pdf->Ln();
        $pdf->SetX(25);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("Importe Total"), $borde, 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25, $alto, utf8_decode(number_format($total*1.18,2)), $borde, 0, "R");
        
        $pdf->Ln();
        $pdf->Line(5, $pdf->GetY()+2, 80-5, $pdf->GetY()+2);
        include_once '../modelo/NumeroTexto.php';
        $importe_total_venta = number_format($total*1.18,2,'.','');
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
        $son = strtoupper("SON: ".$numeroTexto->convertirLetras($importe_total_venta)).' CON '.$decimales.'/100 SOLES';
        
        $pdf->SetFont('Arial','B',8);
        $pdf->setXY(6,$pdf->getY()+$alto);

        $pdf->MultiCell(70, $alto, $son, $borde, "L");
        $pdf->Ln();
        $pdf->SetFont('Arial','',8);
        $pdf->setX(6);
        $pdf->MultiCell(70, $alto-2, utf8_decode("Representación impresa de la Factura Electrónica, consulte en https://facturae-garzasoft.com"), $borde,  "L");
        $pdf->Ln();
        $pdf->Image(__DIR__ ."/../ficheros/".$nombre_documento."png",28,$pdf->getY());

        //$pdf->Output(__DIR__ ."/../ficheros/".$nombre_documento."pdf", 'F',false); // Se graba el documento .PDF en el disco duro o unidad de estado sólido.
        //chmod($NomArchPDF,0777);  // Se dan permisos de lectura y escritura.

        $pdf->Output($nombre_documento."pdf", 'I',false); // Se muestra el documento .PDF en el navegador.    */
        $pdf->Output();
        exit;
    }

    public static function GenerarPDFGuia($nombre_documento,$filename_zip,$empresa,$tipo_documento,$objSolicitud = array(), $email=false,$ubigeoinicio='',$ubigeofin=''){
        ini_set('memory_limit', '200M');
        $dataSolicitud = json_decode($objSolicitud["data_solicitud"],true);
        $mdlDistrito = new mdlDistrito();
        $mdlUnidad = new mdlUnidad();

        $arrModalidad = [
            "01" => "TRANSPORTE PÚBLICO",
            "02" => "TRANSPORTE PRIVADO"
        ];

        $arrMotivo = [
            "01" => "VENTA",
            "02" => "COMPRA",
            "04" => "TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA",
            "08" => "IMPORTACIÓN",
            "09" => "EXPORTACIÓN",
            "13" => "OTROS",
            "14" => "VENTA SUJETA A CONFIRMACIÓN DEL COMPRADOR",
            "18" => "TRASLADO EMISOR ITINERANTE CP",
            "19" => "TRASLADO A ZONA PRIMARIA",
        ];

        $Numeracion = "T" . $dataSolicitud["numeroguia"];
        $FecEmi = $dataSolicitud["fechaemision"] . " " . $dataSolicitud["horaemision"];   
        $FecTraslado = $dataSolicitud["fechatraslado"];   
        $DestinatarioNombre = $dataSolicitud["usuario"];
        $DestinatarioDoc = $dataSolicitud["doc"];
        $DestinatarioDireccion = $dataSolicitud["direccion"] != "" ? $dataSolicitud["direccion"] : "-";
        // $Modalidad = $dataSolicitud["modalidad"] . " - " .$arrModalidad[$dataSolicitud["modalidad"]];
        $Modalidad = $arrModalidad[$dataSolicitud["modalidad"]];
        $Transbordo = ($dataSolicitud["transbordo"] == "true" || $dataSolicitud["transbordo"] == "SI") ? "SÍ" : "NO";
        $Motivo = ($dataSolicitud["tipomotivo"] == "13" ? $dataSolicitud["motivo"] : $arrMotivo[$dataSolicitud["tipomotivo"]]);
        $Paquetes = $dataSolicitud["paquetes"] == "0" ? "-" : $dataSolicitud["paquetes"];
        $TransporteNombre = $dataSolicitud["transporte"]["nombre"]." ".$dataSolicitud["transporte"]["apellido"];
        $TransporteDoc = $dataSolicitud["transporte"]["doc"];
        $TransporteTipoDoc = $dataSolicitud["transporte"]["tipodoc"]=="6"?"RUC":($dataSolicitud["transporte"]["tipodoc"]=="4"?"C.E.":"DNI");
        $TransportePlaca = $dataSolicitud["transporte"]["placa"];
        //$UbigeoInicio = $mdlDistrito->verDistrinto($dataSolicitud["ubigeoini"])["nombre"];
        $UbigeoInicio = $ubigeoinicio;
        $DireccionInicio = $dataSolicitud["direccini"];
        //$UbigeoFin = $mdlDistrito->verDistrinto($dataSolicitud["ubigeofin"])["nombre"];;
        $UbigeoFin = $ubigeofin;
        $DireccionFin = $dataSolicitud["direccfin"];
        $Detalles = $dataSolicitud["detalles"];
        //OBSERVACIONES ACTUALES DE LA GUIA (CAMPO OBSERVACION DEL FORMULARIO); SI NO HAY, LAS ENVIADAS AL FACTURADOR
        if(isset($objSolicitud["observaciones"]) && trim($objSolicitud["observaciones"]) != ""){
            $Observaciones = $objSolicitud["observaciones"];
        }else{
            $Observaciones = isset($dataSolicitud["observaciones"]) ? $dataSolicitud["observaciones"] : "";
        }
        $OrdenCompra = $dataSolicitud["ordencompra"];
        $NumeroFactura = $dataSolicitud["numerofactura"];
        $PesoTotal = $dataSolicitud["pesototal"];
        
        $pdf = new Fpdf();
        $pdf->AddPage('P','A4');
        $borde = 0;
        $pdf->SetXY(120, 10);
        $pdf->SetFont('Arial','B',10);
        $alto = 8;
        $pdf->Cell(70, $alto, utf8_decode("RUC: ".$empresa["ruc_empresa"]), $borde, 0, "C");
        $pdf->SetXY(120, $pdf->GetY() + 8);
        $pdf->SetFont('Arial','B',10);
        //$pdf->SetTextColor(75,119,227);
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $alto = 12;
        $pdf->MultiCell(70,$alto,utf8_decode("GUIA DE REMISIÓN"),$borde,'C',true);
        $pdf->SetTextColor(0);
        /*if(substr($Numeracion,1,3)=="001"){
            $pdf->Image("logo-chiclayo.jpg",15,4,104,40);
        }else{
            $pdf->Image("logo-lima.jpg",15,4,104,40);
        }*/
        $pdf->Image("logo.jpeg",15,8,60,30);

        $pdf->SetXY(75,12);
        $pdf->SetFont('Arial','B',12);
        $alto = 4;
        $pdf->Cell(100, $alto, utf8_decode($empresa["nombre_empresa"]),$borde, 0, "L");
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(75, $pdf->GetY()+6);
        $pdf->SetFont('Arial','',8);
        $alto = 4;
        if(substr($Numeracion,1,3)=="002"){//CHICLAYO
            $pdf->MultiCell(40, $alto, utf8_decode("AV. INFANCIA NRO. 538 CUSCO - CUSCO - WANCHAQ"), $borde, "L");
            
            $pdf->SetXY(75, $pdf->GetY());
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(40, $alto, "Telf: 972558640", $borde, 0, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto);
            $pdf->SetFont('Arial','',8);
            $pdf->Multicell(50, $alto, "Email: ventas@ssymilabperu.com   ssymilabperu.com", $borde, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto+1);
        }else{
            $pdf->MultiCell(40, $alto, utf8_decode("CAL.MIGUEL GRAU NRO. 403 (FRENTE A COMERCIAL TIMO) ICA - CHINCHA - CHINCHA ALTA"), $borde, "L");
            $pdf->SetXY(75, $pdf->GetY());
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(40, $alto, "Telf: 972558640", $borde, 0, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto);
            $pdf->SetFont('Arial','',8);
            $pdf->Multicell(50, $alto, "Email: ventas@ssymilabperu.com   ssymilabperu.com", $borde, "L");
            $pdf->SetXY(75, $pdf->GetY()+$alto+1);
        }
        $pdf->SetXY(15, 35);

        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(120, $pdf->GetY());
        $pdf->Cell(70, $alto, utf8_decode("Nro. ".$Numeracion), $borde, 0, "C");
        //$pdf->Rect(140, 12, 58, 25,'F');
        $pdf->RoundedRect(120, 7, 70, 35,2,'1234');
        
        $pdf->SetXY(15, 48);
        /*$pdf->SetFont('Arial','B',11);
        $alto = 4;
        $pdf->Cell(100, $alto, utf8_decode($empresa["nombre_empresa"]),$borde, 0, "C");
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(25, $pdf->GetY()+6);
        $pdf->SetFont('Arial','',8);
        $alto = 4;
        $pdf->MultiCell(80, $alto, utf8_decode($empresa["domiciliofiscal_empresa"]), $borde, "C");
        
        $pdf->SetXY(15, $pdf->GetY());
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(100, $alto, "Telf: 074 225124", $borde, 0, "C");
        $pdf->SetXY(25, $pdf->GetY()+$alto);
        $pdf->SetFont('Arial','',8);
        $pdf->Multicell(80, $alto, "Email: ventas@unimedilab.com.pe - ventasunimedilab@hotmail.com", $borde, "C");
        $pdf->SetXY(15, $pdf->GetY()+$alto+1);*/

        $alto = 5;
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(32, $alto, utf8_decode("Fecha Emision:"), 'R', 0, "C");
        $pdf->Cell(30, $alto, utf8_decode("Fecha de Traslado:"), 'R', 0, "C");
        $pdf->Cell(32, $alto, utf8_decode("Docs. Referencia:"), 'R', 0, "C");
        $pdf->Cell(55, $alto, utf8_decode("Motivo de Traslado"), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode("Mod. Transporte"), 0, 0, "C");
        $pdf->SetXY(15, $pdf->GetY()+$alto);

        $pdf->SetFont('Arial','',8);
        $pdf->Cell(32, $alto, utf8_decode(date_format(date_create($FecEmi), "d/m/Y H:i:s")),'R', 0, "C");
        $pdf->Cell(30, $alto, utf8_decode(date_format(date_create($FecTraslado), "d/m/Y")), 'R', 0, "C");
        $pdf->Cell(32, $alto, utf8_decode($NumeroFactura), 'R', 0, "C");
        $y = $pdf->GetY();
        $x = $pdf->GetX();
        $pdf->SetFont('Arial','',7);
        $alto3 = $pdf->GetMultiCellHeight(55, 3, $Motivo, $borde2, "C");
        $pdf->Multicell(55, 3, utf8_decode($Motivo), 'R', "C");
        $pdf->SetXY($x,$y);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(55, $alto, utf8_decode(""), 'R', 0, "C");
        $pdf->Cell(35, $alto, utf8_decode($Modalidad), 0, 0, "C");
        if($alto3>3){
            $pdf->RoundedRect(15, 48, 185, 11,2,'1234');
        }else{
            $pdf->RoundedRect(15, 48, 185, 10,2,'1234');
        }


        $pdf->SetXY(15, $pdf->GetY()+$alto*2);
        
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',8);
        //$pdf->SetDrawColor(205, 205, 205);borde
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->Cell(93, $alto, utf8_decode("DIRECCIÓN DE PARTIDA"), 1, 0, "C",true);
        $pdf->Cell(92, $alto, utf8_decode("DIRECCIÓN DE LLEGADA"), 1, 0, "C",true);
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetDrawColor(0, 0, 0);

        $pdf->SetTextColor(0);
        $alto2=4;
        $pdf->SetFont('Arial','',8);
        $alto3 = $pdf->GetMultiCellHeight(92, 4, $DireccionFin, $borde2, "C");
        $pdf->Cell(93, $alto2, utf8_decode($DireccionInicio), 0, 0, "C");
        $y = $pdf->GetY();
        $pdf->MultiCell(92, 4, utf8_decode($DireccionFin), 0, "C");
        $pdf->SetXY($pdf->GetX(),$y+$alto3);

        $pdf->Cell(93, $alto2, utf8_decode($UbigeoInicio), 0, 0, "C");
        $pdf->Cell(92, $alto2, utf8_decode($UbigeoFin), 0, 0, "C");
        $pdf->SetXY(12, $pdf->GetY()+$alto2);

        $pdf->RoundedRect(15, 63, 185, 13+$alto3-$alto2,2,'34');


        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $yinicial = $pdf->GetY();
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',8);
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->Cell(75, $alto, utf8_decode("DESTINATARIO"), 1, 0, "C",true);
        $pdf->Cell(50, $alto, utf8_decode("UNIDAD DE TRANSPORTE"), 1, 0, "C",true);
        $pdf->Cell(60, $alto, utf8_decode("DATOS CONDUCTORES"), 1, 0, "C",true);
        $pdf->SetXY(15, $pdf->GetY()+$alto);
        $pdf->SetDrawColor(0, 0, 0);

        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','',8);
        $alto3=4;
        $alto2 = $pdf->GetMultiCellHeight(75, 4, $DestinatarioNombre, $borde2, "C");
        $alto21 = $pdf->GetMultiCellHeight(60, 4, $TransporteNombre, $borde2, "L");
        if($alto21>$alto2){
            $alto2 = $alto21;
        }
        $y = $pdf->GetY();
        $pdf->Multicell(75, 4, utf8_decode($DestinatarioNombre), 0, "C");
        $pdf->SetXY($pdf->GetX(),$y);
        $pdf->Cell(75, $alto3, utf8_decode(""), 0, 0, "C");
        $pdf->Cell(50, $alto3, utf8_decode($TransportePlaca), 0, 0, "C");
        $pdf->Multicell(60, 4, utf8_decode($TransporteNombre), 0, "C");
        $pdf->SetXY(12, $y+$alto2);

        $pdf->Cell(75, $alto3, utf8_decode("Num. Doc:".$DestinatarioDoc), 0, 0, "C");
        $pdf->Cell(50, $alto3, utf8_decode(""), 0, 0, "C");
        $pdf->Cell(60, $alto3, utf8_decode($TransporteTipoDoc.": ".$TransporteDoc), 0, 0, "C");
        $pdf->SetXY(12, $pdf->GetY()+$alto3);

        $pdf->RoundedRect(15, $yinicial, 185, 13+$alto2-1,2,'34');

        $pdf->SetXY(14, $pdf->GetY()+$alto3);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(100, $alto2, utf8_decode("REMITIMOS A UD.(ES) EN BUENAS CONDICIONES LO SIGUIENTE:"), 0, 0, "L");
        $pdf->SetXY(15, $pdf->GetY()+$alto3*2);
        
        $productosRectY = $pdf->GetY();
        $pdf->SetFillColor(41,50,131);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',$fontSize);
        $pdf->Cell(15, $alto, utf8_decode("CANTIDAD"), 0, 0, "C", true);
        $pdf->Cell(18, $alto, utf8_decode("CODIGO"), 0, 0, "C", true);
        $pdf->Cell(105, $alto, utf8_decode("PRODUCTO"), 0, 0, "C", true);
        $pdf->Cell(30, $alto, utf8_decode("UNID/MED"), 0, 0, "C", true);
        $pdf->Cell(17, $alto, utf8_decode("PESO"), 0, 0, "C", true);
        $productosRectHeight += $alto;
        $pdf->SetFont('Arial','',$fontSize);
        $pdf->Ln();
        $total = 0;
        $alto = 5;
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255, 255, 255);
        $borde2 = 0;
        $productosRectHeight = 0;
        foreach ($Detalles as $key => $detalle) {
            $alto2 = $pdf->GetMultiCellHeight(105, $alto, mb_strtoupper(str_replace("LOTE", "\nLOTE", strtoupper($detalle["descripcion"]))), $borde2, "L");
            if(!is_null($detalle["nombreunidad"]) && $detalle["nombreunidad"]!=""){
                $unidad = $detalle["nombreunidad"];
            }else{
                $unidad = $mdlUnidad->verUnidad2($detalle["unidadmedida"])["nombre_unidad"];
            }
            $pdf->SetXY(15, $pdf->GetY());
            $pdf->Cell(15, $alto, utf8_decode(number_format($detalle["cantidad"],0)), $borde2, 0, "C");
            $pdf->Cell(18, $alto, utf8_decode($detalle["codigo"]), $borde2, 0, "C");
            $y=$pdf->GetY();
            $pdf->MultiCell(105, $alto, mb_strtoupper(str_replace("LOTE", "\nLOTE", strtoupper($detalle["descripcion"]))), $borde2, "L");
            $pdf->SetXY(153,$y);
            $pdf->Cell(30, $alto, utf8_decode($unidad), $borde2, 0, "C");
            $pdf->Cell(17, $alto, utf8_decode(number_format($detalle["pesounitario"],0)), $borde2, 0, "C");
            $pdf->SetXY(15, $pdf->GetY()+$alto2);
            if($pdf->GetY()>265){
                $pdf->RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*3 + $alto2,2,'34');
                $pdf->AddPage('P','A4');
                $pdf->SetXY(15, 10);
                $productosRectHeight = 0;
                $productosRectY = 10;
            }
            $productosRectHeight += $alto2;
        }
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(30, $alto, utf8_decode("Peso Bruto (KGM):"), 'T', 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(155, $alto, utf8_decode($PesoTotal), 'T', 0, "L");

        $pdf->SetXY(15, $pdf->GetY()+$alto);

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(40, $alto, utf8_decode("Numero de Bultos o Pallets:"), 'T', 0, "L");
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(145, $alto, utf8_decode($Paquetes), 'T', 0, "L");

        $pdf->SetXY(15, $pdf->GetY()+$alto);

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(25, $alto, utf8_decode("Observaciones:"), 'T', 0, "L");
        $pdf->SetFont('Arial','',8);

        $alto2 = $pdf->GetMultiCellHeight(105, $alto, mb_strtoupper(strtoupper($Observaciones)), $borde2, "L");
        $pdf->Multicell(160, $alto, utf8_decode($Observaciones), 'T', "L");

        $pdf->RoundedRect(15, $productosRectY, 185, $productosRectHeight + $alto*3 + $alto2,2,'34');

        $pdf->SetXY(12, $pdf->GetY()+$alto);


        $pdf->SetFont('Arial','',8);
        //$pdf->Cell(185, $alto, utf8_decode("Representación impresa de la Guía de Remisión Electrónica, consulte en https://facturae-garzasoft.com"), $borde, 0, "L");
        $pdf->Ln();
        
        $pdf->Ln();

        $pdf->Output(__DIR__ ."/../ficheros/".$nombre_documento."pdf", 'F'); 

        if($email){
            $pdf->close();
            return true;
        }else{
            $pdf->Output($nombre_documento."pdf", 'I'); // Se muestra el documento .PDF en el navegador.    */
            $pdf->Output();
            exit;
        }
    }
    
    public static function GenerarPLESunat($fecini,$fecfin,$nombre){
        include_once '../modelo/mdlSolicitud.php';
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes4($fecini,$fecfin,$id_empresa);
        
        $helper = new Sample();
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue("A2", "Tipo_Comprob");
        $sheet->setCellValue("B2", "Serie");
        $sheet->setCellValue("C2", "Correlativo");
        $sheet->setCellValue("D2", "Fecha_Comprob");
        $sheet->setCellValue("E2", "Fecha_Contable");
        $sheet->setCellValue("F2", "RUC/DNI");
        $sheet->setCellValue("G2", "Dias_Pago");
        $sheet->setCellValue("H2", "Cod_Alm");
        $sheet->setCellValue("I2", "Item");
        $sheet->setCellValue("J2", "Cod_Art");
        $sheet->setCellValue("K2", "UM");
        $sheet->setCellValue("L2", "Cantidad");
        $sheet->setCellValue("M2", "Importe");
        $sheet->setCellValue("N2", "Valor_Venta");
        $sheet->setCellValue("O2", "Descuento");
        $sheet->setCellValue("P2", "Igv");
        $sheet->setCellValue("Q2", "Afecto_Igv");
        $sheet->setCellValue("R2", "Peso");
        $sheet->setCellValue("S2", "Moneda");
        $sheet->setCellValue("T2", "TC");
        $sheet->setCellValue("U2", "Fecha_Comprob_Origen");
        $sheet->setCellValue("V2", "Tipo_Comprob_Origen");
        $sheet->setCellValue("W2", "Serie_Origen");
        $sheet->setCellValue("X2", "Correlativo_Origen");
        $sheet->setCellValue("Y2", "Item_Origen");
        $sheet->setCellValue("Z2", "Estado");
        $sheet->setCellValue("AA2", "Manual");
        $sheet->setCellValue("AB2", "Categoria_Cont");
        
        $i = 3;
        foreach ($comprobantes as $comprobante) {
            $data = json_decode($comprobante["data_solicitud"],true);
            $detalles = $data["detalles"];
            $tipoDoc = "";
            $docId = "";
            $diasPago = "0";
            if($comprobante["tipo_documento"]=="F"){
                $tipoDoc = "001";
                $docId = $data["ruc"];
            }elseif($comprobante["tipo_documento"]=="B"){
                $tipoDoc = "003";
                $docId = $data["dni"];
            }elseif($comprobante["tipo_documento"]=="C"){
                $tipoDoc = "007";
                $docId = $data["doc"];
            }elseif($comprobante["tipo_documento"]=="D"){
                $tipoDoc = "008";
                $docId = $data["doc"];
            }
            $tasaIGV = $comprobante["IGV"];
            if(empty($tasaIGV) || strlen(trim($tasaIGV))){
                $tasaIGV = 0.18;
            }
            $moneda = $data["moneda"];
            if($moneda=="PEN"){
                $moneda = "SOL";
            }
            $numeroreferencia = $data["numeroreferencia"];
            $fechareferencia = $data["fechareferencia"];
            $seriereferencia = "";
            $tiporeferencia = "";
            if(strlen($numeroreferencia)>0){
                $numeroreferencia = explode("-", $numeroreferencia);
                $seriereferencia = $numeroreferencia[0];
                $numeroreferencia = $numeroreferencia[1];
                if(substr($seriereferencia, 0, 1)=="F"){
                    $tiporeferencia = "001";
                }elseif(substr($seriereferencia, 0, 1)=="B"){
                    $tiporeferencia = "003";
                }
                $fechareferencia = date_format(date_create($fechareferencia),"d/m/Y");
            }
            $estado = "";
            if($comprobante["estado_solicitud"]=="T" || $comprobante["estado_solicitud"]=="M"){
                $estado = "020";
            }else{
                $estado = "090";
            }
            /*$importe = 0;
            foreach ($detalles as $detalle) {
                $importe = $importe + ($detalle["cantidad"] * $detalle["precioventaunitarioxitem"]);
            }*/
                $importeT = 0;
                $igvT = 0;
                $afectoT = "0";
                $cantidadT = 0;
                $descuentoT = 0;
            foreach ($detalles as $item => $detalle) {
                $codAlm = "001";
                $codArt = $detalle["codigo"];
                if($codArt == "-"){
                    $codArt = "0";
                }
                $undMed = $detalle["unidadmedida"];
                if($undMed=="NIU" || $undMed == "ZZ" || $comprobante["tipo_documento"]=="C" || $comprobante["tipo_documento"]=="D"){
                    $undMed = "UND";
                }
                $cantidadT = $cantidadT + $detalle["cantidad"];
                $descuento = $detalle["descuentoxitem"];
                if($descuento > 0){
                    $descuentoT = $descuentoT + $descuento * $cantidad * (($detalle["precioventaunitarioxitem"] * $tasaIGV)/(1 + $tasaIGV));
                }
                $importe = (($detalle["cantidad"] * $detalle["precioventaunitarioxitem"])) - $descuento;
                $importeT = $importeT + $importe;
                if($detalle["tipoigv"]>=10 && $detalle["tipoigv"]<20){
                    $igv = ($importe * $tasaIGV)/(1 + $tasaIGV);
                    $igvT = $igvT + $igv;
                    $afecto = "1";
                }
                error_log("FECHA SIN ".$data["fechaemision"]);
                error_log("FECHA CON ".date_format(date_create($data["fechaemision"],"d/m/Y")));
                error_log("----------------");
            }
                if($comprobante["tipo_documento"]=="C"){
                    $importeT = $importeT * (-1);
                }
                
                $sheet->setCellValue("A".$i, $tipoDoc);
                $sheet->setCellValue("B".$i, $comprobante["serie"]);
                $sheet->setCellValue("C".$i, $comprobante["correlativo"]);
                $sheet->setCellValue("D".$i, date_format(date_create($data["fechaemision"]),"d/m/Y"));
                $sheet->setCellValue("E".$i, date_format(date_create($comprobante["fechahora_envio"]),"d/m/Y"));
                $sheet->setCellValue("F".$i, $docId);
                $sheet->setCellValue("G".$i, $diasPago);
                $sheet->setCellValue("H".$i, $codAlm);
                $sheet->setCellValue("I".$i, 1);
                $sheet->setCellValue("J".$i, 1);
                $sheet->setCellValue("K".$i, "UND");
                $sheet->setCellValue("L".$i, 1);
                $sheet->setCellValue("M".$i, round($importeT,2));
                $sheet->setCellValue("N".$i, round($importeT - $igvT,2));
                $sheet->setCellValue("O".$i, round($descuentoT,2));
                $sheet->setCellValue("P".$i, round($igvT,2));
                $sheet->setCellValue("Q".$i, 1);
                $sheet->setCellValue("R".$i, "0");
                $sheet->setCellValue("S".$i, $moneda);
                $sheet->setCellValue("T".$i, "0");
                $sheet->setCellValue("U".$i, $fechareferencia);
                $sheet->setCellValue("V".$i, $tiporeferencia);
                $sheet->setCellValue("W".$i, $seriereferencia);
                $sheet->setCellValue("X".$i, $numeroreferencia);
                $sheet->setCellValue("Y".$i, "0");
                $sheet->setCellValue("Z".$i, $estado);
                $sheet->setCellValue("AA".$i, "0");
                $sheet->setCellValue("AB".$i, "CFAC");
                $i++;
        }
        
        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('PLESunat');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="PLESunat.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }
    
    public static function GenerarResumenComprobantes($fecini,$fecfin,$id_empresa){
        include_once '../modelo/mdlSolicitud.php';
        $mdlSolicitud = new mdlSolicitud();
        $comprobantes = $mdlSolicitud->listarSolicitudes4($fecini,$fecfin,$id_empresa);
        
        $helper = new Sample();
        
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue("A1", "N°");
        $sheet->setCellValue("B1", "Tipo Comprobante");
        $sheet->setCellValue("C1", "Numeracion");
        $sheet->setCellValue("D1", "DNI/RUC");
        $sheet->setCellValue("E1", "Nombre/Razon Social");
        $sheet->setCellValue("F1", "Total");
        $sheet->setCellValue("G1", "Fecha de Emision");
        $sheet->setCellValue("H1", "Fecha de Declaracion");
        $sheet->setCellValue("I1", "Estado");
        
        $i = 2;
        $w = 1;
        foreach ($comprobantes as $comprobante) {
            $data = json_decode($comprobante["data_solicitud"],true);
            $detalles = $data["detalles"];
            $tipoDoc = "";
            $docId = "";
            $diasPago = "0";
            if($comprobante["tipo_documento"]=="F"){
                $tipoDoc = "001";
                $docId = $data["ruc"];
                $tipoDocumento = "FACTURA";
                $numeracion = $data["numerofactura"];
            }elseif($comprobante["tipo_documento"]=="B"){
                $tipoDoc = "003";
                $docId = $data["dni"];
                $tipoDocumento = "BOLETA";
                $numeracion = $data["numeroboleta"];
            }elseif($comprobante["tipo_documento"]=="C"){
                $tipoDoc = "007";
                $docId = $data["doc"];
                $tipoDocumento = "NOTA DE CREDITO";
                $numeracion = $data["numeronotacredito"];
            }elseif($comprobante["tipo_documento"]=="D"){
                $tipoDoc = "008";
                $docId = $data["doc"];
                $tipoDocumento = "NOTA DE DEBITO";
                $numeracion = $data["numeronotadebito"];
            }
            if($comprobante["estado_solicitud"]=="T" || $comprobante["estado_solicitud"]=="M"){
                $estadoComprobante = "ACTIVO";
            }else{
                $estadoComprobante = "DE BAJA";
            }
            $sheet->setCellValue("A".$i, $w);$w++;
            $sheet->setCellValue("B".$i, $tipoDocumento);
            $sheet->setCellValue("C".$i, $numeracion);
            $sheet->setCellValue("D".$i, $docId);
            $sheet->setCellValue("E".$i, $data["usuario"]);
            $sheet->setCellValue("F".$i, $comprobante["total_doc"]);
            $sheet->setCellValue("G".$i, date_format(date_create($data["fechaemision"]), "d/m/Y"));
            $sheet->setCellValue("H".$i, date_format(date_create($comprobante["fechahora_envio"]), "d/m/Y"));
            $sheet->setCellValue("I".$i, $estadoComprobante);
            $i++;
        }
        
        $spreadsheet->getActiveSheet()->setTitle('Comprobantes');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;

    }
    
    public static function ComprobarRespuestaXML($filenameR_xml){
        if(!file_exists($filenameR_xml)){
            throw new Exception("NO SE ENCUENTRA EL FICHERO EN LA RUTA: ".$filenameR_xml);
        }
        $xml = file_get_contents($filenameR_xml);
        $DOM = new DOMDocument('1.0', 'ISO-8859-1');
        $DOM->preserveWhiteSpace = FALSE;
        $DOM->loadXML($xml);
        $DocumentResponse = $DOM->getElementsByTagName('DocumentResponse')->item(0);
        $ResponseCode = $DocumentResponse->getElementsByTagName('ResponseCode');
        foreach($ResponseCode as $Nodo){
            $code = $Nodo->nodeValue;
            break;
        }
        $Description = $DocumentResponse->getElementsByTagName('Description');
        foreach($Description as $Nodo){
            $descripcion = $Nodo->nodeValue;
            break;
        }
        return array(
            "codigo"=>$code,
            "detalle"=>$descripcion
        );
    }

    public static function QuitarTildes($cadena) {
        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $texto = str_replace($no_permitidas, $permitidas ,$cadena);
        return $texto;
    }
    
    public static function EnviarCorreoNuevaPoliza($usuario,$poliza) {
        if (!empty($usuario["correos"]) && is_array($usuario["correos"]) && count($usuario["correos"])>0) {
            $para = implode(", ", $usuario["correos"]);
            $título = 'NUEVA POLIZA';
            $mensaje = '
                <html>
                    <head>
                      <title>NUEVA POLIZA</title>
                    </head>
                    <body>
                        <p>¡SE HA REGISTRADO UNA NUEVA POLIZA EN LA PLATAFORMA GCCORREDORES!</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>NUMERO</th>
                                    <th>FECHA INICIO</th>
                                    <th>FECHA FINAL</th>
                                    <th>NOMBRE DE POLIZA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $poliza["numero_poliza"] . '</td>
                                    <td>' . $poliza["fecha_inicio_poliza"] . '</td>
                                    <td>' . $poliza["fecha_fin_poliza"] . '</td>
                                    <td>' . $poliza["nombre_compania_tipopoliza"] . '</td>
                                </tr>
                            </tbody>
                        </table>
                        <p>INGRESA A <a href="http://gccorredores.com/seguros">AQUI</a></p>
                    </body>
                </html>
                ';
            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: GCCorredores <sistema@gccorredores.com>' . "\r\n";
            mail($para, $título, $mensaje, $cabeceras);
        }
    }
    
    public static function EnviarCorreoNuevoUsuario($usuario) {
        if (!empty($usuario["correos"]) && is_array($usuario["correos"]) && count($usuario["correos"])>0) {
            $para = implode(", ", $usuario["correos"]);
            $título = 'USUARIO REGISTRADO';
            $mensaje = '
                <html>
                    <head>
                      <title>USUARIO REGISTRADO</title>
                    </head>
                    <body>
                        <p>¡SE HA REGISTRADO EN LA PLATAFORMA GCCORREDORES!</p>
                        <p><b>USUARIO: </b>' . $usuario["user_usuario"] . '</p>
                        <p><b>CONTRASEÑA: </b>' . $usuario["pass_usuario"] . '</p>
                        <p>INGRESA A <a href="http://gccorredores.com/seguros">AQUI</a></p>
                    </body>
                </html>
                ';
            $cabeceras = 'MIME-Version: 1.0' . "\r\n";
            $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $cabeceras .= 'From: GCCorredores <sistema@gccorredores.com>' . "\r\n";
            mail($para, $título, $mensaje, $cabeceras);
        }
    }
    
}
//Algoritmos::FirmaDigital(new DOMDocument(),"pruebaId", "CERTIFICADO.cer", "at25jk3o2016");
//$xmlFile = Algoritmos::FirmaDigital(new DOMDocument(),"signature10723124871", "dlVyY0hRRklGa1RxRGFsSQ==.p12", "vcNTfp5gEdP8VH4z");
//$xmlFile->save(__DIR__ ."/../ficheros/$nombre_documento.xml");
/*$domDocument = new DOMDocument();
        $domDocument->load(__DIR__ ."/../ficheros/10723124871-01-F001-4355.xml");

        $zip = new ZipArchive();
        $filename_zip = __DIR__ ."/../ficheros/".$nombre_documento."zip";
        if($zip->open($filename_zip,ZIPARCHIVE::CREATE)===true) {
            //$zip->addEmptyDir("dummy");
            $zip->addFile(__DIR__ ."/../ficheros/".$nombre_documento."xml",$nombre_documento."xml");
            //$zip->addFile($nombre_documento."xml","dummy/".$nombre_documento."xml");
            $zip->close();
        }else {
            throw new Exception('Error creando '.$filename_zip);
        }
        
        $file_ZIP = file_get_contents($filename_zip);
        $file_ZIP_BASE64 = base64_encode($file_ZIP);
        
        $cliente_SUNAT = new nusoap_client(WBSV_ENV_PRO);
        $error = $cliente_SUNAT->getError();
        if ($error) {
            throw new Exception("ERROR: ".$error);
        }
        $cliente_SUNAT->setCredentials($usuario_sunat, $password_sunat);
        
        $mdlSolicitud->actualizarSolicitud($id_solicitud, $nombre_documento, date("Y-m-d\TH:i:s"), "", "E", 
                array(
                    array($nombre_documento,$filename_xml,"xml"),
                    array($nombre_documento,$filename_zip,"zip"),
                ));
        
        $result = $cliente_SUNAT->call("sendBill", array(
            "fileName"=>$nombre_documento."zip",
            "contentFile"=>$file_ZIP_BASE64
        ));
        
        throw new Exception("ERROR 0: ". json_encode($result));
        if ($cliente_SUNAT->fault) {
            throw new Exception("ERROR 1: ". json_encode($result));
        } else {
            $error = $cliente->getError();
            if ($error) {
                throw new Exception("ERROR 2: ". json_encode($error));
            } else {
                throw new Exception("NO HAY ERROR");
            }
        }
        */