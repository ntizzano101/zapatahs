<?php
class Retenciones_model extends CI_Model {
    
    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
    }
    ##ARTICULOS
    
    
    public function listado($tipo,$f1,$f2)
        {
            $sql="SELECT  DATE_FORMAT(r.rete_fecha, '%d/%m/%Y') AS rete_fecha,r.nro_comprobante,r.monto,c.cliente,c.cuit FROM 
            opago_pago r inner join opago o on r.id_pago=o.id
            inner join clientes c on o.id_cliente=c.id
            where r.id_medio_pago=? and rete_fecha between ? and ?
            ";            
            $retorno=$this->db->query($sql,array($tipo,$f1,$f2))->result();
            return $retorno;
        } 
    }   
        
  ?>