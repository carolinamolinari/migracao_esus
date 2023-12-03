<?php

    require_once '../config/db.php';
    if (session_status() !== PHP_SESSION_ACTIVE ){

        session_start();
    }

    class Postgres{
     
        public $conn;
        public $openConnection;
       


        public $contagemCadastroPaciente;

        public $cadastroPaciente;

        public $cadastroIndividual;

        public $cadastroDomiciliar;

        public $dependentes;

        public $domicilioFamilia;

        public $profissionaisEquipePaciente;

        public $profissionaisEquipeDomicilio;
    

        public function __construct() 
        {
            $this->conn = new DBConnectionPostgres();
            $this->conn->connect();  
            
              
        }  

//CONTAGEM CADASTROS DE PACIENTES
    public function contagemCadastroPaciente(){
    
        $sql_contagem_cadastro_paciente = "SELECT 
                                                    count(*) AS contagem
                                            from        tb_cidadao
                                         
                                            left join    tb_cidadao_vinculacao_equipe 
                                            on             tb_cidadao_vinculacao_equipe.co_cidadao = tb_cidadao.co_seq_cidadao
                                            where       tb_cidadao.st_ativo = 1
                                            and         tb_cidadao.no_cidadao is not null
                                            and         tb_cidadao.dt_nascimento is not null
                                            and         (
                                            tb_cidadao_vinculacao_equipe.st_saida_cadastro_obito <> 1
                                            or tb_cidadao_vinculacao_equipe.st_saida_cadastro_territorio <> 1
                                            ) ";

        $qry_contagem_cadastro_paciente = $this->conn->query($sql_contagem_cadastro_paciente);

        $res_contagem_cadastro_paciente = $this->conn->data($qry_contagem_cadastro_paciente);

        $registros = $res_contagem_cadastro_paciente['contagem'];

        return $this->contagemCadastroPaciente = $registros;
    }


//CADASTRO PACIENTE
        public function cadastroPaciente(){

            //$openConnection $this->conn->connect(); 

            $ResCadastroPaciente = array();              
           
            $sql_cadastro_paciente = "SELECT
                                            tb_cidadao.co_seq_cidadao,
                                            no_cidadao,
                                            dt_nascimento,
                                            no_sexo,
                                            co_estado_civil,
                                            nu_cpf,
                                            nu_telefone_residencial,
                                            nu_telefone_celular,
                                            nu_telefone_contato,
                                            no_mae,
                                            no_pai,
                                            tb_localidade.co_ibge,
                                            tb_uf.sg_uf,
                                            tb_localidade.nu_cep,
                                            tb_tipo_logradouro.no_tipo_logradouro,
                                            ds_logradouro,
                                            nu_numero,
                                            ds_complemento,
                                            no_bairro,
                                            ds_cep,
                                            dt_obito,
                                            co_raca_cor,
                                            nu_nis_pis_pasep,
                                            tb_pais.no_pais_portugues,
                                            tb_localidade.no_localidade,
                                            nu_cns,
                                            co_escolaridade,
                                            st_desconhece_nome_mae,
                                            st_desconhece_nome_pai
                                from        tb_cidadao
                                left join   tb_tipo_logradouro
                                on          tb_tipo_logradouro.co_tipo_logradouro = tb_cidadao.tp_logradouro
                                left join   tb_pais
                                on          tb_pais.co_pais = tb_cidadao.co_pais_nascimento
                                left join   tb_localidade
                                on          tb_localidade.co_localidade = tb_cidadao.co_localidade
                                left join   tb_uf tb_uf
								on 			tb_localidade.co_uf = tb_uf.co_uf
                                left join	tb_cidadao_vinculacao_equipe 
                                on 			tb_cidadao_vinculacao_equipe.co_cidadao = tb_cidadao.co_seq_cidadao
                                where       tb_cidadao.st_ativo = 1
                                and         tb_cidadao.no_cidadao is not null
                                and         tb_cidadao.dt_nascimento is not null
                                and         (
                                    tb_cidadao_vinculacao_equipe.st_saida_cadastro_obito <> 1
                                    or tb_cidadao_vinculacao_equipe.st_saida_cadastro_territorio <> 1
                                )   
                           
                                order by    tb_cidadao.co_seq_cidadao 
                                limit 1
                       
         
                                
            ";

            $qry_cadastro_paciente = $this->conn->query($sql_cadastro_paciente);
            $res_cadastro_paciente = $this->conn->data($qry_cadastro_paciente);

            //Colocando o resultado em um array para retornar
            array_push($ResCadastroPaciente, $res_cadastro_paciente); 

            while($res_cadastro_paciente = $this->conn->data($qry_cadastro_paciente)) {  
                
                array_push($ResCadastroPaciente, $res_cadastro_paciente);              
                            
            }        
            
            $this->cadastroPaciente = $ResCadastroPaciente;

            
       
        }
        public function getCadastroPaciente(){
            return $this->cadastroPaciente;
        }

        
//CADASTRO INDIVIDUAL
        public function cadastroIndividual(){

            //$openConnection $this->conn->connect(); 

            $ResCadastroIndividual= array();              
           
            //echo '<pre>'.
            $sql_cadastro_individual = "SELECT      
                                            tb_cds_cad_individual.co_seq_cds_cad_individual,
                                            tb_cds_cad_individual.no_cidadao,
                                            cast(tb_cds_cad_individual.dt_nascimento as date) dt_nascimento,
                                            tb_cds_cad_individual.no_mae_cidadao,
                                            case when tb_cds_cad_individual.st_responsavel_familiar = 1 then
                                                1
                                            else
                                                2
                                            end fl_responsavel_familiar,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_adulto,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 2 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_outras_criancas,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 3 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_sozinha,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 4 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_outro,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 133 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_adolescente,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 134 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) criancas_creche,
                                            (
                                                select      case when tb_cds_cidadao_resposta.st_resposta = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 3
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) crupo_comunitario,
                                            (
                                                select      case when tb_cds_cidadao_resposta.st_resposta = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 4
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) pl_saude_privado,
                                            (
                                                select      case when tb_cds_cidadao_resposta.st_resposta = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 5
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) comunidade_tradicional,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 6
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) comunidade_tradicional_espec,
                                            (
                                                select      case when tb_cds_cidadao_resposta.st_resposta = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 9
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) tem_deficiencia,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 10
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 12
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) def_auditiva,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 10
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 13
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) def_visual,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 10
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 14
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) def_intelectual,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 10
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 15
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) def_fisica,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 10
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 16
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) def_outros,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 17 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 18 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 19 then
                                                                    4
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 20 then
                                                                    5
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 12
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ),
                                                1
                                            ) tempo_situacao_rua,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 13
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) acompanhado_instituicao,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 14
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) instituicao_qual,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 15
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) recebe_beneficio,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 16
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) referencia_familiar,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 17
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) visita_familiar_frequente,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 18
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) visita_familiar_parentesco,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 19
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) gestante,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 20
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) maternidade_referencia,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 21 then
                                                                3
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 22 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 23 then
                                                                1
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 21
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) peso,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 22
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) fumante,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 23
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) usa_alcool,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 24
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) outras_drogas,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 25
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) hipertensao,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 26
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) diabetes,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 27
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) avc_derrame,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 28
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) infarto,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 24 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 25 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 26 then
                                                                    4
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 30
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ),
                                                1
                                            ) doenca_cardiaca,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 27 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 28 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 29 then
                                                                    4
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 32
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ),
                                                1
                                            ) problemas_rins,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 30 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 31 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 32 then
                                                                    4
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 33 then
                                                                    5
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 34
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ),
                                                1
                                            ) doenca_respiratoria_pulmao,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 35
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) hanseniase,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 36
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) tuberculose,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 37
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) cancer,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 38
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) internacao_12_meses,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 39
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) motivo,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 41
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) acamado,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 42
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) domiciliado,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 44
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) plantas_quais,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 45
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) praticas_integrativas_complem,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 47
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit   1
                                            ) outras_cond_saude, 
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 34 then
                                                                1
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 35 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 36 then
                                                                3
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 48
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentacao_diaria,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 49
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 37
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentecao_restaurante_pop,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 49
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 38
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentacao_doacao_grupo_rel,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 49
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 39
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentacao_doacao_restaurante,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 49
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 40
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentacao_doacao_pop,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 49
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 41
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) alimentacao_outros,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 50
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) higiene_pessoal,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 51
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 42
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) higiene_banho,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 51
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 43
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) higiene_sanitario,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 51
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 44
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) higiene_bucal,
                                            (
                                                select      1
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 51
                                                and 		tb_cds_cidadao_resposta.co_pergunta_detalhe = 45
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) higiene_outros,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 46 then
                                                                1
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 47 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 48 then
                                                                4
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 49 then
                                                                3
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 50 then
                                                                99
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 55
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) in_situacao_conjugal,
                                            (
                                                select      tb_cds_cidadao_resposta.ds_resposta
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 53
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) ocupacao,
                                            (
                                                select      case when tb_cds_cidadao_resposta.st_resposta = 1 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 54
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) frequenta_escola,  
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 51 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 52 then
                                                                3
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 53 then
                                                                4
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 54 then
                                                                5
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 55 then
                                                                6
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 56 then
                                                                7
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 57 then
                                                                8
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 58 then
                                                                9
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 59 then
                                                                10
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 60 then
                                                                11
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 61 then
                                                                12
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 62 then
                                                                13
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 63 then
                                                                14
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 64 then
                                                                15
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 65 then
                                                                1
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 55
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) curso,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 66 then
                                                                1
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 67 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 68 then
                                                                3
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 69 then
                                                                4
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 70 then
                                                                5
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 71 then
                                                                6
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 72 then
                                                                7
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 73 then
                                                                8
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 74 then
                                                                9
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 147 then
                                                                10
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 56
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) situacao_mercado,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 149 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 150 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 151 then
                                                                    5
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 156 then
                                                                    4
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 74
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ), (
                                                    select      case when tb_cds_cidadao_resposta.st_resposta <> 1 then
                                                                    1
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 73
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                )
                                            ) identidade_genero,
                                            coalesce(
                                                (
                                                    select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 148 then
                                                                    2
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 153 then
                                                                    3
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 154 then
                                                                    4
                                                                when tb_cds_cidadao_resposta.co_pergunta_detalhe = 155 then
                                                                    8
                                                                else
                                                                    1
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 77
                                                    order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                ), (
                                                    select      case when tb_cds_cidadao_resposta.st_resposta = 0 then
                                                                    1
                                                                end
                                                    from        tb_cds_cidadao_resposta
                                                    where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                    and         tb_cds_cidadao_resposta.co_pergunta = 76
                                                    order by    tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                    limit       1
                                                )
                                            ) orientacao_sexual,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 135 then
                                                                1
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 136 then
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1000
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) saida_motivo,
                                            (
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 137 then
                                                                1
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 138 then
                                                                2
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 139 then
                                                                3
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 140 then
                                                                4
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 141 then
                                                                5
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 142 then
                                                                6
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 143 then
                                                                7
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 144 then
                                                                8
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 145 then
                                                                9
                                                            when tb_cds_cidadao_resposta.co_pergunta_detalhe = 146 then
                                                                10
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 1001
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) parentesco,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 43
                                                order by    tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) plantas_medicinais,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 29
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) doenca_cardiaca_possui,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 24 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 29
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) doenca_cardiaca_insuficiencia,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 25 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 29
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) doenca_cardiaca_outra,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 26 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 29
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) doenca_cardiaca_nao_sabe,
                                            (
                                                select      coalesce(tb_cds_cidadao_resposta.st_resposta, 2)
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 31
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ) problema_rins_possui,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 27 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 32
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) problema_rins_insuficiencia,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 28 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 32
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) problema_rins_outro,
                                            coalesce((
                                                select      case when tb_cds_cidadao_resposta.co_pergunta_detalhe = 29 then
                                                                1
                                                            else
                                                                2
                                                            end
                                                from        tb_cds_cidadao_resposta
                                                where       tb_cds_cidadao_resposta.co_cds_cad_individual = tb_cds_cad_individual.co_seq_cds_cad_individual
                                                and         tb_cds_cidadao_resposta.co_pergunta = 32
                                                order by	tb_cds_cidadao_resposta.co_seq_cds_cidadao_resposta desc
                                                limit       1
                                            ), 2) problema_rins_nao_sabe,
                                            tb_fat_cad_individual.st_frequenta_cuidador cuidador_trad,
                                            tb_fat_cad_individual.st_tratamento_psiquiatra tratam_internacao_psiquiatrica,                
                                            cast(tb_cds_cad_individual.dt_obito as date) dt_obito
                                from        tb_cds_cad_individual
                                left join   tb_fat_cad_individual
                                on          tb_fat_cad_individual.nu_uuid_ficha = tb_cds_cad_individual.co_unico_ficha
                                where       tb_cds_cad_individual.st_ficha_inativa = 0
                                and         tb_cds_cad_individual.st_versao_atual = 1
                                and         tb_cds_cad_individual.no_mae_cidadao is not null
                                and         tb_cds_cad_individual.dt_nascimento is not null
                         
                              

                                order by    tb_cds_cad_individual.co_seq_cds_cad_individual desc, 
                                            tb_cds_cad_individual.nu_cns_cidadao, 
                                            tb_cds_cad_individual.nu_cartao_sus_responsavel desc 
                                  
                                                       

            ";

            $qry_cadastro_individual = $this->conn->query($sql_cadastro_individual);
            $res_cadastro_individual = $this->conn->data($qry_cadastro_individual);

            array_push($ResCadastroIndividual, $res_cadastro_individual); 

            while($res_cadastro_individual = $this->conn->data($qry_cadastro_individual)) {  
                
                array_push($ResCadastroIndividual, $res_cadastro_individual);              
                            
            }        
            
            $this->cadastroIndividual = $ResCadastroIndividual;
            
       
        }
        public function getCadastroIndividual(){
            return $this->cadastroIndividual;
        }

