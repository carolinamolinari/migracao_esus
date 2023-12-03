<?php
   
    require_once '../config/head.php';    


    class VinculoCadIndEquipe{

        public function vinculo_paciente_equipe(){

            $registro = array();        
            $nome_arquivo = 'nao_vinculados_paciente_equipe.csv';
            $cabecalho =  ["Nome","Nome da Mae", "Data de Nascimento", "Co_seq", "Nome do Profissional", "CNS", "Area", "Microarea","INE", "Motivo"];
            
            $pacienteEquipe = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->busca_profissionais_equipe_paciente();

        
            $lista_profissionais_equipe = $connectionPostgres->getProfissionaisEquipePaciente(); 
            $qtd_profissionais_equipe = sizeof($lista_profissionais_equipe);

            for($i = 0; $i < $qtd_profissionais_equipe; $i++){ 

                $CO_SEQ_CDS_CAD_INDIVIDUAL = tratamento($lista_profissionais_equipe[$i]['co_seq_cds_cad_individual']);      
                $MICROAREA = tratamento($lista_profissionais_equipe[$i]['cd_microarea']);
                $AREA = tratamento($lista_profissionais_equipe[$i]['cd_area']);
                $INE = tratamento($lista_profissionais_equipe[$i]['cd_ine']);
                $NU_CNS = tratamento($lista_profissionais_equipe[$i]['nu_cns']);

                $NO_CIDADAO = tratamento($lista_profissionais_equipe[$i]['no_cidadao']); 
                $NO_MAE = tratamento($lista_profissionais_equipe[$i]['no_mae_cidadao']);
                $DT_NASCIMENTO = tratamento($lista_profissionais_equipe[$i]['dt_nascimento']); 


                //Busca o profissional na rkm a partir do numero do CNS
                $lista_equipe_rkm = $pacienteEquipe->busca_profissional_equipe($NU_CNS);
                
                
                if(!empty($lista_equipe_rkm[0]['CD_USUARIO_SISTEMA'])){

                    $RESP_CADASTRO = tratamento($lista_equipe_rkm[0]['CD_USUARIO_SISTEMA']);
                    $ID_ATB_MICROAREA = tratamento($lista_equipe_rkm[0]['ID_ATB_MICROAREA']);
                    $ID_ATB_AREA = tratamento($lista_equipe_rkm[0]['ID_ATB_AREA']);
                    $CD_EQUIPE = tratamento($lista_equipe_rkm[0]['CD_EQUIPE']);
                    $UNIDADE_RESP_CAD = tratamento($lista_equipe_rkm[0]['ID_UNIDADE']);
                    $INE = tratamento($lista_equipe_rkm[0]['INE']);
                    //$NR_CNES = tratamento($lista_equipe_rkm[0]['NR_CNES']);
                    $CBO_PROFISSIONAL = tratamento($lista_equipe_rkm[0]['CBO']);
                    $CNES_UNIDADE = tratamento($lista_equipe_rkm[0]['NR_CNES']);

                    if(empty($ID_ATB_MICROAREA)){
                        $FORA_AREA = 1;
                        $ID_ATB_MICROAREA = "NULL";
                    }else{
                        $FORA_AREA = 0;
                    }                    
                    if(empty($ID_MICROAREA_CIDADAO)){
                        $ID_MICROAREA_CIDADAO = "NULL";
                        
                    }

                    //Se o profissional responsavel pelo cadastro do paciente no banco de origem estiver devidamente cadastrado no banco de destino, 
                    //ocorre a atualização dos dados de equipe do cidadao nao banco de destino
                    
                    if($RESP_CADASTRO){
                        
                        $pacienteEquipe->update_atb_cad_ind_equipe ($CD_EQUIPE, $ID_ATB_AREA, $ID_ATB_MICROAREA, $RESP_CADASTRO, $UNIDADE_RESP_CAD, 
                                                    $INE, $CBO_PROFISSIONAL, $CNES_UNIDADE, $FORA_AREA, $CO_SEQ_CDS_CAD_INDIVIDUAL);

                    }
                    else{
                       
                        
                        $dados = ["Nome" => $NO_CIDADAO,
                                "Nome da Mae" => $NO_MAE,
                                "Data de Nascimento" => $DT_NASCIMENTO,
                                "Co_seq" => $CO_SEQ_CDS_CAD_INDIVIDUAL,
                                

                                "CNS" => $NU_CNS, 
                                "Area" => $AREA, 
                                "Microarea" => $MICROAREA,
                                "INE" => $INE
                    
                        ];

                        $motivo = "Nenhum respnsável associado ao Paciente";
                        $registro[] = registrosNaoMigrados($dados, acento($motivo));
                    }

                    
                }
                else{

                    $dados = ["Nome" => $NO_CIDADAO,
                                "Nome da Mae" => $NO_MAE,
                                "Data de Nascimento" => $DT_NASCIMENTO,
                                "Co_seq" => $CO_SEQ_CDS_CAD_INDIVIDUAL,
                                
                                "CNS" => $NU_CNS, 
                                "Area" => $AREA, 
                                "Microarea" => $MICROAREA,
                                "INE" => $INE
                    
                        ];

                    $motivo = "Profissional não encontrado com as características";
                    $registro[] = registrosNaoMigrados($dados, acento($motivo));

                }
               
            } 
            criaOutPut($nome_arquivo, $cabecalho, $registro); 
            header("location:../interface/interfaceEtapas2.php");
        }            
    }

?>