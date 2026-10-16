<?php
include_once '../cado/AccesoPostgres.php';

class mdlMenu  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function listarMenus() {
        $sql = "SELECT * FROM menu mn WHERE mn.estado_menu = 'N' ORDER BY mn.nombre_menu";
        $result = AccesoPostgres::obtener($sql,array());
        $menus = array();
        while ($fila = pg_fetch_object($result)) {
            $menus[] = array(
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu,
                "orden_menu"=>$fila->orden_menu
            );
        }
        return $menus;
    }
    
    public function insertarMenu($nombre,$orden) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO menu(nombre_menu,orden_menu) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$orden));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarMenu($id_menu,$nombre,$orden) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE menu SET nombre_menu = $1, orden_menu = $2 WHERE id_menu = $3";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$orden,$id_menu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarMenu($id_menu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE menu SET estado_menu = 'A' WHERE id_menu = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_menu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verMenu($id_menu) {
        $sql = "SELECT * FROM menu mn WHERE id_menu = $1 ORDER BY mn.nombre_menu";
        $result = AccesoPostgres::obtener($sql,array($id_menu));
        $menu = array();
        while ($fila = pg_fetch_object($result)) {
            $menu = array(
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu,
                "orden_menu"=>$fila->orden_menu
            );
        }
        return $menu;
    }
    
    public function listarMenus2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM menu mn";
        $sql_count = "SELECT count(DISTINCT mn.id_menu) as numero_filas FROM menu mn";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mn.nombre_menu";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_menu = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND mn.nombre_menu ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $menus = array();
        while ($fila = pg_fetch_object($result)) {
            $menus[] = array(
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu,
                "orden_menu"=>$fila->orden_menu
            );
        }
        return array($menus,$numero_filas);
    }
    
}