//CADASTRO DOMICILIAR
        public function cadastroDomiciliar(){

            //$openConnection $this->conn->connect(); 

            $ResCadastroDomiciliar = array();              
           
            $sql_cadastro_domiciliar = "SELECT      
                                            tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar,
                                            tb_tipo_logradouro.no_tipo_logradouro no_logr_tipo,
                                            tb_cds_cad_domiciliar.no_logradouro descricao_endereco,
                                            tb_cds_cad_domiciliar.no_bairro no_bairro,
                                            tb_cds_cad_domiciliar.nu_cep cep,
                                            tb_cds_cad_domiciliar.nu_domicilio numero,
                                            tb_cds_cad_domiciliar.ds_complemento no_complemento,
                                            tb_cds_cad_domiciliar.tp_cds_imovel tipo_imovel_atb,
                                            coalesce(tb_cds_cad_domiciliar.st_recusa_cad, 0) termo_recusa,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 83 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 84 then
                                                                2
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 58
                                                limit       1
                                            ) as cd_tipo_localidade,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 75 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 77 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 78 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 79 then
                                                                4
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 80 then
                                                                5
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 46 then
                                                                6
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 82 then
                                                                7
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 81 then
                                                                8
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 57
                                                limit       1
                                            ) cd_situacao,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 85 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 86 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 87 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 88 then
                                                                4
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 59
                                                limit       1
                                            ) cd_tipo_imovel,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 117 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 118 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 119 then
                                                                5
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 120 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 121 then
                                                                4
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 68
                                                limit       1
                                            ) cd_tipo_abastecimento,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 97 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 98 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 99 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 100 then
                                                                4
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 65
                                                limit       1
                                            ) cd_tratamento_agua,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 122 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 123 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 124 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 125 then
                                                                7
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 126 then
                                                                5
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 127 then
                                                                6
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 69
                                                limit       1
                                            ) cd_escoamento,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 93 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 94 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 95 then
                                                                4
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 96 then
                                                                5
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 64
                                                limit       1
                                            ) cd_destino_lixo,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 89 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 90 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 91 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 92 then
                                                                4
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 62
                                                limit       1
                                            ) tipo_acesso,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 101 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 102 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 103 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 104 then
                                                                4
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 105 then
                                                                5
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 106 then
                                                                6
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 107 then
                                                                7
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 108 then
                                                                8
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 66
                                                limit       1
                                            ) situacao_producao_rural,
                                            (
                                                select      case when tb_cds_domicilio_resposta.co_pergunta_detalhe = 109 then
                                                                1
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 110 then
                                                                2
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 111 then
                                                                3
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 112 then
                                                                4
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 113 then
                                                                5
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 114 then
                                                                6
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 115 then
                                                                7
                                                            when tb_cds_domicilio_resposta.co_pergunta_detalhe = 116 then
                                                                8
                                                            end
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 67
                                                limit       1
                                            ) material_paredes_externas,
                                            (
                                                select      tb_cds_domicilio_resposta.ds_resposta
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 61
                                                limit       1
                                            ) nr_comodo,
                                            (
                                                select      tb_cds_domicilio_resposta.st_resposta
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 63
                                                limit       1
                                            ) disp_energia_eletrica,
                                            (
                                                select      tb_cds_domicilio_resposta.st_resposta
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 70
                                                limit       1
                                            ) animais,
                                            (
                                                select      tb_cds_domicilio_resposta.ds_resposta
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 72
                                                limit       1
                                            ) animais_qtde,
                                            (
                                                select      1
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 71
                                                and         tb_cds_domicilio_resposta.co_pergunta_detalhe = 128
                                                limit       1
                                            ) animais_gato,
                                            (
                                                select      1
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 71
                                                and         tb_cds_domicilio_resposta.co_pergunta_detalhe = 129
                                                limit       1
                                            ) animais_cachorro,
                                            (
                                                select      1
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 71
                                                and         tb_cds_domicilio_resposta.co_pergunta_detalhe = 130
                                                limit       1
                                            ) animais_passaro,
                                            (
                                                select      1
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 71
                                                and         tb_cds_domicilio_resposta.co_pergunta_detalhe = 131
                                                limit       1
                                            ) animais_cricao,
                                            (
                                                select      1
                                                from        tb_cds_domicilio_resposta
                                                where       tb_cds_domicilio_resposta.co_cds_cad_domiciliar = tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar
                                                and         tb_cds_domicilio_resposta.co_pergunta = 71
                                                and         tb_cds_domicilio_resposta.co_pergunta_detalhe = 132
                                                limit       1
                                            ) animais_outros
                                from        tb_cds_cad_domiciliar
                                inner join  tb_tipo_logradouro
                                on          tb_tipo_logradouro.co_tipo_logradouro = tb_cds_cad_domiciliar.tp_logradouro
                                where       tb_cds_cad_domiciliar.st_versao_atual = 1
                               
            
                                order by    tb_cds_cad_domiciliar.co_seq_cds_cad_domiciliar desc
                            
            ";

            $qry_cadastro_domiciliar = $this->conn->query($sql_cadastro_domiciliar);
            $res_cadastro_domiciliar = $this->conn->data($qry_cadastro_domiciliar);

            array_push($ResCadastroDomiciliar, $res_cadastro_domiciliar); 

            while($res_cadastro_domiciliar = $this->conn->data($qry_cadastro_domiciliar)) {  
                
                array_push($ResCadastroDomiciliar, $res_cadastro_domiciliar);              
                            
            }     
                  
            $this->cadastroDomiciliar = $ResCadastroDomiciliar;
            
       
        }
        public function getCadastroDomiciliar(){
            return $this->cadastroDomiciliar;
        }

