<?php
   
    require_once '../config/head.php';    


    class CriaDomicilioPacienteSemDom{
       
       

        public function cria_domicilio_pac_sem_dom(){

            $connectionFirebird = new Migracao();

            $lista_pacientes_sem_domicilio = $connectionFirebird->busca_pac_end_sem_domicilio(); 
            $qtd_pacientes_sem_domicilio = sizeof($lista_pacientes_sem_domicilio);

            if($qtd_pacientes_sem_domicilio > 0){

                for($i = 0; $i < $qtd_pacientes_sem_domicilio; $i++){

                    $CD_USUARIO_SUS = tratamento($lista_pacientes_sem_domicilio[$i]['CD_USUARIO_SUS']);
                    $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($lista_pacientes_sem_domicilio[$i]['ID_LOGRADOURO_BAIRRO_NUMERO']);   

                    $endereco = $connectionFirebird->verifica_endereco_completo($ID_LOGRADOURO_BAIRRO_NUMERO); 
                       
                    if(!empty($endereco[0]['CD_DOMICILIO'])){ 

                            $CD_DOMICILIO = tratamento($endereco[0]['CD_DOMICILIO']);

                            $ATUALIZA_DOMICILIO_SUS_COMPLEMENTO = $connectionFirebird->update_sus_complemento_domicilio($CD_DOMICILIO, $CD_USUARIO_SUS);
                            //print_r($ATUALIZA_DOMICILIO_SUS_COMPLEMENTO);
                    }                   
                }
            }

  
            //header("location:interfaceEtapas.php");
        }

    }
?>