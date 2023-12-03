<?php
   
    require_once '../config/head.php';    

//Script 1: cadastro paciênte


    
    class CadastroPaciente{
        public $cnsnac_rkm;
       
        public function cadastro_paciente(){


            $registro = array();
            $cabecalho = ['Codigo Sequencial Cidadao', 'Nome', 'Data de Nascimento','Nome da Mae', 'Nome do Pai', 'Motivo' ];
            $nome_arquivo = 'nao_ineridos_etapa_cad_paciente.csv';
        
    
            //variáveis de conexão
            $cadastroPaciente = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->cadastroPaciente();
          
            //Trazedo o resultado do select do postgres
            $lista_pacientes = $connectionPostgres->getCadastroPaciente(); 
            $qtd_pacientes = sizeof($lista_pacientes);

            $PRONTUARIO_MAX = $cadastroPaciente->verifica_prontuario();
            
            $PRONTUARIO = (empty($PRONTUARIO_MAX)) ? ($PRONTUARIO = 1) : ($PRONTUARIO = $PRONTUARIO_MAX);
    
            
            for($i = 0; $i < $qtd_pacientes; $i++){


                //Variáveis
                {
                    //$IBGE = tratamento('352240');
                    //$CEP =  tratamento('17360332');
                    //$CD_MUNICIPIO = tratamento('3376');                  
                   

                    $CEP =  tratamento($lista_pacientes[$i]['nu_cep']);                    
                    $IBGE = tratamento(substr($lista_pacientes[$i]['co_ibge'], 0, -1));
                    $UF_MUN_LOCALIDADE = tratamento($lista_pacientes[$i]['sg_uf']);


                    if(empty($lista_pacientes[$i]['sg_uf']) || empty($IBGE)){
                        $CD_MUNICIPIO = 1;
                    }else{
                        $CD_MUNICIPIO = $cadastroPaciente->verifica_cd_municipio_residencia($IBGE, $UF_MUN_LOCALIDADE);
                        $CD_MUNICIPIO = (empty($CD_MUNICIPIO))?(1):($CD_MUNICIPIO);
                    }
                    
                
                    //USUARIO CNSNAC_RKM

                    $MUNI_CD_COD_IBGE_RESID = $IBGE;
                    
                    $CD_CEP                    = ($lista_pacientes[$i]['ds_cep']) ? tratamento($lista_pacientes[$i]['ds_cep']) : $CEP;            

                    $CD_PATH_MUNICIPIO         = tratamento('1');
                    $ID_PAIS_RESID             = tratamento('33');
                    $NR_UNIDADE                = tratamento('1');
                    $CLASSIFICACAO             = tratamento('MUNICIPE');
                    $CD_MUNICIPIO_RESID        = $CD_MUNICIPIO;

                    $CO_SEQ_CIDADAO            = tratamento($lista_pacientes[$i]['co_seq_cidadao']);
                    $CD_USUARIO_SUS            = tratamento(strtoupper(md5(uniqid(rand(), true)))); 
                    $NO_USUARIO                = tratamento($lista_pacientes[$i]['no_cidadao']);
                    $IN_SEXO                   = ($lista_pacientes[$i]['no_sexo'] == 'MASCULINO') ? tratamento('M') : tratamento('F');      
                    
                    $DT_NASCIMENTO             = tratamento($lista_pacientes[$i]['dt_nascimento']);
                    $NO_PAI                    = tratamento($lista_pacientes[$i]['no_pai']);
                    $NO_MAE                    = tratamento($lista_pacientes[$i]['no_mae']);
                    $NR_CPF                    = tratamento(str_replace(['.', '-'], '', $lista_pacientes[$i]['nu_cpf']));
                    
                    $NR_PRONTUARIO             = tratamento(str_pad($PRONTUARIO, 8 ,"0", STR_PAD_LEFT));
                    $NR_PRONTUARIO_INT         = tratamento($PRONTUARIO);
                    
                    $CD_ETNIA                  = $lista_pacientes[$i]['co_raca_cor'];
                    $CD_ETNIA                  = ($CD_ETNIA == 3) ? 4 : $CD_ETNIA;
                    $CD_ETNIA                  = ($CD_ETNIA == 4) ? 3 : $CD_ETNIA;
                    $CD_ETNIA                  = tratamento($CD_ETNIA);
                    
                    $DT_OBITO                  = tratamento($lista_pacientes[$i]['dt_obito']);

                    $CD_USUARIO_ESCOLARIDADE   = $lista_pacientes[$i]['co_escolaridade'];            
                    $CD_USUARIO_ESCOLARIDADE   = ($CD_USUARIO_ESCOLARIDADE == 13) ? 1 : $CD_USUARIO_ESCOLARIDADE;
                    $CD_USUARIO_ESCOLARIDADE   = ($CD_USUARIO_ESCOLARIDADE == 10) ? 3 : $CD_USUARIO_ESCOLARIDADE;
                    $CD_USUARIO_ESCOLARIDADE   = ($CD_USUARIO_ESCOLARIDADE ==  5) ? 4 : $CD_USUARIO_ESCOLARIDADE;
                    $CD_USUARIO_ESCOLARIDADE   = ($CD_USUARIO_ESCOLARIDADE == 15) ? 0 : $CD_USUARIO_ESCOLARIDADE;    
                    $CD_USUARIO_ESCOLARIDADE   = tratamento($CD_USUARIO_ESCOLARIDADE);

                    $NO_LOGR_TIPO              = strtoupper(($lista_pacientes[$i]['no_tipo_logradouro']) ? tratamento($lista_pacientes[$i]['no_tipo_logradouro']) : tratamento('ND'));
                    $ABREV_LOGR_TIPO           = tratamento(substr($lista_pacientes[$i]['no_tipo_logradouro'], 0, 3));             
                    $NO_LOGRADOURO             = strtoupper(($lista_pacientes[$i]['ds_logradouro']) ? tratamento($lista_pacientes[$i]['ds_logradouro']) : tratamento('NAO DEFINIDO'));
                    $NR_LOGRADOURO             = ($lista_pacientes[$i]['nu_numero']) ? tratamento($lista_pacientes[$i]['nu_numero'], 7) : tratamento('0');
                    $NR_LOGRADOURO             = (int) filter_var($NR_LOGRADOURO, FILTER_SANITIZE_NUMBER_INT);
                    $NO_COMPL_LOGRADOURO       = tratamento($lista_pacientes[$i]['ds_complemento']);
                    $NO_BAIRRO                 = strtoupper(($lista_pacientes[$i]['no_bairro']) ? tratamento($lista_pacientes[$i]['no_bairro'], 30) : tratamento('NAO DEFINIDO'));
                    
                    $NR_DDD_TELEFONE           = tratamento($lista_pacientes[$i]['nu_telefone_residencial'], 2, '00');
                    $NR_TELEFONE               = tratamento(substr(str_replace(['-', ' '], '', $lista_pacientes[$i]['nu_telefone_residencial']), 2), 8, '00000000');
                    $NR_DDD_CELULAR            = tratamento($lista_pacientes[$i]['nu_telefone_celular'], 2);
                    $NR_CELULAR                = tratamento(substr(str_replace(['-', ' '], '', $lista_pacientes[$i]['nu_telefone_celular']), 2), 9);
                    $NR_DDD_RECADO             = tratamento($lista_pacientes[$i]['nu_telefone_contato'], 2);
                    $NR_RECADO                 = tratamento(substr(str_replace(['-', ' '], '', $lista_pacientes[$i]['nu_telefone_contato']), 2), 9);
                    
                    $IN_SITUACAO_CONJUGAL      = tratamento($lista_pacientes[$i]['co_estado_civil']);
                    $NR_PISPASEP               = tratamento($lista_pacientes[$i]['nu_nis_pis_pasep']);
                    $NO_PAIS                   = tratamento($lista_pacientes[$i]['no_pais_portugues']);
                    $NO_MUNICIPIO              = tratamento($lista_pacientes[$i]['no_localidade']);
                    $NR_CNS                    = tratamento($lista_pacientes[$i]['nu_cns']);

                    $DESCONHECE_MAE            = $lista_pacientes[$i]['st_desconhece_nome_mae'];
                    $DESCONHECE_MAE            = ($DESCONHECE_MAE == 1 || !$lista_pacientes[$i]['no_mae'] || empty($lista_pacientes[$i]['no_mae'])) ? '1' : '0';
                    $DESCONHECE_PAI            = $lista_pacientes[$i]['st_desconhece_nome_pai']; 
                    $DESCONHECE_PAI            = ($DESCONHECE_PAI == 1 || !$lista_pacientes[$i]['no_pai'] || empty($lista_pacientes[$i]['no_pai'])) ? '1' : '0';
                    

                    if($DESCONHECE_PAI == 1){                   
                        $NO_PAI = "'NOME DESCONHECIDO'";
                    }
                

                    if($DESCONHECE_MAE == 1){
                        $NO_MAE = "'NOME DESCONHECIDO'";
                    }
                }

                
                $dados = [
                    
                    'Codigo Sequencial Cidadao' => $CO_SEQ_CIDADAO,
                    'Nome' => $lista_pacientes[$i]['no_cidadao'],     
                    'Data de Nascimento' => posicaoData($lista_pacientes[$i]['dt_nascimento']),
                    'Nome da Mae' => $lista_pacientes[$i]['no_mae'],
                    'Nome do Pai' => $lista_pacientes[$i]['no_pai']
                ];
                
                $REFERENCIA = 0;
                //verificando se o paciente existe na sus_complemento
                $CD_SUS_COMPLEMENTO = $cadastroPaciente->verifica_cd_sus_comp($NO_USUARIO, $DT_NASCIMENTO, $NO_MAE, $NR_CPF, $NR_CNS, $REFERENCIA);
                

                //Se não existir, insere o paciente no banco

                if(empty($CD_SUS_COMPLEMENTO)){//correto   
                    
                    

                    //PAÍS
                    $ID_PAIS = $cadastroPaciente->verifica_pais($NO_PAIS);

                    if (empty($ID_PAIS) || $ID_PAIS == 33) {
                        $ID_PAIS_NASC = tratamento('33');
                        $NACIONALIDADE = tratamento('BRASILEIRO');
                    } else {
                        $ID_PAIS_NASC = tratamento($ID_PAIS);
                        $NACIONALIDADE = tratamento('ESTRANGEIRO');
                    }
                

                    //MUNICÍPIO
                    $ID_MUNICIPIO = $cadastroPaciente->verifica_municipio($NO_MUNICIPIO);
                    
                    if (empty($ID_MUNICIPIO)) {
                        $CD_MUNICIPIO_NASC = $CD_MUNICIPIO;
                    } else {
                        $CD_MUNICIPIO_NASC = tratamento($ID_MUNICIPIO);
                    }

                    //Município nascimento
                    $MUNI_CD_COD_IBGE_NASC = tratamento($cadastroPaciente->verifica_cd_ibge($CD_MUNICIPIO_NASC));               

                    //Logradouro Tipo 
                    $CD_LOGR_TIPO = $cadastroPaciente->verifica_logradouro_tipo($NO_LOGR_TIPO);

                    if (empty($CD_LOGR_TIPO)) {

                        $CD_LOGR_TIPO = $cadastroPaciente->insere_logradouro_tipo($NO_LOGR_TIPO, $ABREV_LOGR_TIPO);

                    }


                    //Logradouro endereço
                    $ID_LOGRADOURO_ENDERECO = $cadastroPaciente->verifica_logradouro_endereco($NO_LOGRADOURO, $CD_LOGR_TIPO);

                    if (empty($ID_LOGRADOURO_ENDERECO)) {

                        $ID_LOGRADOURO_ENDERECO = tratamento($cadastroPaciente->insere_logradouro_endereco($NO_LOGRADOURO, $CD_LOGR_TIPO));

                    }

                    //Bairros
                    $ID_BAIRRO = $cadastroPaciente->verifica_bairro($NO_BAIRRO);

                    if (empty($ID_BAIRRO)) {
                        
                        $ID_BAIRRO = tratamento($cadastroPaciente->insere_bairro($NO_BAIRRO));

                    }

                    //Logradouro CEP
                    $ID_LOGRADOURO_CEP = $cadastroPaciente->verifica_logradouro_cep($CD_CEP, $ID_BAIRRO, $ID_LOGRADOURO_ENDERECO);

                    if (empty($ID_LOGRADOURO_CEP)) {

                        $ID_LOGRADOURO_CEP = tratamento($cadastroPaciente->insere_logradouro_cep($CD_CEP, $ID_BAIRRO, $ID_LOGRADOURO_ENDERECO));
                    }

                    
                    //Logradouro Bairro Numero
                    $ID_LOGRADOURO_BAIRRO_NUMERO = $cadastroPaciente->verifica_logradouro_bairro_numero($NR_LOGRADOURO, $ID_LOGRADOURO_ENDERECO, $ID_BAIRRO, $ID_LOGRADOURO_CEP);

                    if (empty($ID_LOGRADOURO_BAIRRO_NUMERO)) {

                        $ID_LOGRADOURO_BAIRRO_NUMERO = tratamento($cadastroPaciente->insere_logradouro_bairro_numero($NR_LOGRADOURO, $ID_LOGRADOURO_ENDERECO, $ID_BAIRRO, $ID_LOGRADOURO_CEP));                 
                        
                    }

                    $cadastroPaciente->insere_sus_complemento ($MUNI_CD_COD_IBGE_RESID, $CO_SEQ_CIDADAO, $CD_USUARIO_SUS, $NO_USUARIO, $IN_SEXO, $DT_NASCIMENTO, 
                                                    $NO_PAI, $NO_MAE, $NR_CPF, $NR_PRONTUARIO, $NR_PRONTUARIO_INT, $NACIONALIDADE, $CLASSIFICACAO, $CD_MUNICIPIO_NASC, $CD_MUNICIPIO_RESID, 
                                                    $MUNI_CD_COD_IBGE_NASC, $CD_ETNIA, $CD_PATH_MUNICIPIO, $ID_PAIS_RESID, $NR_UNIDADE, $CD_USUARIO_ESCOLARIDADE, $DT_OBITO, $CD_LOGR_TIPO, 
                                                    $NO_LOGRADOURO, $NR_LOGRADOURO, $NO_COMPL_LOGRADOURO, $NO_BAIRRO, $NR_DDD_TELEFONE, $NR_TELEFONE, $NR_DDD_CELULAR, $NR_CELULAR, $NR_DDD_RECADO, $NR_RECADO, 
                                                    $CD_CEP, $IN_SITUACAO_CONJUGAL, $NR_PISPASEP, $ID_PAIS_NASC, $NR_CNS, $DESCONHECE_MAE, $DESCONHECE_PAI, $ID_LOGRADOURO_BAIRRO_NUMERO);
                    
                                                    
                    //CNSNACK - Variáveis padrão
                    $NR_FICHA = tratamento('1');
                    $CD_NACIONALIDADE = tratamento('1');
                    $LOTE_NR_LOTE = tratamento('1');
                    $ID_CBOR = tratamento('1');
                    $ID_CADASTRADOR = tratamento('1');
                    $NO_USERNAME = tratamento('ADMIN');
                    $NR_VERSAO = tratamento('1');
                    $MUNI_NO_MUNICIPIO_NASC = tratamento('1');
                    $COD_IBGE = (empty($lista_pacientes[$i]['co_ibge']))?('1'):(tratamento($lista_pacientes[$i]['co_ibge']));

                    
                    $INSERT_CNSNACKRKM = $cadastroPaciente->insere_usuario_cnsnacrkm ($CD_USUARIO_SUS, $NR_FICHA, $NO_USUARIO, $DT_NASCIMENTO, $IN_SEXO, 
                                                                                    $NO_PAI, $NO_MAE, $CD_NACIONALIDADE, $LOTE_NR_LOTE, $MUNI_CD_COD_IBGE_RESID, 
                                                                                    $MUNI_CD_COD_IBGE_NASC, $MUNI_NO_MUNICIPIO_NASC, $ID_CBOR, $ID_CADASTRADOR, 
                                                                                    $NO_USERNAME, $NR_VERSAO, $NR_CNS, $COD_IBGE);
                    
                    if(empty($INSERT_CNSNACKRKM)){                    
                        $motivo = 'Paciente não inserido no banco CNSNACK_RKM';             
                        $registro[] = registrosNaoMigrados($dados, acento($motivo));
                    }

                    $PRONTUARIO++;      
                    
                }
                else{
                    $motivo = 'Paciente já cadastrado com o código identificador interno: '. $CD_SUS_COMPLEMENTO;              
                    $registro[] = registrosNaoMigrados($dados, acento($motivo));
                    
                }
            }
            $_SESSION['cabecalho'] = $cabecalho;
            $_SESSION['registro'] = $registro;
            
            if(!empty($registro)){
                criaOutPut($nome_arquivo, $cabecalho, $registro); 
            }
            
        }
           
    }

?>