//VINCULO RESPONSÁVEL
        public function verifica_dependentes($co_seq_cds_cad_individual_resp){

            //$openConnection $this->conn->connect();    
            
            $ResDependentes= array();        
           
            $sql = "SELECT DISTINCT    
                                    tcci.co_seq_cds_cad_individual
                        from        tb_cds_cad_individual tcci
                        where       tcci.nu_cartao_sus_responsavel = (
                                                                        select tcci.nu_cns_cidadao
                                                                        from tb_cds_cad_individual tcci
                                                                        where tcci.co_seq_cds_cad_individual = $co_seq_cds_cad_individual_resp)
                        OR          tcci.nu_cpf_responsavel = (
                                                                select tcci.nu_cpf_cidadao
                                                                from tb_cds_cad_individual tcci
                                                                where tcci.co_seq_cds_cad_individual = $co_seq_cds_cad_individual_resp)   
            ";
            $execute = $this->conn->query($sql);
            $return = $this->conn->data($execute);

            array_push($ResDependentes, $return); 

            while($return = $this->conn->data($execute)) {  
                
                array_push($ResDependentes, $return);              
                            
            }        
            
            $this->dependentes = $ResDependentes;
       
        }
        public function getDependentes(){
            return $this->dependentes;
        }

//VINCULO DOMICILIAR
        public function vinculo_domicilio_familia(){

            //$openConnection $this->conn->connect();    
            
            $ResDomicilioFamilia = array();        
        
            $sql = "SELECT		
                                tcci.no_cidadao,	
                                tcci.no_mae_cidadao, 
                                cast(tcci.dt_nascimento as date) as dt_nascimento, 
                                tcci.co_seq_cds_cad_individual,
                                
                                tccd.co_seq_cds_cad_domiciliar,
                                tcci.co_seq_cds_cad_individual,
                                tcdf.st_mudanca
                    from		tb_cds_domicilio_familia tcdf
                    inner join	tb_cds_cad_domiciliar tccd
                    on			tccd.co_seq_cds_cad_domiciliar = tcdf.co_cds_cad_domiciliar
                    inner join	tb_cds_cad_individual tcci
                    on			tcci.nu_cns_cidadao = tcdf.nu_cartao_sus or tcci.nu_cpf_cidadao = tcdf.nu_cpf_cidadao
                    where       tcci.st_versao_atual = 1       

            ";
            $execute = $this->conn->query($sql);
            $return = $this->conn->data($execute);

            array_push($ResDomicilioFamilia, $return); 

            while($return = $this->conn->data($execute)) {  
                
                array_push($ResDomicilioFamilia, $return);              
                            
            }        
            
            $this->domicilioFamilia = $ResDomicilioFamilia;

        }
        public function getDomicilioFamilia(){
            return $this->domicilioFamilia;
        }

