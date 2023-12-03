<?php
   
    require_once '../config/head.php';    

    class VinculoDomicilioEquipe{


        public function vinculo_domicilio_equipe(){

            $registro = array();        
            $nome_arquivo = 'nao_vinculados_domicilio_equipe.csv';
            $cabecalho =  ["Co_seq domiciliar", "Area", "Microarea", "INE", "CNS Profissional", "Motivo"];
        

            $domicilioEquipe = new Migracao();

            $connectionPostgres = new Postgres();
            $connectionPostgres->busca_profissionais_equipe_domicilio();
    
            $lista_profissionais_equipe = $connectionPostgres->getProfissionaisEquipeDomicilio(); 
            $qtd_profissionais_equipe = sizeof($lista_profissionais_equipe);
    
            for($i = 0; $i < $qtd_profissionais_equipe; $i++){ 

                $CO_SEQ_CDS_CAD_DOMICILIAR = tratamento($lista_profissionais_equipe[$i]['co_seq_cds_cad_domiciliar']);           
                $MICROAREA = tratamento($lista_profissionais_equipe[$i]['cd_microarea']);
                $AREA = tratamento($lista_profissionais_equipe[$i]['cd_area']);
                $INE = tratamento($lista_profissionais_equipe[$i]['cd_ine']);
                $NU_CNS = tratamento($lista_profissionais_equipe[$i]['nu_cns']);
    
                $lista_equipe_rkm = $domicilioEquipe->busca_profissional_equipe($NU_CNS);
            
                //Verifica se o profissional responsavel pelo cadastro existe na rkm e está devidamente cadastrado
                if(!empty($lista_equipe_rkm[0]['CD_USUARIO_SISTEMA'])){

                    $RESP_CADASTRO = tratamento($lista_equipe_rkm[0]['CD_USUARIO_SISTEMA']);
                    $ID_ATB_MICROAREA = tratamento($lista_equipe_rkm[0]['ID_ATB_MICROAREA']);
                    $ID_ATB_AREA = tratamento($lista_equipe_rkm[0]['ID_ATB_AREA']);
                    $CD_EQUIPE = tratamento($lista_equipe_rkm[0]['CD_EQUIPE']);
                    $UNIDADE_RESP_CAD = tratamento($lista_equipe_rkm[0]['ID_UNIDADE']);
                    $INE = tratamento($lista_equipe_rkm[0]['INE']);
                    $NR_CNES = tratamento($lista_equipe_rkm[0]['NR_CNES']);
                    $CBO_PROFISSIONAL = tratamento($lista_equipe_rkm[0]['CBO']);
         
                    if($ID_ATB_MICROAREA == "NULL"){
                        $FORA_AREA = 1;
                    }else{
                        $FORA_AREA = 0;
                    }    
    
                    $BUSCA_DOMICILIO = $domicilioEquipe->verifica_domicilio($CO_SEQ_CDS_CAD_DOMICILIAR);
    
                    if($BUSCA_DOMICILIO){
                        
                        $domicilioEquipe->update_soc_domicilio_equipe($RESP_CADASTRO, $CBO_PROFISSIONAL, $ID_ATB_MICROAREA, $ID_ATB_AREA, $CD_EQUIPE, 
                                                      $UNIDADE_RESP_CAD, $INE, $NR_CNES, $FORA_AREA, $CO_SEQ_CDS_CAD_DOMICILIAR);
                        
                    }else{

                    }
                }else{
                    $dados = ["Co_seq domiciliar" => $CO_SEQ_CDS_CAD_DOMICILIAR, 
                                "Area" => $AREA, 
                                "Microarea" => $MICROAREA, 
                                "INE" => $INE, 
                                "CNS Profissional" => $NU_CNS
                             ];

                    $motivo = "Domicílio não encontrado";
                    $registro[] = registrosNaoMigrados($dados, acento($motivo));
                } 
            }

            criaOutPut($nome_arquivo, $cabecalho, $registro); 
            header("location:../interface/interfaceEtapas2.php");
          
        }
    }


?>