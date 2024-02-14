<?php
class Importador_model extends CI_Model {
    
    public function __construct()
    {
            // Call the CI_Model constructor
            parent::__construct();
    }
    
    //LISTADOS VARIOS
    public function inserto_proveedor($ob){
        $sql="select id from proveedores where cuit=?";
        $retorno=$this->db->query($sql, array($ob->cuit))->result();          
        $id=false;
        if($retorno){$id=$retorno[0]->id;}
        if(!$id){
            $sql="insert into proveedores(proveedor,cuit,iva,id_empresa,rz) 
            values(?,?,?,?,?)";
            $this->db->query($sql, array($ob->nombre,$ob->cuit,$ob->iva,1,$ob->nombre));
            $sql="SELECT max(id) as rta from proveedores";
            $retorno=$this->db->query($sql)->result();
           
        }
        else{
            $sql="SELECT id as rta from proveedores where cuit=?";
            $retorno=$this->db->query($sql,array($ob->cuit))->result();
           
        }
        return $retorno[0];

    }
    public function inserto_factura_compra($ob){
        $sql="select id_factura from facturas  where id_proveedor=? and puerto=? and numero=? and cod_afip=?";
        $retorno=$this->db->query($sql, array($ob->id_proveedor,$ob->pto,$ob->nro,$ob->tipo))->result();           
        $id=false;
        if($retorno){$id=$retorno[0]->id_factura;}
        if(!$id){
            $sql="insert into facturas (fecha,total,puerto,numero,cod_afip,neto,periodo_iva,id_empresa,
            excento,letra,tipo_comp,codigo_comp,id_proveedor,per_iva,per_ing_bto,per_ganancia
            ,con_nograv) 
             values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
           $r1=$this->db->query($sql, 
            array(
                $ob->fecha,
                $ob->total,
                $ob->pto,
                $ob->nro,
                $ob->tipo,               
                0,
                $ob->periodoiva,
                1,
                $ob->exento,                
                $ob->letra,     
                1,
                $ob->tipo,   
                $ob->id_proveedor,
                $ob->preretiva,
                $ob->perib,
                $ob->otrosimp,
                $ob->nogra));                               
        }        
        $cad="update facturas set id_tipo_comp=(select  id from cod_afip where id_iva_compra=1 and cod_afip_t='".$ob->tipo."' limit 1) 
        where id_proveedor=". $ob->id_proveedor   ." and 
        puerto=".   $ob->pto." and numero=".$ob->nro." and codigo_comp='". $ob->tipo ."'";
        $x=$this->db->query($cad);
        return 0;

    }
    public function inserto_factura_compra_ali($ali){
        $sql="update facturas set neto=neto+" . $ali->neto ." , iva=iva+". $ali->impuesto ."," ;
        $cad="";
        if($ali->alicuota=='0003'){ $cad=" iva0=". $ali->impuesto . ",neto0=".$ali->neto ;}
            #0003 0,00            
        if($ali->alicuota=='0004'){$cad=" iva105=".$ali->impuesto . ",neto105=".$ali->neto;   }            
            #0004 10,50 %
        if($ali->alicuota=='0005'){$cad=" iva21=".$ali->impuesto  . ",neto21=".$ali->neto;  }
            #0005 21,00 %
        if($ali->alicuota=='0006'){$cad=" iva27=".$ali->impuesto  . ",neto27=".$ali->neto;   }                
            #0006 27,00 %            
            #0008 5,00 %
            #0009 2,50  
        $sql=$sql.$cad . " where 
        puerto=".$ali->pto." and numero=".$ali->nro." and cod_afip='".$ali->tipo."' and 
         id_proveedor=(select id_proveedor from proveedores where cuit='".$ali->cuit."')";
      
      $x=$this->db->query($sql);
    }
          
}
?>