//VINCULO PACIENTE EQUIPE
        public function busca_profissionais_equipe_paciente(){

            //$openConnection $this->conn->connect();    
            
            $ResProfissionaisEquipe = array();        

            $sql = "SELECT DISTINCT  
                            tcci.co_seq_cds_cad_individual,
                            tcci.no_cidadao,	
                            tcci.no_mae_cidadao, 
                            cast(tcci.dt_nascimento as date) as dt_nascimento,

                            tp.no_profissional no_usuario_sistema,
                            tcp.nu_cbo_2002 cbo_profissional,
                            tus.no_unidade_saude no_fantasia,
                            tus.nu_cnes,
                            te.ds_area cd_area,
                            tcci.nu_micro_area cd_microarea,
                            tcp.nu_ine cd_ine,
                            tp.nu_cns
                    from        tb_cds_prof tcp
                    inner join  tb_prof tp
                    on          tp.nu_cns = tcp.nu_cns
                    inner join  tb_equipe te
                    on          te.nu_ine = tcp.nu_ine
                    inner join  tb_cds_cad_individual tcci
                    on          tcci.co_cds_prof_cadastrante = tcp.co_seq_cds_prof
                    inner join  tb_unidade_saude tus
                    on          tus.nu_cnes = tcp.nu_cnes  
                    where       tcci.st_ficha_inativa = 0
                    and         tcci.st_versao_atual = 1       

            ";
            $execute = $this->conn->query($sql);
            $return = $this->conn->data($execute);

            array_push($ResProfissionaisEquipe, $return); 

            while($return = $this->conn->data($execute)) {  
                
                array_push($ResProfissionaisEquipe, $return);              
                            
            }        
            
            $this->profissionaisEquipePaciente = $ResProfissionaisEquipe;

        }
        public function getProfissionaisEquipePaciente(){
            return $this->profissionaisEquipePaciente;
        }


