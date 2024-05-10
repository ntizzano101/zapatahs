<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" /> 
<style>
.container a:hover, a:visited, a:link, a:active{
    text-decoration: none;
}   
</style>
<div class="container">  
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">RETENCIONES</div>     
                <form class="navbar-form navbar-left" role="search" method="POST" action="<?php echo base_url(); ?>retenciones">                           
                Tipo
                <?php
                $this->load->helper('form');
                      $options = [
                    '3'  => 'Retencion IVA',
                    '4'    => 'Retencion GANAN.',
                    '5'  => 'Retencion ING.BTOS',
                    '8' => 'Retencion SUS',
                ];             
                echo form_dropdown('tipo', $options, $tipo,'class="form-control"');
                ?>
                Fecha Desde<input type="date" class="form-control" name="fdesde" value="<?=$fdesde?>">
                Fecha Hasta<input type="date" class="form-control" name="fhasta" value="<?=$fhasta?>">
                <button type="submit" class="btn btn-primary">Filtrar</button>	
                <a href="<?php echo base_url(); ?>/retenciones/exportar/1/<?=$fdesde?>/<?=$fhasta?>/<?=$tipo?>" target="blank_" class="btn btn-default">Excel(.)</a>			
                <a href="<?php echo base_url(); ?>/retenciones/exportar/2/<?=$fdesde?>/<?=$fhasta?>/<?=$tipo?>" target="blank_" class="btn btn-default">Excel(,)</a>			
                
                </form>
                <table class="table">
                  <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Cliente</th>
                          <th>Cuit</th>
                          <th>Comprobante</th>
                          <th>Total</th>
                        </tr>
                  </thead>
                  <tbody>
                        <?php 
                        $total=0;
                        foreach($retenciones as $cta){                 
                            $total=$total + $cta->monto;
                            ?>	
                                <tr>
                                    <td><?=$cta->rete_fecha ?></td>
                                    <td><?=$cta->cliente ?></td>                              
                                    <td><?=$cta->cuit ?></td>
                                    <td><?=$cta->nro_comprobante ?></td>                                                                        
                                    <td align="right"><?=number_format($cta->monto,2,".",",")?></td>                                    
                                </tr>
                        <?php	
                        }                        
                        ?>                        
                        <tr>
                            <td colspan="4"></td>
                            <td  align="right"><?=number_format($total,2,".",",")?></td>                         

                        </tr>

                  </tbody>
                </table>
            </div>
        </div>
    </div>
</div>    