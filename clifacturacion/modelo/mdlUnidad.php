<?php
include_once '../cado/AccesoPostgres.php';

class mdlUnidad extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }
    
    public function insertarUnidad($nombre,$codigo) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "INSERT INTO unidad(nombre_unidad,codigo_unidad) VALUES ($1,$2)";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$codigo));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function actualizarUnidad($id_unidad,$nombre,$codigo) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE unidad SET nombre_unidad = $1,  codigo_unidad = $2 WHERE id_unidad = $3";
        $result = AccesoPostgres::ejecutar($sql,array($nombre,$codigo,$id_unidad));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function eliminarUnidad($id_unidad) {
        AccesoPostgres::iniciarTransaccion();
        $sql = "UPDATE unidad SET estado_unidad = 'A' WHERE id_unidad = $1";
        $result = AccesoPostgres::ejecutar($sql,array($id_unidad));
        if(!$result){
            AccesoPostgres::abortarTransaccion();
            throw new Exception("ERROR EN LA CONSULTA");
        }else{
            AccesoPostgres::finalizarTransaccion();
        }
    }
    
    public function verUnidad($id_unidad) {
        $sql = "SELECT * FROM unidad md WHERE id_unidad = $1";
        $result = AccesoPostgres::obtener($sql,array($id_unidad));
        $menu = array();
        while ($fila = pg_fetch_object($result)) {
            $menu = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "codigo_unidad"=>$fila->codigo_unidad,
            );
        }
        return $menu;
    }
    
    public function verUnidad2($codigo_unidad) {
        $sql = "SELECT * FROM unidad md WHERE codigo_unidad = $1 AND estado_unidad = 'N'";
        $result = AccesoPostgres::obtener($sql,array($codigo_unidad));
        $menu = array();
        while ($fila = pg_fetch_object($result)) {
            $menu = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "codigo_unidad"=>$fila->codigo_unidad,
            );
        }
        return $menu;
    }
    
    public function listarUnidades() {
        $sql = "SELECT * FROM unidad sm";
        $sql_where = " WHERE estado_unidad = 'N'";
        $sql_order = " ORDER BY sm.nombre_unidad";
        $n=1;
        $parametros = array();
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $unidades = array();
        while ($fila = pg_fetch_object($result)) {
            $unidades[] = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "codigo_unidad"=>$fila->codigo_unidad,
            );
        }
        return $unidades;
    }
    
    public function listarUnidades2($nombre,$codigo,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM unidad sm";
        $sql_count = "SELECT count(DISTINCT sm.id_unidad) as numero_filas FROM unidad sm";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY sm.nombre_unidad";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_unidad = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND sm.nombre_unidad ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($codigo) && strlen($codigo)>0){ $sql_where.= " AND sm.codigo_unidad ILIKE $$n";$parametros[] = "%$codigo%";$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $unidades = array();
        while ($fila = pg_fetch_object($result)) {
            $unidades[] = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "codigo_unidad"=>$fila->codigo_unidad,
            );
        }
        return array($unidades,$numero_filas);
    }
    
    public function listarUnidades3($id_perfil) {
        $sql = "SELECT sm.*,mn.* FROM unidad sm"
                . " INNER JOIN perfil_unidad pfsm ON sm.id_unidad = pfsm.id_unidad"
                . " INNER JOIN menu mn ON sm.id_menu = mn.id_menu";
        $sql_where = " WHERE sm.estado_unidad = 'N'";
        $sql_order = " ORDER BY mn.orden_menu,mn.id_menu,sm.orden_unidad";
        $n=1;
        $parametros = array();
        if(!empty($id_perfil) && strlen($id_perfil)>0 && $id_perfil>0){ $sql_where.= " AND pfsm.id_perfil = $$n";$parametros[]=$id_perfil;$n++;}
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $unidades = array();
        while ($fila = pg_fetch_object($result)) {
            $unidades[] = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "ruta_unidad"=>$fila->ruta_unidad,
                "icono_unidad"=>$fila->icono_unidad,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu
            );
        }
        return $unidades;
    }
    
    public function listarUnidades4($nombre,$perfil,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM unidad sm INNER JOIN perfil_unidad pfsm ON sm.id_unidad = pfsm.id_unidad";
        $sql_count = "SELECT count(DISTINCT sm.id_unidad) as numero_filas FROM unidad sm INNER JOIN perfil_unidad pfsm ON sm.id_unidad = pfsm.id_unidad";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY mn.nombre_perfil,sm.nombre_unidad";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE estado_unidad = 'N'";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND sm.nombre_unidad ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($menu) && strlen($menu)>0 && $menu>0){ $sql_where.= " AND sm.id_menu = $$n";$parametros[] = $menu;$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $unidades = array();
        while ($fila = pg_fetch_object($result)) {
            $unidades[] = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "ruta_unidad"=>$fila->ruta_unidad,
                "icono_unidad"=>$fila->icono_unidad,
                "orden_unidad"=>$fila->orden_unidad,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu
            );
        }
        return array($unidades,$numero_filas);
    }
    
    public function listarUnidades5($id_perfil) {
        $sql = "SELECT sm.*,mn.*,(SELECT pfsm.id_perfil_unidad FROM perfil_unidad pfsm WHERE pfsm.id_perfil = $1 AND pfsm.id_unidad = sm.id_unidad) AS id_perfil_unidad FROM unidad sm"
                . " INNER JOIN menu mn ON sm.id_menu = mn.id_menu";
        $sql_where = " WHERE sm.estado_unidad = 'N'";
        $sql_order = " ORDER BY mn.orden_menu,mn.id_menu,sm.orden_unidad";
        $n=1;
        $parametros = array();
        $parametros[]=$id_perfil;$n++;
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $unidades = array();
        while ($fila = pg_fetch_object($result)) {
            $unidades[] = array(
                "id_unidad"=>$fila->id_unidad,
                "nombre_unidad"=>$fila->nombre_unidad,
                "ruta_unidad"=>$fila->ruta_unidad,
                "icono_unidad"=>$fila->icono_unidad,
                "id_menu"=>$fila->id_menu,
                "nombre_menu"=>$fila->nombre_menu,
                "id_perfil_unidad"=>$fila->id_perfil_unidad
            );
        }
        return $unidades;
    }
    
}
