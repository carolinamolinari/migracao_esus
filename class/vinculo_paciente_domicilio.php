<?php
   
    require_once '../config/head.php';    

    class VinculoPacienteDomicilio{

        public function vinculo_paciente_domicilio(){

            $registro = array();        
            $nome_arquivo = 'nao_vinculados_paciente_domicilio.csv';
            $cabecalho =  ["NOME RESPONSAVEL", "NOME MAE RESPONSAVEL", "DATA NASCIMENTO RESPONSAVEL", "CO_SEQ_CDS_CAD_DOMICILIAR", "CO_SEQ_CDS_CAD_INDIVIDUAL", "CD_DOMICILIO", "ID_LOGRADOURO_BAIRRO_NUMERO", "NO_LOGRADOURO",
            "NO_COMPL_LOGRADOURO", "NR_LOGRADOURO", "NO_BAIRRO", "CD_CEP", "CD_USUARIO_SUS_RESP", "CD_USUARIO_SUS_DEPENDENTE", "MOTIVO"];
            

            $vinculoDomiciliar = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->vinculo_domicilio_familia();

            $lista_domicilio_familia = $connectionPostgres->getDomicilioFamilia(); 

            $qtd_domicilio_familia = sizeof($lista_domicilio_familia);

            for($i = 0; $i < $qtd_domicilio_familia; $i++){

                $CO_SEQ_CDS_CAD_DOMICILIAR = tratamento($lista_domicilio_familia[$i]['co_seq_cds_cad_domiciliar']); 
                $CO_SEQ_CDS_CAD_INDIVIDUAL = tratamento($lista_domicilio_familia[$i]['co_seq_cds_cad_individual']);

                $NO_CIDADAO_RESPONSAVEL = tratamento($lista_domicilio_familia[$i]['no_cidadao']); 
                $NO_MAE_CIDADAO_RESPONSAVEL = tratamento($lista_domicilio_familia[$i]['no_mae_cidadao']);
                $DT_NASCIMENTO_RESPONSAVEL = tratamento($lista_domicilio_familia[$i]['dt_nascimento']); 
        

                //$ST_MUDANCA = tratamento($lista_domicilio_familia[$i]['st_mudanca']);
                
                $lista_domicilio_rkm = $vinculoDomiciliar->verifica_dados_domicilio($CO_SEQ_CDS_CAD_DOMICILIAR);
                $qtd_domicilio = sizeof($lista_domicilio_rkm);
                    
                //Para cada dependente encontrado, executa o update
                for($j = 0; $j < $qtd_domicilio; $j++){

                    $CD_DOMICILIO = tratamento($lista_domicilio_rkm[$j]['CD_DOMICILIO']);

                    if($CD_DOMICILIO <> "NULL"){

                        $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($lista_domicilio_rkm[$j]['ID_LOGRADOURO_BAIRRO_NUMERO']);
                        $ID_TIPO_LOGRADOURO = tratamento($lista_domicilio_rkm[$j]['ID_TIPO_LOGRADOURO']);
                        $NO_LOGRADOURO = tratamento($lista_domicilio_rkm[$j]['NO_LOGRADOURO']);
                        $NR_LOGRADOURO = tratamento($lista_domicilio_rkm[$j]['NR_LOGRADOURO'], 7);
                        $NO_COMPL_LOGRADOURO = tratamento($lista_domicilio_rkm[$j]['NO_COMPL_LOGRADOURO']);
                        $NO_BAIRRO = tratamento($lista_domicilio_rkm[$j]['NO_BAIRRO']);
                        $CD_CEP = tratamento($lista_domicilio_rkm[$j]['CD_CEP']);

                        //pega o responsavel pelo domicilio baseado nas infos do postgres
                        $CD_USUARIO_SUS_RESP = tratamento($vinculoDomiciliar->verifica_id_usuario($CO_SEQ_CDS_CAD_INDIVIDUAL)); 

                        //Atualizando na sus_complemento o cd_domicilio do responsavel
                        $ATUALIZA_SUS_COMP =  tratamento($vinculoDomiciliar->update_sus_comp_domicilio($CD_DOMICILIO, $ID_LOGRADOURO_BAIRRO_NUMERO, 
                                                        $ID_TIPO_LOGRADOURO, $NO_LOGRADOURO, $NR_LOGRADOURO, $NO_COMPL_LOGRADOURO, $NO_BAIRRO, $CD_CEP, $CD_USUARIO_SUS_RESP));

                        //Verifica se o registro ja existe na resp fam. Foi necessário porque existem pacientes que tem mais de 1 co_seq_cad_individual, mas com o mesmo cd_usuario_sus, o que acabava gerando erro
                        $VERIFICA_REGISTRO_RESP = $vinculoDomiciliar->verifica_registro_existente_resp_fam($CD_DOMICILIO, $CD_USUARIO_SUS_RESP, $CD_USUARIO_SUS_RESP);

                        if($CD_USUARIO_SUS_RESP <> "NULL" && empty($VERIFICA_REGISTRO_RESP)){

                            //Insere na soc_domicilio_resp_fam o registro do responsavel familiar como um morador e responsavel    
                            $INSERE_SOC_DOM_RESP_FAM = $vinculoDomiciliar->insere_soc_domicilio_resp_fam($CD_DOMICILIO, $CD_USUARIO_SUS_RESP, $CD_USUARIO_SUS_RESP);

                            //Busco usuarios na atb_cad_ind que tem o responsavel familiar em questao mas que o id seja diferente do cd dele, 
                            //para pegar os dependentes e nao ele
                            $lista_resp_fam_cad_ind = $vinculoDomiciliar->verifica_cad_ind_resp_fam($CD_USUARIO_SUS_RESP);                   
                            $qtd_resp_fam_cad_ind = sizeof($lista_resp_fam_cad_ind);

                            //para cada dependente encontrado
                            for($k = 0; $k < $qtd_resp_fam_cad_ind; $k++){

                                //Pego o cd dos dependentes
                                $CD_USUARIO_SUS_DEPENDENTE = tratamento($lista_resp_fam_cad_ind[$k]['ID_USUARIO']);

                                $VERIFICA_REGISTRO_DEPENDENTE = $vinculoDomiciliar->verifica_registro_existente_resp_fam($CD_DOMICILIO, $CD_USUARIO_SUS_RESP, $CD_USUARIO_SUS_DEPENDENTE);

                                if($CD_USUARIO_SUS_DEPENDENTE <> "NULL" && empty($VERIFICA_REGISTRO_DEPENDENTE)){
                                    
                                    //Atualiza as infos de endereço do dependente na sus_complemento
                                    $ATUALIZA_SUS_COMP_DEPENDENTE = tratamento($vinculoDomiciliar->update_sus_comp_domicilio($CD_DOMICILIO, $ID_LOGRADOURO_BAIRRO_NUMERO, 
                                                                    $ID_TIPO_LOGRADOURO, $NO_LOGRADOURO, $NR_LOGRADOURO, $NO_COMPL_LOGRADOURO, $NO_BAIRRO, $CD_CEP, $CD_USUARIO_SUS_DEPENDENTE));

                                    //insere na resp_fam o dependente e vinculado ao seu responsavel
                                    $INSERE_SOC_DOM_DEPENDENTE = $vinculoDomiciliar->insere_soc_domicilio_resp_fam($CD_DOMICILIO, $CD_USUARIO_SUS_RESP, $CD_USUARIO_SUS_DEPENDENTE);
                                    
                                } 
                                //não tem else porque o else significa que não outro id de usuario que esteja no mesmo domicilio que nao seja o 
                                //do responsavel, ou seja, mora sozinho e não há dependente                   
                            }
                        }else{
                            $dados = ["NOME RESPONSAVEL" => $NO_CIDADAO_RESPONSAVEL, 
                                        "NOME MAE RESPONSAVEL" => $NO_MAE_CIDADAO_RESPONSAVEL, 
                                        "DATA NASCIMENTO RESPONSAVEL" => $DT_NASCIMENTO_RESPONSAVEL,
                                        "CO SEQ RESPONSAVEL" => $CO_SEQ_CDS_CAD_INDIVIDUAL, 
                                        
                                        "CO_SEQ_CDS_CAD_DOMICILIAR" => $CO_SEQ_CDS_CAD_DOMICILIAR,
                                        "CD_DOMICILIO" => $CD_DOMICILIO, 
                                        "ID_LOGRADOURO_BAIRRO_NUMERO" => $ID_LOGRADOURO_BAIRRO_NUMERO, 
                                        "NO_LOGRADOURO" => $NO_LOGRADOURO, 
                                        "NO_COMPL_LOGRADOURO" => $NO_COMPL_LOGRADOURO,  
                                        "NR_LOGRADOURO" => $NR_LOGRADOURO, 
                                        "NO_BAIRRO" => $NO_BAIRRO, 
                                        "CD_CEP" => $CD_CEP, 
                                        "CD_USUARIO_SUS_RESP" => $CD_USUARIO_SUS_RESP, 
                                        "CD_USUARIO_SUS_DEPENDENTE" => ""];

                            $motivo = "Responsavel pelo domicilio nao encontrado";
                            $registro[] = registrosNaoMigrados($dados, acento($motivo));
                        }
                    }else{
                        $dados = ["NOME RESPONSAVEL" => $NO_CIDADAO_RESPONSAVEL, 
                                    "NOME MAE RESPONSAVEL" => $NO_MAE_CIDADAO_RESPONSAVEL, 
                                    "DATA NASCIMENTO RESPONSAVEL" => $DT_NASCIMENTO_RESPONSAVEL,
                                    "CO SEQ RESPONSAVEL" => $CO_SEQ_CDS_CAD_INDIVIDUAL, 
                                    
                                    "CO_SEQ_CDS_CAD_DOMICILIAR" => $CO_SEQ_CDS_CAD_DOMICILIAR,
                                    "CD_DOMICILIO" => "", 
                                    "ID_LOGRADOURO_BAIRRO_NUMERO" => "", 
                                    "NO_LOGRADOURO" => "", 
                                    "NO_COMPL_LOGRADOURO" => "",  
                                    "NR_LOGRADOURO" => "", 
                                    "NO_BAIRRO" => "", 
                                    "CD_CEP" => "", 
                                    "CD_USUARIO_SUS_RESP" => "", 
                                    "CD_USUARIO_SUS_DEPENDENTE" => ""];

                        $motivo = "Domicílio não encontrado";
                        $registro[] = registrosNaoMigrados($dados, acento($motivo));
                    }
                }
            }
            criaOutPut($nome_arquivo, $cabecalho, $registro); 
            header("location:../interface/interfaceEtapas2.php");
        }
    }        
?>
     