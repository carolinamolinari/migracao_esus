<?php
   
    require_once '../config/head.php';    

    class CadastroIndividual{


        public function cadastro_individual() {   

            $registro = array();
            $cabecalho = ['Codigo Sequencial Cidadao', 'Nome', 'Data de Nascimento','Nome da Mae', 'Motivo' ];
            $nome_arquivo = 'nao_ineridos_etapa_cad_individual.csv';


            $cadastroIndividual = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->cadastroIndividual();

            //Trazedo o resultado do select do postgres
            $lista_cadastro_individual = $connectionPostgres->getCadastroIndividual(); 

            

            $qtd_cadastro_individual = sizeof($lista_cadastro_individual);

            for($i = 0; $i < $qtd_cadastro_individual; $i++){ 

                //Variaveis
                {    
                    $CO_SEQ_CDS_CAD_INDIVIDUAL = $lista_cadastro_individual[$i]['co_seq_cds_cad_individual'];
                    $NO_USUARIO                = tratamento($lista_cadastro_individual[$i]['no_cidadao']);
                    $DT_NASCIMENTO             = tratamento($lista_cadastro_individual[$i]['dt_nascimento']);
                    $SAIDA_DATA                = $lista_cadastro_individual[$i]['dt_obito'];
                    $NO_MAE                    = tratamento($lista_cadastro_individual[$i]['no_mae_cidadao']);

                }


                $dados = [                    
                    'Codigo Sequencial Cadastro Individual' => tratamento($lista_cadastro_individual[$i]['co_seq_cds_cad_individual']),
                    'Nome' => $lista_cadastro_individual[$i]['no_cidadao'],     
                    'Data de Nascimento' => $lista_cadastro_individual[$i]['dt_nascimento'],
                    'Nome da Mae' => $lista_cadastro_individual[$i]['no_mae_cidadao']
                ];

                
                //verificando se o paciente existe na sus_complemento
                $ID_USUARIO = $cadastroIndividual->verifica_cd_sus_comp($NO_USUARIO, $DT_NASCIMENTO, $NO_MAE, 1);
                

                //Se existir, faz a inserção do cadastro individual                      
                if(!empty($ID_USUARIO)){//correto  
                
                    $ID_USUARIO = tratamento($ID_USUARIO);
                    $CD_CAD_INDIVIDUAL = $cadastroIndividual->verifica_atb_cadastro_individual($ID_USUARIO);
                    
                    // Se nao existir cadastro individual para esse individuo, insere. Isso evita duplicidade
                    if(empty($CD_CAD_INDIVIDUAL)){ 

                        //Variáveis
                        { 
                            $FL_EXPORTACAO                  = tratamento('1');
                            $DT_EXPORTACAO                  = 'CURRENT_TIMESTAMP';
                            $FL_RESPONSAVEL_FAMILIAR        = tratamento($lista_cadastro_individual[$i]['fl_responsavel_familiar'], NULL, '2');
                            $PARENTESCO                     = tratamento($lista_cadastro_individual[$i]['parentesco']);
                            $OCUPACAO                       = tratamento($lista_cadastro_individual[$i]['ocupacao']);
                            $CURSO                          = tratamento($lista_cadastro_individual[$i]['curso']);
                            $SITUACAO_MERCADO               = tratamento($lista_cadastro_individual[$i]['situacao_mercado']);
                            $CRUPO_COMUNITARIO              = tratamento($lista_cadastro_individual[$i]['crupo_comunitario']);
                            $PL_SAUDE_PRIVADO               = tratamento($lista_cadastro_individual[$i]['pl_saude_privado']);
                            $COMUNIDADE_TRADICIONAL         = tratamento($lista_cadastro_individual[$i]['comunidade_tradicional']);
                            $COMUNIDADE_TRADICIONAL_ESPEC   = tratamento($lista_cadastro_individual[$i]['comunidade_tradicional_espec']);
                            $ORIENTACAO_SEXUAL              = tratamento($lista_cadastro_individual[$i]['orientacao_sexual']);
                            $TEM_DEFICIENCIA                = tratamento($lista_cadastro_individual[$i]['tem_deficiencia']);
                            $DEF_AUDITIVA                   = tratamento($lista_cadastro_individual[$i]['def_auditiva'], NULL, '2');
                            $DEF_INTELECTUAL                = tratamento($lista_cadastro_individual[$i]['def_intelectual'], NULL, '2');
                            $DEF_VISUAL                     = tratamento($lista_cadastro_individual[$i]['def_visual'], NULL, '2');
                            $DEF_FISICA                     = tratamento($lista_cadastro_individual[$i]['def_fisica'], NULL, '2');
                            $DEF_OUTROS                     = tratamento($lista_cadastro_individual[$i]['def_outros'], NULL, '2');
                            $GESTANTE                       = tratamento($lista_cadastro_individual[$i]['gestante']);
                            $SAIDA_MOTIVO                   = tratamento($lista_cadastro_individual[$i]['saida_motivo']);
                            $MATERNIDADE_REFERENCIA         = tratamento($lista_cadastro_individual[$i]['maternidade_referencia']);
                            $PESO                           = tratamento($lista_cadastro_individual[$i]['peso']);
                            $FUMANTE                        = tratamento($lista_cadastro_individual[$i]['fumante'], NULL, '2');
                            $USA_ALCOOL                     = tratamento($lista_cadastro_individual[$i]['usa_alcool'], NULL, '2');
                            $OUTRAS_DROGAS                  = tratamento($lista_cadastro_individual[$i]['outras_drogas'], NULL, '2');
                            $HIPERTENSAO                    = tratamento($lista_cadastro_individual[$i]['hipertensao'], NULL, '2');
                            $DIABETES                       = tratamento($lista_cadastro_individual[$i]['diabetes'], NULL, '2');
                            $AVC_DERRAME                    = tratamento($lista_cadastro_individual[$i]['avc_derrame'], NULL, '2');
                            $INFARTO                        = tratamento($lista_cadastro_individual[$i]['infarto'], NULL, '2');
                            $HANSENIASE                     = tratamento($lista_cadastro_individual[$i]['hanseniase'], NULL, '2');
                            $TUBERCULOSE                    = tratamento($lista_cadastro_individual[$i]['tuberculose'], NULL, '2');
                            $CANCER                         = tratamento($lista_cadastro_individual[$i]['cancer'], NULL, '2');
                            $INTERNACAO_12_MESES            = tratamento($lista_cadastro_individual[$i]['internacao_12_meses'], NULL, '2');
                            $MOTIVO                         = tratamento($lista_cadastro_individual[$i]['motivo']);
                            $DOENCA_CARDIACA                = tratamento($lista_cadastro_individual[$i]['doenca_cardiaca']);
                            $PROBLEMAS_RINS                 = tratamento($lista_cadastro_individual[$i]['problemas_rins']);
                            $DOENCA_RESPIRATORIA_PULMAO     = tratamento($lista_cadastro_individual[$i]['doenca_respiratoria_pulmao']);
                            $ACAMADO                        = tratamento($lista_cadastro_individual[$i]['acamado'], NULL, '2');
                            $DOMICILIADO                    = tratamento($lista_cadastro_individual[$i]['domiciliado'], NULL, '2');
                            $PLANTAS_QUAIS                  = tratamento($lista_cadastro_individual[$i]['plantas_quais']);
                            $PRATICAS_INTEGRATIVAS_COMPLEM  = tratamento($lista_cadastro_individual[$i]['praticas_integrativas_complem']);
                            $OUTRAS_COND_SAUDE              = tratamento($lista_cadastro_individual[$i]['outras_cond_saude']);
                            $TEMPO_SITUACAO_RUA             = tratamento($lista_cadastro_individual[$i]['tempo_situacao_rua']);
                            $ACOMPANHADO_INSTITUICAO        = tratamento($lista_cadastro_individual[$i]['acompanhado_instituicao']);
                            $INSTITUICAO_QUAL               = tratamento($lista_cadastro_individual[$i]['instituicao_qual']);
                            $RECEBE_BENEFICIO               = tratamento($lista_cadastro_individual[$i]['recebe_beneficio']);
                            $REFERENCIA_FAMILIAR            = tratamento($lista_cadastro_individual[$i]['referencia_familiar']);
                            $VISITA_FAMILIAR_FREQUENTE      = tratamento($lista_cadastro_individual[$i]['visita_familiar_frequente']);
                            $VISITA_FAMILIAR_PARENTESCO     = tratamento($lista_cadastro_individual[$i]['visita_familiar_parentesco']);
                            $ALIMENTACAO_DIARIA             = tratamento($lista_cadastro_individual[$i]['alimentacao_diaria']);
                            $ALIMENTECAO_RESTAURANTE_POP    = tratamento($lista_cadastro_individual[$i]['alimentecao_restaurante_pop'], NULL, '2');
                            $ALIMENTACAO_DOACAO_RESTAURANTE = tratamento($lista_cadastro_individual[$i]['alimentacao_doacao_restaurante'], NULL, '2');
                            $ALIMENTACAO_DOACAO_GRUPO_REL   = tratamento($lista_cadastro_individual[$i]['alimentacao_doacao_grupo_rel'], NULL, '2');
                            $ALIMENTACAO_DOACAO_POP         = tratamento($lista_cadastro_individual[$i]['alimentacao_doacao_pop'], NULL, '2');
                            $ALIMENTACAO_OUTROS             = tratamento($lista_cadastro_individual[$i]['alimentacao_outros'], NULL, '2');
                            $HIGIENE_PESSOAL                = tratamento($lista_cadastro_individual[$i]['higiene_pessoal'], NULL, '2');
                            $HIGIENE_BANHO                  = tratamento($lista_cadastro_individual[$i]['higiene_banho'], NULL, '2');
                            $HIGIENE_SANITARIO              = tratamento($lista_cadastro_individual[$i]['higiene_sanitario'], NULL, '2');
                            $HIGIENE_BUCAL                  = tratamento($lista_cadastro_individual[$i]['higiene_bucal'], NULL, '2');
                            $HIGIENE_OUTROS                 = tratamento($lista_cadastro_individual[$i]['higiene_outros'], NULL, '2');
                            $FREQUENTA_ESCOLA               = tratamento($lista_cadastro_individual[$i]['frequenta_escola']);
                            $CRIANCAS_ADULTO                = tratamento($lista_cadastro_individual[$i]['criancas_adulto'], NULL, '2');
                            $CRIANCAS_OUTRAS_CRIANCAS       = tratamento($lista_cadastro_individual[$i]['criancas_outras_criancas'], NULL, '2');
                            $CRIANCAS_ADOLESCENTE           = tratamento($lista_cadastro_individual[$i]['criancas_adolescente'], NULL, '2');
                            $CRIANCAS_SOZINHA               = tratamento($lista_cadastro_individual[$i]['criancas_sozinha'], NULL, '2');
                            $CRIANCAS_CRECHE                = tratamento($lista_cadastro_individual[$i]['criancas_creche'], NULL, '2');
                            $CRIANCAS_OUTRO                 = tratamento($lista_cadastro_individual[$i]['criancas_outro'], NULL, '2');
                            $IDENTIDADE_GENERO              = tratamento($lista_cadastro_individual[$i]['identidade_genero']);
                            $PLANTAS_MEDICINAIS             = tratamento($lista_cadastro_individual[$i]['plantas_medicinais'], NULL, '2');
                            $DOENCA_CARDIACA_POSSUI         = tratamento($lista_cadastro_individual[$i]['doenca_cardiaca_possui']);
                            $DOENCA_CARDIACA_INSUFICIENCIA  = tratamento($lista_cadastro_individual[$i]['doenca_cardiaca_insuficiencia'], NULL, '2');
                            $DOENCA_CARDIACA_OUTRA          = tratamento($lista_cadastro_individual[$i]['doenca_cardiaca_outra'], NULL, '2');
                            $DOENCA_CARDIACA_NAO_SABE       = tratamento($lista_cadastro_individual[$i]['doenca_cardiaca_nao_sabe'], NULL, '2');
                            $PROBLEMA_RINS_POSSUI           = tratamento($lista_cadastro_individual[$i]['problema_rins_possui']);
                            $PROBLEMA_RINS_INSUFICIENCIA    = tratamento($lista_cadastro_individual[$i]['problema_rins_insuficiencia'], NULL, '2');
                            $PROBLEMA_RINS_OUTRO            = tratamento($lista_cadastro_individual[$i]['problema_rins_outro'], NULL, '2');
                            $PROBLEMA_RINS_NAO_SABE         = tratamento($lista_cadastro_individual[$i]['problema_rins_nao_sabe'], NULL, '2');
                            $CUIDADOR_TRAD                  = ($lista_cadastro_individual[$i]['cuidador_trad'] == '0') ? '2' : $lista_cadastro_individual[$i]['cuidador_trad'];
                            $CUIDADOR_TRAD                  = tratamento($CUIDADOR_TRAD);
                            $TRATAM_INTERNACAO_PSIQUIATRICA = tratamento($lista_cadastro_individual[$i]['tratam_internacao_psiquiatrica'], NULL, '2');
                            $FIBROMIALGIA                   = '2';
                            $SAIDA_DATA                     = tratamento($lista_cadastro_individual[$i]['dt_obito']);
                        }

                        if(empty($SAIDA_DATA) || $SAIDA_DATA == "'NULL'"){
                            $SAIDA_DATA = "NULL";                   
                        } 

                        $INSERT_ATB_CADASTRO_INDIVIDUAL = $cadastroIndividual->insere_atb_cadastro_individual($CO_SEQ_CDS_CAD_INDIVIDUAL,
                                $ID_USUARIO, $FL_EXPORTACAO, $DT_EXPORTACAO, $FL_RESPONSAVEL_FAMILIAR, $PARENTESCO, $OCUPACAO, $CURSO, $SITUACAO_MERCADO, $CRUPO_COMUNITARIO, 
                                $PL_SAUDE_PRIVADO, $COMUNIDADE_TRADICIONAL, $COMUNIDADE_TRADICIONAL_ESPEC, $ORIENTACAO_SEXUAL, $TEM_DEFICIENCIA, $DEF_AUDITIVA, $DEF_INTELECTUAL, 
                                $DEF_VISUAL, $DEF_FISICA, $DEF_OUTROS, $SAIDA_MOTIVO, $GESTANTE, $MATERNIDADE_REFERENCIA, $PESO, $FUMANTE, $USA_ALCOOL, $OUTRAS_DROGAS, $HIPERTENSAO,
                                $DIABETES, $AVC_DERRAME, $INFARTO, $HANSENIASE, $TUBERCULOSE, $CANCER, $INTERNACAO_12_MESES, $MOTIVO, $DOENCA_CARDIACA, $PROBLEMAS_RINS, 
                                $DOENCA_RESPIRATORIA_PULMAO, $TRATAM_INTERNACAO_PSIQUIATRICA, $FIBROMIALGIA, $ACAMADO, $DOMICILIADO, $PLANTAS_QUAIS, $PRATICAS_INTEGRATIVAS_COMPLEM,
                                $OUTRAS_COND_SAUDE, $TEMPO_SITUACAO_RUA, $ACOMPANHADO_INSTITUICAO, $INSTITUICAO_QUAL, $RECEBE_BENEFICIO, $REFERENCIA_FAMILIAR, $VISITA_FAMILIAR_FREQUENTE,
                                $VISITA_FAMILIAR_PARENTESCO, $ALIMENTACAO_DIARIA, $ALIMENTECAO_RESTAURANTE_POP, $ALIMENTACAO_DOACAO_RESTAURANTE, $ALIMENTACAO_DOACAO_GRUPO_REL, 
                                $ALIMENTACAO_DOACAO_POP,$ALIMENTACAO_OUTROS,$HIGIENE_PESSOAL,$HIGIENE_BANHO,$HIGIENE_SANITARIO, $HIGIENE_BUCAL, $HIGIENE_OUTROS, $FREQUENTA_ESCOLA, 
                                $CRIANCAS_ADULTO, $CRIANCAS_OUTRAS_CRIANCAS, $CRIANCAS_ADOLESCENTE, $CRIANCAS_SOZINHA, $CRIANCAS_CRECHE, $CRIANCAS_OUTRO, $IDENTIDADE_GENERO, 
                                $PLANTAS_MEDICINAIS, $DOENCA_CARDIACA_POSSUI, $DOENCA_CARDIACA_INSUFICIENCIA, $DOENCA_CARDIACA_OUTRA, $DOENCA_CARDIACA_NAO_SABE,  
                                $PROBLEMA_RINS_POSSUI, $PROBLEMA_RINS_INSUFICIENCIA, $PROBLEMA_RINS_OUTRO, $PROBLEMA_RINS_NAO_SABE, $CUIDADOR_TRAD, $SAIDA_DATA);

                        /*if(!empty($INSERT_ATB_CADASTRO_INDIVIDUAL)){
                            //deu certo
                        }*/

                    }
                }else{
                    $motivo = 'Paciente não foi migrado na etapa anterior, não podendo ter seu cadastro inidividual migrado';              
                    $registro[] = registrosNaoMigrados($dados, acento($motivo));
                }
            } // for  

            criaOutPut($nome_arquivo, $cabecalho, $registro); 
        }
    }

?>