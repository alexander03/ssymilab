<?php
include_once '../cado/AccesoPostgres.php';

class mdlSubmenu extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function insertarSubmenu($nombre,$ruta,$icono,$orden,$id_menu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO submenu(nombre_submenu,ruta_submenu,icono_submenu,orden_submenu,id_menu) VALUES ($1,$2,$3,$4,$5)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$ruta,$icono,$orden,$id_menu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarSubmenu($id_submenu,$nombre,$ruta,$icono,$orden,$id_menu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE submenu SET nombre_submenu = $1,  ruta_submenu = $2,  icono_submenu = $3,  orden_submenu = $4, id_menu = $5 WHERE id_submenu = $6";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$ruta,$icono,$orden,$id_menu,$id_submenu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarSubmenu($id_submenu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE submenu SET estado_submenu = 'A' WHERE id_submenu = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_submenu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verSubmenu($id_submenu) {
        $sql = "SELECT * FROM submenu md WHERE id_submenu = $1";
        $result = AccesoPostgres::obtener($sql,array($id_submenu));
        $menu = array();
        while ($fila = pg_fetch_object($result)) {
            $menu = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "orden_submenu"=>$fila->orden_submenu,
                "id_menu"=>$fila->id_menu,
            );
        }
        return $menu;
    }
    
    public function listarSubmenus($id_menu) {
        $sql = "SELECT * FROM submenu sm";
        $sql_where = " WHERE estado_submenu = 'N'";
        $sql_order = " ORDER BY sm.nombre_submenu";
        $n=1;
        $parametros = array();
        if(!empty($id_menu) && strlen($id_menu)>0 && $id_menu>0){ $sql_where.= " AND sm.id_menu = $$n";$parametros[]=$id_menu;$n++;}
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $submenus = array();
        while ($fila = pg_fetch_object($result)) {
            $submenus[] = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "orden_submenu"=>$fila->orden_submenu,
                "id_menu"=>$fila->id_menu
            );
        }
        return $submenus;
    }
    
    public function listarSubmenus2($nombre,$menu,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM submenu sm LEFT JOIN menu mn ON sm.id_menu = mn.id_menu";
        $sql_count = "SELECT count(DISTINCT sm.id_submenu) as numero_filas FROM submenu sm LEFT JOIN menu mn ON sm.id_menu = mn.id_menu";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mn.nombre_menu,sm.nombre_submenu";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_submenu = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND sm.nombre_submenu ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($menu) && strlen($menu)>0 && $menu>0){ $sql_where.= " AND sm.id_menu = $$n";$parametros[] = $menu;$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $submenus = array();
        while ($fila = pg_fetch_object($result)) {
            $submenus[] = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "orden_submenu"=>$fila->orden_submenu,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu
            );
        }
        return array($submenus,$numero_filas);
    }
    
    public function listarSubmenus3($id_perfil) {
        $sql = "SELECT sm.*,mn.* FROM submenu sm"
                . " INNER JOIN perfil_submenu pfsm ON sm.id_submenu = pfsm.id_submenu"
                . " INNER JOIN menu mn ON sm.id_menu = mn.id_menu";
        $sql_where = " WHERE sm.estado_submenu = 'N'";
        $sql_order = " ORDER BY mn.orden_menu,mn.id_menu,sm.orden_submenu";
        $n=1;
        $parametros = array();
        if(!empty($id_perfil) && strlen($id_perfil)>0 && $id_perfil>0){ $sql_where.= " AND pfsm.id_perfil = $$n";$parametros[]=$id_perfil;$n++;}
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $submenus = array();
        while ($fila = pg_fetch_object($result)) {
            $submenus[] = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu
            );
        }
        return $submenus;
    }
    
    public function listarSubmenus4($nombre,$perfil,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM submenu sm INNER JOIN perfil_submenu pfsm ON sm.id_submenu = pfsm.id_submenu";
        $sql_count = "SELECT count(DISTINCT sm.id_submenu) as numero_filas FROM submenu sm INNER JOIN perfil_submenu pfsm ON sm.id_submenu = pfsm.id_submenu";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mn.nombre_perfil,sm.nombre_submenu";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_submenu = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND sm.nombre_submenu ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($menu) && strlen($menu)>0 && $menu>0){ $sql_where.= " AND sm.id_menu = $$n";$parametros[] = $menu;$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $submenus = array();
        while ($fila = pg_fetch_object($result)) {
            $submenus[] = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "orden_submenu"=>$fila->orden_submenu,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu
            );
        }
        return array($submenus,$numero_filas);
    }
    
    public function listarSubmenus5($id_perfil) {
        $sql = "SELECT sm.*,mn.*,(SELECT pfsm.id_perfil_submenu FROM perfil_submenu pfsm WHERE pfsm.id_perfil = $1 AND pfsm.id_submenu = sm.id_submenu) AS id_perfil_submenu FROM submenu sm"
                . " INNER JOIN menu mn ON sm.id_menu = mn.id_menu";
        $sql_where = " WHERE sm.estado_submenu = 'N'";
        $sql_order = " ORDER BY mn.orden_menu,mn.id_menu,sm.orden_submenu";
        $n=1;
        $parametros = array();
        $parametros[]=$id_perfil;$n++;
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $submenus = array();
        while ($fila = pg_fetch_object($result)) {
            $submenus[] = array(
                "id_submenu"=>$fila->id_submenu,
                "nombre_submenu"=>$fila->nombre_submenu,
                "ruta_submenu"=>$fila->ruta_submenu,
                "icono_submenu"=>$fila->icono_submenu,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu,
                "id_perfil_submenu"=>$fila->id_perfil_submenu
            );
        }
        return $submenus;
    }
    
}
