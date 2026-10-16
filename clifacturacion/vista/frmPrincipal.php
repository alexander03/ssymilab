<?php
session_start();
///PARA FE
include '../modelo/mdlUsuario.php';
include '../modelo/mdlEmpresa.php';
include '../modelo/mdlSubmenu.php';
$mdlEmpresa = new mdlEmpresa();
$mdlUsuario = new mdlUsuario();
$usuario = $mdlUsuario->login('symilab','Symilab*2024');
$empresa = $mdlEmpresa->verEmpresa($usuario["id_empresa"]);
$_SESSION["usuario"] = $usuario;
$_SESSION["empresa"] = $empresa;

$empresa = $mdlEmpresa->verEmpresa($usuario["id_empresa"]);
$menus = array();
$mdlSubmenu = new mdlSubmenu();
$index = -1;
$ids_submenus = array();
$ids_menus = array();
foreach ($usuario["ids_perfiles"] as $key=>$id_perfil) {
    if($usuario["tipos_usuario"][$key]=="A"){
        $_SESSION["isAdmin"] = true;
    }
    if($usuario["tipos_usuario"][$key]=="O"){
        $_SESSION["isOtro"] = true;
    }
    if($usuario["tipos_usuario"][$key]=="C"){
        $_SESSION["isCliente"] = true;
    }
    $submenus = $mdlSubmenu->listarSubmenus3($id_perfil);
    $ultimo_menu = 0;
    foreach ($submenus as $submenu) {
        if(!in_array($submenu["id_submenu"], $ids_submenus)){
            $i = -1;
            $id_menu = intval($submenu["id_menu"]);
            $id_menu = $ids_menus[$id_menu];
            if($id_menu>0){
                $i = $ids_menus[$submenu["id_menu"]] - 1;
            }else{
                if($ultimo_menu != $submenu["id_menu"]){
                    $index++;
                    $menus[$index] = array($submenu["nombre_menu"],array());
                    $ultimo_menu = $submenu["id_menu"];
                    $ids_submenus[] = $submenu["id_submenu"];
                    $ids_menus[intval($submenu["id_menu"])] = $index + 1;
                    $i = $index;
                }
            }
            $ruta = $submenu["ruta_submenu"];
            $ruta = str_replace("#id_usuario", $usuario["id_usuario"], $ruta);
            $ruta = str_replace("#id_persona", $usuario["id_persona"], $ruta);
            if(!empty($cliente)){
                $ruta = str_replace("#id_cliente", $cliente["id_cliente"], $ruta);
            }
            if(!empty($ejecutivo)){
                $ruta = str_replace("#id_ejecutivo", $ejecutivo["id_ejecutivo"], $ruta);
            }
            $menus[$i][1][] = array($submenu["nombre_submenu"],$ruta,$submenu["icono_submenu"]);
        }
    }
}
$usuario["menu"] = $menus;
$_SESSION["usuario"] = $usuario;
$_SESSION["empresa"] = $empresa;
$_SESSION["token"] = "";
$LIMITE_DE_LISTAS = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'LIMITE_DE_LISTAS'", array()))->valor_propiedad;
if(empty($LIMITE_DE_LISTAS) || $LIMITE_DE_LISTAS<=0){
    $LIMITE_DE_LISTAS = 10;
}
$LIMITE_DE_PAGINACION = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'LIMITE_DE_PAGINACION'", array()))->valor_propiedad;
if(empty($LIMITE_DE_PAGINACION) || $LIMITE_DE_PAGINACION<=0){
    $LIMITE_DE_PAGINACION = 5;
}
$WSDL_AUTORIZACION = pg_fetch_object($mdlUsuario->obtener("SELECT valor_propiedad FROM propiedad WHERE codigo_propiedad = 'WSDL_AUTORIZACION'", array()))->valor_propiedad;
if(empty($WSDL_AUTORIZACION) || strlen(trim($WSDL_AUTORIZACION))<=0){
    //$WSDL_AUTORIZACION = "http://martinampuero.com/facturacion/wsdl/autenticacion.php";
    //$WSDL_AUTORIZACION = "http://martinampuero.com/facturacion/wsdl/autenticacion.php";
    throw new Exception("NO SE ENCUENTRA EL WEBSERVICE DE AUTENTICACION");
}
$_SESSION["Propiedad"]["LIMITE_DE_LISTAS"] = $LIMITE_DE_LISTAS;
$_SESSION["Propiedad"]["LIMITE_DE_PAGINACION"] = $LIMITE_DE_PAGINACION;
$_SESSION["Propiedad"]["WSDL_AUTORIZACION"] = $WSDL_AUTORIZACION;
$_SESSION["Filtros"] = array();

