<?php
   
    require_once '../config/head.php';   
    
    class CadastroDomiciliar{
        //private $etapa = "CADASTRO DOMICILIAR";

        public function cadastro_domiciliar(){


            $registro = array();
            $cabecalho = [];
            $nome_arquivo = 'nao_ineridos_etapa_cad_domiciliar.csv';

            $cadastroDomiciliar = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->cadastroDomiciliar();

            //Trazedo o resultado do select do postgres
            $lista_cadastro_domiciliar = $connectionPostgres->getCadastroDomiciliar(); 
            $qtd_cadastro_domiciliar = sizeof($lista_cadastro_domiciliar);
          

            
            for($i = 0; $i < $qtd_cadastro_domiciliar; $i++){


                //Variáveis
                {
                    $NO_LOGR_TIPO = tratamento(strtoupper($lista_cadastro_domiciliar[$i]['no_logr_tipo']));
                    $ABREV_LOGR_TIPO = tratamento(substr($lista_cadastro_domiciliar[$i]['no_logr_tipo'], 0, 3)); 
                    $DESCRICAO_ENDERECO = tratamento(strtoupper($lista_cadastro_domiciliar[$i]['descricao_endereco']));      
                    $NO_BAIRRO = strtoupper(tratamento($lista_cadastro_domiciliar[$i]['no_bairro'], 30));
                    $CEP = tratamento($lista_cadastro_domiciliar[$i]['cep']);
                    $CO_SEQ_CDS_CAD_DOMICILIAR = tratamento($lista_cadastro_domiciliar[$i]['co_seq_cds_cad_domiciliar']);
                }

                $dados = [         
                    'Codigo Sequencial Domicilio' => $CO_SEQ_CDS_CAD_DOMICILIAR,         
                    'Tipo do Logradouro' => $lista_cadastro_domiciliar[$i]['no_logr_tipo'],
                    'Endereco' => $lista_cadastro_domiciliar[$i]['descricao_endereco'],     
                    'Bairro' => $lista_cadastro_domiciliar[$i]['no_bairro'],
                    'CEP' => tratamento($lista_cadastro_domiciliar[$i]['cep'])
                ];

                $NUMERO = $lista_cadastro_domiciliar[$i]['numero'];
                $NUMERO = (int) filter_var($NUMERO, FILTER_SANITIZE_NUMBER_INT);
                
                if ($NUMERO >= 7087041591 || empty($NUMERO)) {
                    $NUMERO = 0;
                }

                
                
            //TIPO ENDEREÇO
                $ID_TIPO_ENDERECO = $cadastroDomiciliar->verifica_logradouro_tipo($NO_LOGR_TIPO);
                //Se nao existe, insere
                if (empty($ID_TIPO_ENDERECO)) {
                
                    $ID_TIPO_ENDERECO = $cadastroDomiciliar->insere_logradouro_tipo($NO_LOGR_TIPO, $ABREV_LOGR_TIPO);
                }

            //ENDEREÇO          

                $ID_LOGRADOURO_ENDERECO = $cadastroDomiciliar->verifica_logradouro_endereco($DESCRICAO_ENDERECO, $ID_TIPO_ENDERECO);
                //Se nao existe, insere
                if ($ID_LOGRADOURO_ENDERECO == NULL) {
                
                    $ID_LOGRADOURO_ENDERECO = $cadastroDomiciliar->insere_logradouro_endereco($DESCRICAO_ENDERECO, $ID_TIPO_ENDERECO);
                }


            //BAIRRO
                
                $ID_BAIRRO = $cadastroDomiciliar->verifica_bairro($NO_BAIRRO);

                if (empty($ID_BAIRRO)) {

                    $ID_BAIRRO = $cadastroDomiciliar->insere_bairro($NO_BAIRRO);

                }
            

            //CEP            

                $ID_LOGRADOURO_CEP = $cadastroDomiciliar->verifica_logradouro_cep($CEP, $ID_BAIRRO, $ID_LOGRADOURO_ENDERECO);
                
                if (empty($ID_LOGRADOURO_CEP)) {

                    $ID_LOGRADOURO_CEP = $cadastroDomiciliar->insere_logradouro_cep($CEP, $ID_BAIRRO, $ID_LOGRADOURO_ENDERECO);
                }

            
            //LOGRADOURO BAIRRO NUMERO
                
                $ID_LOGRADOURO_BAIRRO_NUMERO = $cadastroDomiciliar->verifica_logradouro_bairro_numero($NUMERO, $ID_LOGRADOURO_ENDERECO, $ID_BAIRRO, $ID_LOGRADOURO_CEP);

                if (empty($ID_LOGRADOURO_BAIRRO_NUMERO)) {

                    $ID_LOGRADOURO_BAIRRO_NUMERO = $cadastroDomiciliar->insere_logradouro_bairro_numero($NUMERO, $ID_LOGRADOURO_ENDERECO, $ID_BAIRRO, $ID_LOGRADOURO_CEP);                 
                    
                }

            //SOC DOMICILIO              

               $CD_DOMICILIO = $cadastroDomiciliar->verifica_soc_domicilio($CO_SEQ_CDS_CAD_DOMICILIAR);

                //Se não existe domicilio com esse co_seq, insere
                if(empty($CD_DOMICILIO)){

                    //Variaveis
                    {
                      
                      
                        $NO_COMPLEMENTO            = tratamento($lista_cadastro_domiciliar[$i]['no_complemento']);
                        $TERMO_RECUSA              = tratamento($lista_cadastro_domiciliar[$i]['termo_recusa']);
                        $CD_TIPO_LOCALIDADE        = tratamento($lista_cadastro_domiciliar[$i]['cd_tipo_localidade']);
                        $CD_SITUACAO               = tratamento($lista_cadastro_domiciliar[$i]['cd_situacao']);
                        $CD_TIPO_IMOVEL            = tratamento($lista_cadastro_domiciliar[$i]['cd_tipo_imovel']);
                        $CD_TIPO_ABASTECIMENTO     = tratamento($lista_cadastro_domiciliar[$i]['cd_tipo_abastecimento']);
                        $CD_TRATAMENTO_AGUA        = tratamento($lista_cadastro_domiciliar[$i]['cd_tratamento_agua']);
                        $CD_ESCOAMENTO             = tratamento($lista_cadastro_domiciliar[$i]['cd_escoamento']);
                        $CD_DESTINO_LIXO           = tratamento($lista_cadastro_domiciliar[$i]['cd_destino_lixo']);
                        $TIPO_ACESSO               = tratamento($lista_cadastro_domiciliar[$i]['tipo_acesso']);
                        $SITUACAO_PRODUCAO_RURAL   = tratamento($lista_cadastro_domiciliar[$i]['situacao_producao_rural']);
                        $MATERIAL_PAREDES_EXTERNAS = tratamento($lista_cadastro_domiciliar[$i]['material_paredes_externas']);
                        $NR_COMODO                 = tratamento($lista_cadastro_domiciliar[$i]['nr_comodo']);
                        $DISP_ENERGIA_ELETRICA     = tratamento($lista_cadastro_domiciliar[$i]['disp_energia_eletrica']);
                        $ANIMAIS                   = tratamento($lista_cadastro_domiciliar[$i]['animais']);
                        $ANIMAIS_QTDE              = tratamento($lista_cadastro_domiciliar[$i]['animais_qtde']);
                        $ANIMAIS_GATO              = tratamento($lista_cadastro_domiciliar[$i]['animais_gato']);
                        $ANIMAIS_CACHORRO          = tratamento($lista_cadastro_domiciliar[$i]['animais_cachorro']);
                        $ANIMAIS_PASSARO           = tratamento($lista_cadastro_domiciliar[$i]['animais_passaro']);
                        $ANIMAIS_CRICAO            = tratamento($lista_cadastro_domiciliar[$i]['animais_cricao']);
                        $ANIMAIS_OUTROS            = tratamento($lista_cadastro_domiciliar[$i]['animais_outros']);
                        $TIPO_IMOVEL_ATB           = tratamento($lista_cadastro_domiciliar[$i]['tipo_imovel_atb']);
                    }

                    $dados = [                    
                        'Codigo Sequencial Cadastro Individual' => tratamento($lista_cadastro_domiciliar[$i]['co_seq_cds_cad_individual']),
                        'Nome' => $lista_cadastro_domiciliar[$i]['no_cidadao'],     
                        'Data de Nascimento' => $lista_cadastro_domiciliar[$i]['dt_nascimento'],
                        'Nome da Mae' => $lista_cadastro_domiciliar[$i]['no_mae_cidadao']
                    ];

                    $INSERT_SOC_DOMICILIO = $cadastroDomiciliar->insere_soc_domicilio($CO_SEQ_CDS_CAD_DOMICILIAR, $NO_COMPLEMENTO, $TERMO_RECUSA, $CD_TIPO_LOCALIDADE, $CD_SITUACAO, $CD_TIPO_IMOVEL, 
                                                                                    $CD_TIPO_ABASTECIMENTO, $CD_TRATAMENTO_AGUA, $CD_ESCOAMENTO, $CD_DESTINO_LIXO, $TIPO_ACESSO, $SITUACAO_PRODUCAO_RURAL, 
                                                                                    $MATERIAL_PAREDES_EXTERNAS, $NR_COMODO, $DISP_ENERGIA_ELETRICA, $ANIMAIS, $ANIMAIS_QTDE, $ANIMAIS_GATO, $ANIMAIS_CACHORRO, 
                                                                                    $ANIMAIS_PASSARO, $ANIMAIS_CRICAO, $ANIMAIS_OUTROS, $ID_LOGRADOURO_BAIRRO_NUMERO, $TIPO_IMOVEL_ATB);

                    if(!empty($INSERT_SOC_DOMICILIO)){
                        //deu certo
                    }
                    

                }else{
                    $motivo = 'Código do domicilio já estava cadastrado.';              
                    $registro[] = registrosNaoMigrados($dados, acento($motivo));
                }

            }
            criaOutPut($nome_arquivo, $cabecalho, $registro); 
	
        }
        
    }
?>