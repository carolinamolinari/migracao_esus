<?php
   
    require_once '../config/head.php';    


    class VinculoPacienteSemDomicilio{

        public function vinculo_paciente_sem_domicilio(){
            
            $connectionFirebird = new Migracao();

            $lista_pacientes_sem_domicilio = $connectionFirebird->busca_pac_end_sem_domicilio(); 
            $qtd_pacientes_sem_domicilio = sizeof($lista_pacientes_sem_domicilio);

            if($qtd_pacientes_sem_domicilio > 0){
                for($i = 0; $i < $qtd_pacientes_sem_domicilio; $i++){
                    $CD_USUARIO_SUS = tratamento($lista_pacientes_sem_domicilio[$i]['CD_USUARIO_SUS']);
                    $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($lista_pacientes_sem_domicilio[$i]['ID_LOGRADOURO_BAIRRO_NUMERO']);

                    $endereco_completo = $connectionFirebird->verifica_endereco_completo($ID_LOGRADOURO_BAIRRO_NUMERO);            
                
                    if(!empty($endereco_completo[0]['CD_DOMICILIO'])){
                        $CD_DOMICILIO = tratamento($endereco_completo[0]['CD_DOMICILIO']);
                        $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($endereco_completo[0]['ID_LOGRADOURO_BAIRRO_NUMERO']);
                        $ID_TIPO_LOGRADOURO = tratamento($endereco_completo[0]['ID_TIPO_LOGRADOURO']);
                        $NO_LOGRADOURO = tratamento($endereco_completo[0]['NO_LOGRADOURO']);
                        $NR_LOGRADOURO = tratamento($endereco_completo[0]['NR_LOGRADOURO'], 7);
                        $NO_COMPL_LOGRADOURO = tratamento($endereco_completo[0]['NO_COMPL_LOGRADOURO']);
                        $NO_BAIRRO = tratamento($endereco_completo[0]['NO_BAIRRO']);
                        $CD_CEP = tratamento($endereco_completo[0]['CD_CEP']);

                        $ATUALIZA_DOMICILIO_SUS_COMPLEMENTO = $connectionFirebird->update_sus_complemento_domicilio_completo($CD_DOMICILIO, $ID_LOGRADOURO_BAIRRO_NUMERO, $ID_TIPO_LOGRADOURO, 
                                                                $NO_LOGRADOURO, $NR_LOGRADOURO, $NO_COMPL_LOGRADOURO, $NO_BAIRRO, $CD_CEP, $CD_USUARIO_SUS);
                        

                    }

                }
            }

            header("location:../interface/interfaceEtapas2.php");
        }
    }

?>