include '../controlador/Algoritmos.php';
$valido = Algoritmos::ValidarUsuario($_SESSION["usuario"]);
$usuario = $_SESSION["usuario"];
$menus = $usuario["menu"];
?>
<!DOCTYPE html>
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="../css/materialize.css"  media="screen,projection"/>
        <link rel="shortcut icon" type="image/png" href="../images/logo3.png"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Comfortaa" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Righteous" rel="stylesheet">
        <script src="../js/ResizeSensor.js"></script>
        <script src="../js/ElementQueries.js"></script>
        <link href="../css/estilos.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>CLIFACTURACION ELECTRONICA</title>
    </head>
    <body>
        <header>
            <div class="navbar-fixed">
                <nav>
                    <div class="nav-wrapper">
                        <a class="brand-logo menu-lateral pointer hide-on-med-and-down" data-activates="slide-out"><img src="../images/logo3.png" class="responsive-img logo-barra left">CLIFACTURACION ELECTRONICA</a>
                        <a class="brand-logo pointer hide-on-large-only">CLIFACTURACION ELECTRONICA</a>
                        <a href="#" data-activates="slide-out" class="menu-lateral" ><img src="../images/logo3.png" class="responsive-img logo-barra"></a>
                        <ul id="nav-mobile" class="right hide-on-med-and-down table-of-contents">
                            <li><a class="btn" href="../controlador/contUsuario.php?funcion=logout">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
            <ul id="slide-out" class="side-nav">
                <li><div class="userView">
                  <div class="background">
                    <img src="../images/office.jpg">
                  </div>
                  <a href="#!" onclick="recargarPorURL('../vista/frmMiPerfil.php?id_usuario=<?php echo $usuario["id_usuario"];?>','')"><img class="circle" src="../images/user.png"></a>
                  <a href="#!"><span class="white-text name"><?php echo $_SESSION["usuario"]["nombres_persona"]." ".$_SESSION["usuario"]["apellidos_persona"];?></span></a>
                  <a href="#!"><span class="white-text email"><?php echo $_SESSION["usuario"]["correo"];?></span></a>
                </div></li>
                <?php foreach ($menus as $key => $menu) { ?>
                <li><a class="subheader"><?php echo $menu[0];?></a></li>
                <?php foreach ($menu[1] as $item) { ?>
                <li class="row" style="margin: 0px;">
                    <a class="pointer" style="width: 100%; height: 40px;" onclick="recargarPorURL('<?php echo $item[1];?>','')">
                        <div class="col s2 valign-wrapper" style="height: 40px;"><i class="material-icons prefix"><?php echo $item[2];?></i></div>
                        <div class="col s10 valign-wrapper" style="height: 40px;"><p style="line-height: 20px; margin: 0px;"><?php echo $item[0];?></p></div>
                    </a>
                </li>
                <?php }?>
                <li><div class="divider"></div></li>
                <?php }?>
            </ul>
        </header>
        <main class="main_principal">
            <div class="">
                <nav>
                    <div class="nav-wrapper lstSecuencia" style="overflow-y: auto;">
                        <div class="col s12" id="lstSecuencia">
                        </div>
                    </div>
                </nav>
                <div id="divPrincipal" class="contenido_principal">
                    
                </div>
                <div id="modDetallesTabla" class="modal modal-fixed-footer">
                    <div>
                        <button class="btn btn-floating right red modal-action modal-close boton-close valign-wrapper"><i class="material-icons tiny">clear</i></button>
                    </div>
                    <div class="modal-content">
                        <div class="row Header">
                            <h2>DETALLES DEL REGISTRO SELECCIONADO</h2>
                        </div>
                        <div class="row Content">
                            <table class="centered">
                                <tbody id="tbodyDetallesRegistro">
                                    <tr>
                                        <td>NOMBRE COMPLETO</td>
                                        <td>EDUARDO ESPINOZA LLONTOP</td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE COMPLETO</td>
                                        <td>EDUARDO ESPINOZA LLONTOP</td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE COMPLETO</td>
                                        <td>EDUARDO ESPINOZA LLONTOP</td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE COMPLETO</td>
                                        <td>EDUARDO ESPINOZA LLONTOP</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="modTasasDetalle" class="modal modal-fixed-footer modalDetalles">
                    <div>
                        <button class="btn btn-floating right red modal-action modal-close boton-close valign-wrapper"><i class="material-icons tiny">clear</i></button>
                    </div>
                    <div class="modal-content">
                        <div class="row Header">
                            <h2 id="h2Cabecera">DETALLES DEL REGISTRO SELECCIONADO</h2>
                        </div>
                        <div class="row Content" id="divContenido">
                        </div>
                    </div>
                </div>
                <div class="fixed-action-btn indigo-text text-darken-4 click-to-toggle">
                    <a class="btn-floating btn-large yellow" id="btnOpciones" data-position="left" data-delay="200" data-tooltip="MÁS OPCIONES">
                      <i class="large material-icons">menu</i>
                    </a>
                    <ul id="ulOpciones">
                    </ul>
                </div>
            </div>
        </main>
        <footer class="page-footer" style="padding: 0px;">
          <div class="footer-copyright">
            <div class="container">
            © 2018 Copyright Text Garzasoft
            </div>
          </div>
        </footer>
        <script type="text/javascript" src="../js/jquery-3.2.1.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script type="text/javascript">
            var firstMenu = $("#slide-out").find("a.pointer")[0];
            $(firstMenu).trigger("click");
        </script>
    </body>
</html>
