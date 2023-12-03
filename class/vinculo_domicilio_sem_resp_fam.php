<?php
   
    require_once '../config/head.php';    


    class VinculoDomicilioSemRespFam{
       
        public function vinculo_domicilio_sem_resp_fam(){
            $connectionFirebird = new Migracao();

            //domicilios sem responsavel 
            $lista_domicilio_sem_resp = $connectionFirebird->verifica_domicilio_sem_resp(); 
            $qtd_domicilio_sem_resp = sizeof($lista_domicilio_sem_resp);

            
            if($qtd_domicilio_sem_resp > 0){

                for($i = 0; $i < $qtd_domicilio_sem_resp; $i++){

                    $CD_DOMICILIO = tratamento($lista_domicilio_sem_resp[$i]['CD_DOMICILIO']);

                    $VERIFICA_DOMICILIOS_SEM_RESP = $connectionFirebird->contagem_domicilio_sem_resp($CD_DOMICILIO);

                    if(!empty($VERIFICA_DOMICILIOS_SEM_RESP)){
                        
                        $connectionFirebird->update_cad_ind_resp_fam($CD_DOMICILIO);
                    }
                }
            }

            header("location:../interface/interfaceEtapas2.php");
        }
    }

?>