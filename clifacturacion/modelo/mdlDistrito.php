<?php
include_once '../cado/AccesoPostgres.php';

class mdlDistrito extends AccesoPostgres{
    
    public function __construct() {
        if(empty(parent::getConexion())){
            parent::__construct();
        }else{
            $this->setConexion(parent::getConexion());
        }
    }

    public function listarDistritos() {
        $sql = "SELECT ds.id AS ubigeo, ds.name AS nombre_distrito, pv.name AS nombre_provincia, dp.name AS nombre_departamento FROM ubigeo_peru_districts ds JOIN ubigeo_peru_provinces pv ON ds.province_id = pv.id JOIN ubigeo_peru_departments dp ON pv.department_id = dp.id";
        $sql_where = " WHERE TRUE";
        $sql_order = " ORDER BY ds.id ASC";
        $n=1;
        $parametros = array();
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $distritos = array();
        while ($fila = pg_fetch_object($result)) {
            $distritos[] = array(
                "ubigeo"=>$fila->ubigeo,
                "nombre"=>"{$fila->ubigeo} - {$fila->nombre_departamento} - {$fila->nombre_provincia} - {$fila->nombre_distrito}",
            );
        }
        return $distritos;
    }

    public function verDistrinto($ubigeo) {
        $sql = "SELECT ds.id AS ubigeo, ds.name AS nombre_distrito, pv.name AS nombre_provincia, dp.name AS nombre_departamento FROM ubigeo_peru_districts ds JOIN ubigeo_peru_provinces pv ON ds.province_id = pv.id JOIN ubigeo_peru_departments dp ON pv.department_id = dp.id";
        $sql_where = " WHERE ds.id = $1";
        $sql_order = " ORDER BY ds.id ASC";
        $parametros = array($ubigeo);
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $distrito = array();
        while ($fila = pg_fetch_object($result)) {
            $distrito = array(
                "ubigeo"=>$fila->ubigeo,
                "nombre"=>"{$fila->ubigeo} - {$fila->nombre_departamento} - {$fila->nombre_provincia} - {$fila->nombre_distrito}",
            );
            return $distrito;
        }
    }
    
    public function listarDistrito($id_provincia) {
        $sql = "SELECT * FROM distrito ds";
        $sql_where = " WHERE TRUE";
        $sql_order = " ORDER BY ds.nombre_distrito";
        $n=1;
        $parametros = array();
        if(!empty($id_provincia) && strlen($id_provincia)>0 && $id_provincia>0){ $sql_where.= " AND ds.id_provincia = $$n";$parametros[]=$id_provincia;$n++;}
        $sql = $sql . $sql_where .$sql_order;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $distritos = array();
        while ($fila = pg_fetch_object($result)) {
            $distritos[] = array(
                "id_distrito"=>$fila->id_distrito,
                "nombre_distrito"=>$fila->nombre_distrito,
                "id_provincia"=>$fila->id_provincia
            );
        }
        return $distritos;
    }
    
    public function listarDistrito2($nombre,$departamento,$provincia,$OFFSET,$LIMIT) {
        $OFFSET = ($OFFSET - 1) * $LIMIT;
        $sql = "SELECT * FROM distrito ds LEFT JOIN provincia pv ON ds.id_provincia = pv.id_provincia LEFT JOIN departamento dp ON pv.id_departamento = dp.id_departamento";
        $sql_count = "SELECT count(DISTINCT ds.id_distrito) as numero_filas FROM distrito ds LEFT JOIN provincia pv ON ds.id_provincia = pv.id_provincia LEFT JOIN departamento dp ON pv.id_departamento = dp.id_departamento";
        $parametros = array();
        $n = 1;
        $sql_order = " ORDER BY dp.nombre_departamento,pv.nombre_provincia,ds.nombre_distrito";
        $sql_limit = " OFFSET ".$OFFSET." LIMIT ".$LIMIT;
        $sql_where = " WHERE TRUE";
        if(!empty($nombre) && strlen($nombre)>0){ $sql_where.= " AND ds.nombre_distrito ILIKE $$n";$parametros[] = "%$nombre%";$n++;}
        if(!empty($departamento) && strlen($departamento)>0 && $departamento>0){ $sql_where.= " AND pv.id_departamento = $$n";$parametros[] = $departamento;$n++;}
        if(!empty($provincia) && strlen($provincia)>0 && $provincia>0){ $sql_where.= " AND ds.id_provincia = $$n";$parametros[] = $provincia;$n++;}
        $sql = $sql . $sql_where . $sql_order . $sql_limit;
        $result = AccesoPostgres::obtener($sql,$parametros);
        $sql_count = $sql_count.$sql_where;
        $result2 = AccesoPostgres::obtener($sql_count,$parametros);
        while ($fila = pg_fetch_object($result2)) {
            $numero_filas = $fila->numero_filas;
        }
        $distritos = array();
        while ($fila = pg_fetch_object($result)) {
            $distritos[] = array(
                "id_distrito"=>$fila->id_distrito,
                "nombre_distrito"=>$fila->nombre_distrito,
                "id_provincia"=>$fila->id_provincia,
                "nombre_departamento"=>$fila->nombre_departamento,
                "nombre_provincia"=>$fila->nombre_provincia
            );
        }
        return array($distritos,$numero_filas);
    }
    
}