//VINCULO DOMICILIO EQUIPE
        public function busca_profissionais_equipe_domicilio(){

            //$openConnection $this->conn->connect();    
            
            $ResProfissionaisEquipeDomicilio = array();        

            $sql = "SELECT DISTINCT 
                        tccd.co_seq_cds_cad_domiciliar,
                        tp.no_profissional no_usuario_sistema,
                        tcp.nu_cbo_2002 cbo_profissional,
                        tus.no_unidade_saude no_fantasia,
                        tus.nu_cnes,
                        te.ds_area cd_area,
                        tccd.nu_micro_area cd_microarea,
                        tcp.nu_ine cd_ine,
                        tp.nu_cns
                    from        tb_cds_prof tcp
                    inner join  tb_prof tp
                    on          tp.nu_cns = tcp.nu_cns
                    left join  tb_equipe te
                    on          te.nu_ine = tcp.nu_ine
                    inner join  tb_cds_cad_domiciliar tccd
                    on          tccd.co_cds_prof_cadastrante = tcp.co_seq_cds_prof
                    left join  tb_unidade_saude tus
                    on          tus.nu_cnes = tcp.nu_cnes
                    where       tccd.st_versao_atual = 1     

            ";
            $execute = $this->conn->query($sql);
            $return = $this->conn->data($execute);

            array_push($ResProfissionaisEquipeDomicilio, $return); 

            while($return = $this->conn->data($execute)) {  
                
                array_push($ResProfissionaisEquipeDomicilio, $return);              
                            
            }        
            
            $this->profissionaisEquipeDomicilio = $ResProfissionaisEquipeDomicilio;

        }
        public function getProfissionaisEquipeDomicilio(){
            return $this->profissionaisEquipeDomicilio;
        }

    }
    
?>