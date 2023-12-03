<?php
   
    require_once '../config/head.php';    


    class CriaDomicilioEnderecoSemDom{
       

        public function cria_domicilio_end_sem_dom(){

            $connectionFirebird = new Migracao();

            $lista_enderecos_sem_domicilio = $connectionFirebird->busca_pac_end_sem_domicilio(); //busca paciente com endereço e sem domicilio  
            $qtd_enderecos_sem_domicilio = sizeof($lista_enderecos_sem_domicilio);

            if($qtd_enderecos_sem_domicilio > 0){
                        
                
                for($i = 0; $i < $qtd_enderecos_sem_domicilio; $i++){

                    $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($lista_enderecos_sem_domicilio[$i]['ID_LOGRADOURO_BAIRRO_NUMERO']);
                    
                    $NO_COMPLEMENTO = tratamento($lista_enderecos_sem_domicilio[$i]['NO_COMPL_LOGRADOURO']);

                    $endereco = $connectionFirebird->verifica_endereco_completo($ID_LOGRADOURO_BAIRRO_NUMERO);  
                        
                
                    if(!empty($endereco[0]['CD_DOMICILIO'])){

                        $CD_DOMICILIO = tratamento($endereco[0]['CD_DOMICILIO']);                        

                        $INSERE_DOMICILIO = $connectionFirebird->insere_enereco_soc_domicilio($NO_COMPLEMENTO, $ID_LOGRADOURO_BAIRRO_NUMERO);

                    }
                }
            
            }

            header("location:interfaceEtapas.php");
        }

     

            
    }


?>