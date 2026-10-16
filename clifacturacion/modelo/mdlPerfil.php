<?php
include_once '../cado/AccesoPostgres.php';

class mdlPerfil  extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function insertarPerfil($nombre,$tipo) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO perfil(nombre_perfil,tipo_usuario) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$tipo));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarPerfil($id_perfil,$nombre,$tipo) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE perfil SET nombre_perfil = $1, tipo_usuario = $2 WHERE id_perfil = $3";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$tipo,$id_perfil));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarPerfil($id_perfil) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE perfil SET estado_perfil = 'A' WHERE id_perfil = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_perfil));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function insertarPerfilSubmenu($id_perfil,$id_submenu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO perfil_submenu(id_perfil,id_submenu) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($id_perfil,$id_submenu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarPerfilSubmenu($id_perfil_submenu) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "DELETE FROM perfil_submenu WHERE id_perfil_submenu = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_perfil_submenu));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function insertarPerfilUsuario($id_perfil,$id_usuario) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO perfil_usuario(id_perfil,id_usuario) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($id_perfil,$id_usuario));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarPerfilUsuario($id_perfil_usuario) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "DELETE FROM perfil_usuario WHERE id_perfil_usuario = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_perfil_usuario));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verPerfil($id_perfil) {
        $sql = "SELECT * FROM perfil pf WHERE id_perfil = $1 ORDER BY pf.nombre_perfil";
        $result = AccesoPostgres::obtener($sql,array($id_perfil));
        $perfil = array();
        while ($fila = pg_fetch_object($result)) {
            $perfil = array(
                "id_perfil"=>$fila->id_perfil,
                "nombre_perfil"=>$fila->nombre_perfil,
                "tipo_usuario"=>$fila->tipo_usuario
            );
        }
        return $perfil;
    }
    
    public function listarPerfiles() {
        $sql = "SELECT * FROM perfil pf WHERE pf.estado_perfil = 'N' ORDER BY pf.nombre_perfil";
        $result = AccesoPostgres::obtener($sql,array());
        $perfiles = array();
        while ($fila = pg_fetch_object($result)) {
            $perfiles[] = array(
                "id_perfil"=>$fila->id_perfil,
                "nombre_perfil"=>$fila->nombre_perfil,
                "tipo_usuario"=>$fila->tipo_usuario
            );
        }
        return $perfiles;
    }
    
    public function listarPerfiles2($nombre,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM perfil pf";
        $sql_count = "SELECT count(DISTINCT pf.id_perfil) as numero_filas FROM perfil pf";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY pf.nombre_perfil";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_perfil = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND pf.nombre_perfil ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $perfiles = array();
        while ($fila = pg_fetch_object($result)) {
            $perfiles[] = array(
                "id_perfil"=>$fila->id_perfil,
                "nombre_perfil"=>$fila->nombre_perfil,
                "tipo_usuario"=>$fila->tipo_usuario
            );
        }
        return array($perfiles,$numero_filas);
    }
    
    public function listarPerfiles3($id_usuario) {
        $sql = "SELECT *,(SELECT pfus.id_perfil_usuario FROM perfil_usuario pfus WHERE pfus.id_usuario = $1 AND pfus.id_perfil = pf.id_perfil LIMIT 1) AS id_perfil_usuario FROM perfil pf"
                . " WHERE pf.estado_perfil = 'N' ORDER BY pf.nombre_perfil";
        $result = AccesoPostgres::obtener($sql,array($id_usuario));
        $perfiles = array();
        while ($fila = pg_fetch_object($result)) {
            $perfiles[] = array(
                "id_perfil"=>$fila->id_perfil,
                "nombre_perfil"=>$fila->nombre_perfil,
                "tipo_usuario"=>$fila->tipo_usuario,
                "id_perfil_usuario"=>$fila->id_perfil_usuario
            );
        }
        return $perfiles;
    }
    
}
