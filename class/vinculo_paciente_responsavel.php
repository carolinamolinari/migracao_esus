<?php
   
    require_once '../config/head.php';  
    

    class VinculoPacienteResponsavel{

        public function vinculo_paciente_responsavel(){

            //$registro = array();        
            //$nome_arquivo = 'nao_vinculados_paciente_responsavel.csv';
            //$cabecalho =  ["Co_seq domiciliar", "Area", "Microarea", "INE", "CNS Profissional", "Motivo"];

           
            $connectionFirebird = new Migracao();

            $dependentes = new Postgres();

            //Trazedo o resultado do select de responsaveis familiares identificados no firebird
            $lista_responsavel_familiar = $connectionFirebird->verifica_responsavel_familiar(); 
            $qtd_responsavel_familiar = sizeof($lista_responsavel_familiar);

            if($qtd_responsavel_familiar > 0){

                for($i = 0; $i < $qtd_responsavel_familiar; $i++){

                    $CO_SEQ_CDS_CAD_INDIVIDUAL_RESPONSAVEL = tratamento($lista_responsavel_familiar[$i]['CO_SEQ_CDS_CAD_INDIVIDUAL']);     
                    $ID_USUARIO_RESPONSAVEL = tratamento($lista_responsavel_familiar[$i]['ID_USUARIO']);
                    
                    //Pegando a lista de dependentes a partir da lista de responsaveis  
                    
                    $lista_dependentes = $dependentes->verifica_dependentes($CO_SEQ_CDS_CAD_INDIVIDUAL_RESPONSAVEL); 
                    $lista_dependentes = $dependentes->getDependentes();  
                    $qtd_dependentes = sizeof($lista_dependentes);

                    if($qtd_dependentes > 0){
                        
                        //Para cada dependente encontrado, executa o update
                        for($j = 0; $j < $qtd_dependentes; $j++){
                        
                            $CO_SEQ_CDS_CAD_INDIVIDUAL_DEPENDENTE = tratamento($lista_dependentes[$j]['co_seq_cds_cad_individual']); 
                    
                            //Se retornar um codigo para dependente, faz o update. 
                            //Se nao tiver isso ele fica atualizando todo mundo que nao tem o co_seq_cds_cad_individual = null

                            if($CO_SEQ_CDS_CAD_INDIVIDUAL_DEPENDENTE <> "NULL"){                          

                                $connectionFirebird->update_responsavel_familiar($CO_SEQ_CDS_CAD_INDIVIDUAL_DEPENDENTE, $ID_USUARIO_RESPONSAVEL);
                            
                            }//CRIAR O ELSE
                            
                        }
                    }
                }
            }
            //criaOutPut($nome_arquivo, $cabecalho, $registro); 
            header("location:../interface/interfaceEtapas2.php");
        }
    }

?>
        