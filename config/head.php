<?php
    set_time_limit(0);
    date_default_timezone_set('America/Sao_Paulo');
    error_reporting(E_ALL); ini_set("display_errors", 1);
    //echo "Início da Execução" . date('H:i:s') . '<br/>';
    ini_set('memory_limit', '-1');

    require_once '../funcoes/funcoesPostgres.php'; 
    require_once '../funcoes/funcoesFirebird.php'; 

    echo '  <head> 
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
                <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

            </head>'
    ;

    function posicaoData($data){
        return date($data);
    }

    function acento($valor) {
        $retorno = utf8_decode($valor);
        $comAcentos = utf8_decode("'ÁÍÓÚÉÄÏÖÜËÀÌÒÙÈÃÕÂÎÔÛÊáíóúéäïöüëàìòùèãõâîôûêÇçÑñ");
        $semAcentos = " AIOUEAIOUEAIOUEAOAIOUEaioueaioueaioueaoaioueCcNn";

        for ($i = 0; $i < strlen($comAcentos); $i++) {
            $retorno = str_replace(substr($comAcentos, $i, 1), substr($semAcentos, $i, 1), $retorno);
        }
        return $retorno;
    }

    function tratamento($valor, $tamanho = null, $padrao = null) {
        $valor = trim($valor);
        $valor = (is_null($tamanho)) ? $valor : substr($valor, 0, $tamanho);
        $valor = acento($valor);
        $valor = (empty($valor)) ? ((is_null($padrao)) ? 'NULL' : "'$padrao'") : "'{$valor}'";
        return $valor;
    } 

    function milhar($numero){
        return preg_replace('/([\d]{1,})([\d]{3})/','$1.$2', $numero);
    }
    
    function registrosNaoMigrados($dados, $motivo){        
        //Insere coluna de motivos nos outputs 
        $dados['Motivo'] = $motivo;
        return $dados;
    }

    function criaOutPut($nome_arquivo, $cabecalho = array(), $registro = array()){
        //Cria os outputs
        
        $nome_arquivo = '../output/' . $nome_arquivo;
        $arquivo = fopen($nome_arquivo,'w');

        fputcsv($arquivo, $cabecalho, ';');

        foreach($registro as $row){     
            fputcsv($arquivo, $row, ';');
        }

        fclose($arquivo);

        
    }

    function cabecalhoTabelaRelatorios(){
        echo '<table class="table table-sm table-hover">                           
                        <tbody><tr>';
    }

    function gerarTabelaRelatorio($file, $title){
        echo '<th scope="row">'. $title.'</th>
                        <td class="table-light">
                            <a href='.$file.'> Baixar Relatório CSV </a>
                        </td>
                        </tr>';
    }

    function verificaOutputLogs($cad_paciente, $cad_individual, $cad_domiciliar, $nao_vinc_dom_equipe, $nao_vinc_paciente_dom, $nao_vinc_paciente_equipe, $log_erro, $etapa){
 
        //Cadastro
        if($etapa == 1){
            if(file_exists($cad_paciente) || file_exists($cad_individual) || file_exists($cad_domiciliar)){
                echo "<div class='progresso  shadow p-3 mb-5 bg-white rounded'>
            
                    <legend>Relatórios</legend>";
            }
            if(file_exists($cad_paciente) || file_exists($cad_individual) || file_exists($cad_domiciliar)){
                echo "</p><h6>Cadastros</h6>";


                cabecalhoTabelaRelatorios();
            
                if(file_exists($cad_paciente)){

                    gerarTabelaRelatorio($cad_paciente, "Registros Não Inseridos - Cadastro de Paciêntes");  
                
                }
                    

                if(file_exists($cad_individual)){

                    gerarTabelaRelatorio($cad_individual, "Registros Não Inseridos - Cadastro Individual");                      
                
                }


                if(file_exists($cad_domiciliar)){

                    gerarTabelaRelatorio($cad_domiciliar, "Registros Não Inseridos - Cadastro Domiciliar");                
                    
                }

                echo "</table>";
            }

        }
        //Configuração
        elseif($etapa == 2){
            if(file_exists($nao_vinc_paciente_dom) || file_exists($nao_vinc_dom_equipe) || file_exists($nao_vinc_paciente_equipe)){
                echo "<div class='progresso  shadow p-3 mb-5 bg-white rounded'>
            
                    <legend>Relatórios</legend>";
            }
            if(file_exists($nao_vinc_paciente_dom) || file_exists($nao_vinc_dom_equipe) || file_exists($nao_vinc_paciente_equipe)){

                echo "<br><br><h6>Configuração</h6>";

                cabecalhoTabelaRelatorios();

                if(file_exists($nao_vinc_paciente_dom)){
                
                    gerarTabelaRelatorio($nao_vinc_paciente_dom, "Não vinculados - Paciênte/Domicílio");     
                }

                if(file_exists($nao_vinc_dom_equipe)){

                    gerarTabelaRelatorio($nao_vinc_dom_equipe, "Não vinculados - Cadastro Domiciliar/Equipe");     
            
                }
                    

                if(file_exists($nao_vinc_paciente_equipe)){

                    gerarTabelaRelatorio($nao_vinc_paciente_equipe, "Não vinculados - Cadastro Individual/Equipe");  
                    
                }
                echo "</table>";
            }
        }

        //LogErro
        if(file_exists($log_erro)){
            echo "<br><br><h6>Log de Erros</h6>";
            cabecalhoTabelaRelatorios();
            gerarTabelaRelatorio($log_erro, "Log de Erros");

            echo "</table>";

            //echo "<a href='$log_erro'> Log de Erros - CSV </a></p></p>";
        }
        
        echo "</div>";
    }

    function logNaoMigrados($cabecalho = array(), $registro = array(), $etapa){
        //Verifica se existem arquivos output de loog de erro
        $cad_paciente = '../output/nao_ineridos_etapa_cad_paciente.csv';  
        $cad_individual = '../output/nao_ineridos_etapa_cad_individual.csv';
        $cad_domiciliar = '../output/nao_ineridos_etapa_cad_domiciliar.csv';

        $nao_vinc_dom_equipe = '../output/nao_vinculados_domicilio_equipe.csv';
        $nao_vinc_paciente_dom = '../output/nao_vinculados_paciente_domicilio.csv';
        $nao_vinc_paciente_equipe = '../output/nao_vinculados_paciente_equipe.csv';


        $log_erro = '../output/log_erros.csv';  
            
        if(($etapa == 2)){
            
            
            verificaOutputLogs($cad_paciente, $cad_individual, $cad_domiciliar, 
                                $nao_vinc_dom_equipe, $nao_vinc_paciente_dom, $nao_vinc_paciente_equipe,             
                                $log_erro, $etapa); 

        }
        elseif($etapa == 1){
            
            verificaOutputLogs($cad_paciente, $cad_individual, $cad_domiciliar, 
                                $nao_vinc_dom_equipe, $nao_vinc_paciente_dom, $nao_vinc_paciente_equipe,             
                                $log_erro, $etapa); 


            //Criação da tabela de log
            if($registro){
            
            echo '<div class="progresso  shadow p-3 mb-5 bg-white rounded">

                    <div class="logErro">

                    <legend>Registros não migrados</legend>
                    
                    <table class="table table-striped">
            ';
            }

            //cabeçalho
            foreach ($cabecalho as $chave => $coluna) { //cabeçalho da tabela
            
                
                echo '<th scope="col">'. $coluna .'</th>';         
                        
            }
            echo '</tr>';

            
            foreach($registro as $chave => $valor){ //corpo da tabela
                        
                    
                        echo '<tr>' ; 

                        for ($i = 0; $i < sizeof(array_keys($valor)); $i++){

                            $chaveRegistro = array();
                            $chaveRegistro = array_keys($valor);

                            echo '<td>';
                            
                            print_r($valor[$chaveRegistro[$i]]);

                            echo '</td>';         
                            
                        }               

                        echo '</tr>';
                    
            }
        }

        echo '</tbody></table></div> </div>';

        
    }

    function cabecalhoTabelaStatus(){
        echo '<br><Br><table class="table table-sm table-hover">                           
                                    <tbody><tr>';
    }

    function corpoTabelaStatus($valor, $title){

        $tipo = ($title === 'Paciêntes Migrados' || $title === 'Paciêntes no Banco de Origem' )?('primary'):('success'); //define a cor da célula - bootstrap

        echo '<th scope="row">'.$title.'</th>
                <td class="table-'. $tipo .' ">' . milhar($valor) .' </td></tr></tbody>';
    }
    
    echo ' 
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
            <script src="extensions/export/bootstrap-table-export.js"></script>
    ';
?>
