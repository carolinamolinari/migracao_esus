<?php
   
    require_once '../config/head.php';    


    class VinculoPacienteUnidade{

        public function vinculo_paciente_unidade(){

          
            $connectionFirebird = new Migracao();

            //$connectionFirebird->testeff();


            $lista_pacientes_sem_unidade = $connectionFirebird->busca_paciente_sem_unidade(); 
            $qtd_pacientes_sem_unidade = sizeof($lista_pacientes_sem_unidade);
          
            
            for($i = 1; $i < $qtd_pacientes_sem_unidade; $i++){

                $CD_USUARIO_SUS = tratamento($lista_pacientes_sem_unidade[$i]['CD_USUARIO_SUS']);
                $CD_CAD_INDIVIDUAL = $lista_pacientes_sem_unidade[$i]['CD_CAD_INDIVIDUAL'];
                $NR_UNIDADE = $lista_pacientes_sem_unidade[$i]['ID_UNIDADE_CIDADAO'];

                if($CD_CAD_INDIVIDUAL){
                    $connectionFirebird->update_sus_complemento_unidade($NR_UNIDADE, $CD_USUARIO_SUS);
                }
                
            }

            header("location:../interface/interfaceEtapas2.php");
        } 
    }

?>