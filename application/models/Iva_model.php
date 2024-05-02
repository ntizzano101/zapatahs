<?php
class Iva_model extends CI_Model {
    
    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
    }
    
    //LISTADOS VARIOS
    public function plan()
    {
        $sql="select * from plan order by cuenta";
        $retorno=$this->db->query($sql)->result();
        return $retorno;
    } 
    
    public function plan_buscar($cuenta)
    {
        $cuenta = "%".$cuenta."%";
        $sql="select * from plan  where cuenta like ? or nombre like ? order by cuenta";
        $retorno=$this->db->query($sql,array($cuenta,$cuenta))->result();
        return $retorno;
    } 

    public function plan_id($id)
    {        
        $sql="select * from plan  where id=?";
        $retorno=$this->db->query($sql,array($id))->result();
        return $retorno[0];
    } 

    public function plan_update($plan)
    {        
        $this->db->where('id', $plan->id);
        $this->db->update('plan', $plan);
    } 

    public function plan_insert($plan)
     {    
        
        $this->db->insert('plan', $plan);
    } 

    public function plan_delete($id)
    {        
        $this->db->query("delete from plan where id=?",array($id));
    } 
    public function plan_existe($cuenta,$id){
        $sql="select count(*) as g from plan  where cuenta=? and id<>?";
        $retorno=$this->db->query($sql,array($cuenta,$id))->result();
        return $retorno[0]->g>0;
    }
    public function compras($periodo,$empresa)
        {
            $sql="select p.proveedor,p.cuit,f.*,c.nombre,DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fechaf from facturas f 
            inner join proveedores p on f.id_proveedor=p.id
            inner join cod_afip c on c.id=f.id_tipo_comp
            where f.periodo_iva=? and f.id_empresa=? order by fecha 
            ";
            $retorno=$this->db->query($sql,array($periodo,$empresa))->result();
            return $retorno;
        } 
        public function ventas($periodo,$empresa)
        {
            $sql="select cl.cliente,cl.cuit,f.*,c.nombre,DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fechaf from facturas f 
            inner join clientes cl on cl.id=f.id_cliente
            inner join cod_afip c on c.id=f.id_tipo_comp
            where f.periodo_iva=? and f.id_empresa=? order by fecha 
            ";
            $retorno=$this->db->query($sql,array($periodo,$empresa))->result();
            return $retorno;
        } 
    public function lista_empresas()
        {
            $sql="SELECT id_empresa, razon_soc FROM empresas";
            $retorno=$this->db->query($sql)->result();
            return $retorno;
        }     
        
    public function lista_comprobantes($id_empresa,$id_cliente)
        {           
            $sql="SELECT DISTINCT id, cod_afip, cod_afip_t,cod_afip.nombre FROM cod_afip".
            " WHERE id_iva=(SELECT cond_iva FROM empresas WHERE id_empresa=?)".
            " AND id_iva_compra=(SELECT iva FROM clientes WHERE id=?)".
            "  ORDER BY cod_afip";
            $retorno=$this->db->query($sql, array($id_empresa, $id_cliente))->result();
            return $retorno;
        }    
        
    public function buscar_cliente($id)
        {
            $sql="SELECT a.*, b.cond_iva".
                " FROM clientes a".
                " INNER JOIN cdiva b ON a.iva=b.codigo". 
                " WHERE a.id=?";
            $retorno=$this->db->query($sql, array($id))->row();
            return $retorno;
        }        
    
    public function buscar_item($item)
        {   
            $item="%".trim(strtoupper($item))."%";
            $sql="SELECT *".
                " FROM articulos".
                " WHERE UPPER(codigo) LIKE ? OR  UPPER(articulo) LIKE ?";
            
            $retorno=$this->db->query($sql, array($item, $item))->result();
            return $retorno;
        }       
        
    public function buscar_un_item($id)
        {   
            $sql="SELECT *".
                " FROM articulos".
                " WHERE id_art = ?";
            
            $retorno=$this->db->query($sql, array($id))->row();
            return $retorno;
        }    
        
        
        
    //FACTURAS
    public function listado($b)
        {
            $sql="SELECT a.id_factura AS id".
                ", DATE_FORMAT(a.fecha, '%d/%m/%Y') AS fecha".
                ", b.cliente".
                ", a.puerto,a.numero,a.total,a.codigo_comp,a.letra,c.nombre,e.datos,ca.id_tipo_comp,a.id_comp_asoc".
                " FROM facturas a".
                " INNER JOIN clientes b ON a.id_cliente=b.id".
                " INNER JOIN empresas e ON a.id_empresa=e.id_empresa".
                " INNER JOIN cod_afip ca ON ca.id=a.id_tipo_comp".
                " LEFT JOIN (select distinct cod_afip_t,nombre from cod_afip) as  c on a.codigo_comp=c.cod_afip_t".
                " WHERE TRUE ";
            
            
            if(trim($b) !=""){
                $esFch=false;
                if (substr_count($b,"/")==2){
                    list($dia,$mes,$anio)= explode("/",$b);
                    if(is_numeric($dia) && is_numeric($mes) && is_numeric($anio) ){
                        if(checkdate($mes, $dia, $anio)){
                            $esFch=true;
                            $b=$anio."-".$mes."-".$dia;
                        }
                    }
                }
                
                if($esFch){
                    $sql.=" AND a.fecha=?";
                }else{
                    $b="%".trim(strtoupper($b))."%";
                    $sql.="  AND (UPPER(b.cliente) LIKE ? or  UPPER(e.datos) LIKE ?)";
                }
            }
             
            $sql.="  order by a.fecha desc ,a.cliente ,a.puerto,a.numero,a.codigo_comp,a.letra LIMIT 50";
            
            $retorno=$this->db->query($sql, array($b,$b))->result();
            if((is_array($retorno))){
                return $retorno;
            }
            else
            {
                return array();
            }
             
        }
        
    public function buscar($id)
        {
        $sql="SELECT a.*, DATE_FORMAT(fecha, '%d/%m/%Y') AS fc_format,".
            "SUBSTRING(a.per_iva, 1, 4) AS pi_anio, SUBSTRING(a.per_iva, 5,2) AS pi_mes,".    
            " b.razon_soc AS empresa,".
            " c.cliente AS clie_nombre, c.domicilio AS clie_dir, d.cond_iva AS prov_iva,".
            " e.cod_afip_t AS tp_comprob".    
            " FROM facturas a".
            " INNER JOIN empresas b ON a.id_empresa=b.id_empresa".
            " INNER JOIN clientes c ON a.id_cliente=c.id".  
            " INNER JOIN cdiva d ON c.iva=d.codigo". 
            " INNER JOIN cod_afip e ON a.id_tipo_comp=e.id".    
            " WHERE a.id_factura=?";
        $retorno=$this->db->query($sql, array($id))->row();
        return $retorno;
        } 

  public function posicion($peri){
    $mes=substr($peri,4,2);
    $ano=substr($peri,0,4);
    $f1=$ano.'-'. $mes .'-01';  
    if($mes==12){$ano++;$mes=1;}else{$mes++;}
    $f2=$ano.'-'. $mes .'-01';  

    //debito fiscal 
    //ventas 
    $sql1="select sum(case when tipo_comp=3 then -1*iva else iva end) as iva from facturas where periodo_iva=? and id_cliente > 0 ";    
    //Credito Fiscal
    //FACTURAS compras  
    $sql2="select sum(monto) as iva from opago_pago where rete_fecha >= ? and rete_fecha < ? and id_medio_pago=3";
    $sql3="select sum(case when tipo_comp=3 then -1*iva else iva end) as iva,
    sum(case when tipo_comp=3 then -1*per_iva else per_iva end) as  per_iva from facturas where periodo_iva = ? and id_proveedor > 0 ";
    $retorno1=$this->db->query($sql1, array($peri))->result();
    $retorno2=$this->db->query($sql2, array($f1,$f2))->result();
    $retorno3=$this->db->query($sql3, array($peri))->result();
    return array($retorno1,$retorno2,$retorno3);
  }   
  
  public function siap_compras($p1){
     $sql="select f.*,date_format(f.fecha,'%Y%m%d') as f2,p.proveedor,p.cuit from facturas f,proveedores p where periodo_iva=? and f.id_empresa=1 and p.id=f.id_proveedor";
     $ret=$this->db->query($sql, array($p1))->result();
     return $ret;
  }
  
 }
?>
