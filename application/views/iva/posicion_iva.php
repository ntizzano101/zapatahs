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
                <div class="panel-heading">POSICION IVA</div>     
                <form class="navbar-form navbar-left" role="search" method="POST" action="<?php echo base_url(); ?>iva/posicion">                                           
                Periodo   <input type="text" class="form-control" name="peri" value="<?=$peri?>" id="peri" placeholder="aaaamm..">
                <button type="submit" class="btn btn-primary">Filtrar</button>	
                </form>
                <table class="table">
                  <thead>
                        <tr>
                          <th>Concepto</th>
                          <th align="right">Debe</th>
                          <th align="right">Haber</th>
                          <th align="right">Saldo</th>                          
                        </tr>
                  </thead>
                  <tbody>
                        <?php 
                        $total=$debito1[0]->iva;
                        //debito1
                        ?>	
                                <tr>
                                    <td>IVA Ventas</td>
                                    <td align="right"><?=number_format($debito1[0]->iva,2,".",","); ?></td>                              
                                    <td align="right">0.00</td>                                    
                                    <td align="right"><?=number_format($total,2,".",",")?></td>                                    
                                </tr>
                                <?php $total=$total-$credito2[0]->iva; ?>
                                <tr>
                                    <td>IVA Compras</td>                                                        
                                    <td align="right">0.00</td>
                                    <td align="right"><?=number_format($credito2[0]->iva,2,".",","); ?></td>         
                                    <td align="right"><?=number_format($total,2,".",",")?></td>                                    
                                </tr>
                                <?php $total=$total-$credito1[0]->iva; ?>
                                <tr>
                                    <td>IVA Retenciones</td>                                                        
                                    <td align="right">0.00</td>
                                    <td align="right"><?=number_format($credito1[0]->iva,2,".",","); ?></td>                                                                                                    
                                    <td align="right"><?=number_format($total,2,".",",")?></td>                                    
                                </tr>
                                <?php $total=$total-$credito2[0]->per_iva; ?>                               
                                <tr>
                                    <td>IVA Percepciones</td>                                                        
                                    <td align="right">0.00</td>
                                    <td align="right"><?=number_format($credito2[0]->per_iva,2,".",","); ?></td>                                             
                                    <td align="right"><?=number_format($total,2,".",",")?></td>                                    
                                </tr>
                        

                  </tbody>
                </table>
            </div>
        </div>
    </div>
</div>    