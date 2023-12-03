<?php
   
    require_once '../config/head.php';    

    class DeletaDomicilioSemUso{

        public function deleta_domicilio_sem_uso(){

            $connectionFirebird = new Migracao();
            
            $connectionFirebird->delete_domicilio_sem_paciente();

            $connectionFirebird->update_resp_fam_domicilio_sem_paciente();

            
            header("location:interfaceEtapas.php");
        }
            
    }

?>