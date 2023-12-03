<?php
   
    require_once '../config/head.php';    


    class DeletaEnderecoSemUso{
       
        public function deleta_endereco_sem_uso(){

            $connectionFirebird = new Migracao();

            $lista_enderecos_sem_vinculo = $connectionFirebird->busca_enderecos_sem_vinculo(); 
            $qtd_enderecos_sem_vinculo = sizeof($lista_enderecos_sem_vinculo);
            
            for($i = 0; $i < $qtd_enderecos_sem_vinculo; $i++){            
    
                $ID_LOGRADOURO_CEP = tratamento($lista_enderecos_sem_vinculo[$i]['ID_LOGRADOURO_CEP']);
                $ID_LOGRADOURO_ENDERECO = tratamento($lista_enderecos_sem_vinculo[$i]['ID_LOGRADOURO_ENDERECO']);
                $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($lista_enderecos_sem_vinculo[$i]['ID_LOGRADOURO_BAIRRO_NUMERO']); 
    
                $QUANTIDADE_LOGR_CEP_LBN = $connectionFirebird->contagem_lbn_cep($ID_LOGRADOURO_CEP);            
    
               if($QUANTIDADE_LOGR_CEP_LBN == 1){
    
                    $connectionFirebird->delete_logr_cep($ID_LOGRADOURO_CEP); 
    
                }
    
                $connectionFirebird->delete_lbn($ID_LOGRADOURO_BAIRRO_NUMERO); 
    
                $connectionFirebird->delete_endereco(); 
    
                $connectionFirebird->delete_bairro(); 
    
            }

            header("location:interfaceEtapas.php");
        }

    }


?>