<?php 

        require_once '../config/head.php';   
        require_once '../funcoes/funcoesFirebird.php';
      
        if (!isset($_SESSION)){
            session_start();
            
            $_SESSION['cabecalho'] = array();
            $_SESSION['registro'] = array(); 
        }
        
        $etapa = 2;
        $totalCadastrosIndividuais = new Migracao();  
        $totalCadastrosIndividuais = $totalCadastrosIndividuais->contagemCadInd();   //Todos os cad ind migrados
        
        
        //$totalCadastrosIndividuais = $totalCadastrosIndividuais->{'contagemVincPacResp'}; //variavel da classe que recebeu o valor
?>

<html>
        
    <head> 
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="../js/pusher.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../arquivos/" />   
        

        <script>

            var totalCadastrosIndividuais = <?=$totalCadastrosIndividuais?>;
            function atualizarBarraProgresso() {
                    $.ajax({
                        url: "../funcoes/contagem.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            acao: 'contagemVinculoPacienteresponsavel'
                        },
                        success: function(retorno) {
                            
                            console.log(retorno.resultado);

                            $("#barraProgressoPaciente").css('width', Math.trunc(retorno.resultado / totalCadastrosIndividuais * 100) + '%');
                            $("#barraProgressoPaciente").text(Math.trunc(retorno.resultado / totalCadastrosIndividuais * 100) + '%'); 
                            
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            
                            console.log(textStatus, errorThrown);
                        }
                        
                    });
                    
                    
                }

            function enviarFormulario() {
                // Desabilita o botão para evitar várias submissões
                $('#enviarForm').prop('disabled', true);

                // Enviar os dados para o servidor para a inserção no banco
                $.ajax({
                    url: "../config/verifica_etapas.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        "cb_vinculoPacienteResponsavel": $('#cb_vinculoPacienteResponsavel').is(':checked') ? 'on' : 'off',
                        "cb_vinculoPacienteDomicilio": $('#cb_vinculoPacienteDomicilio').is(':checked') ? 'on' : 'off',
                        "cb_vinculoCadIndEquipe": $('#cb_vinculoCadIndEquipe').is(':checked') ? 'on' : 'off',
                        "cb_vinculoDomicilioEquipe": $('#cb_vinculoDomicilioEquipe').is(':checked') ? 'on' : 'off',
                        "cb_vinculoDomicilioSemCadInd": $('#cb_vinculoDomicilioSemCadInd').is(':checked') ? 'on' : 'off',
                        "cb_vinculoPacienteUnidade": $('#cb_vinculoPacienteUnidade').is(':checked') ? 'on' : 'off'
                    },
                    success: function(retorno) {
                        console.log(retorno);
                        // Após o sucesso da inserção, atualize a barra de progresso
                        atualizarBarraProgresso();
                        

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                        
                    },
                    complete: function() {
                        // Reabilite o botão após a conclusão
                        $('#enviarForm').prop('disabled', false);
                        
                    }
                });
            }
            
              
            $(document).ready(function() {
            

                // Vincula a função enviarFormulario ao envio do formulário
                $('form').submit(function(e) {
                    e.preventDefault(); // Impede o envio padrão do formulário
                    enviarFormulario(); // Chama a função de envio personalizada
                    location.reload(); //da um refresh para aparecer os dados de nao inseridos

                });
               
                // Intervalo para atualização da barra de progresso
                setTimeout(atualizarBarraProgresso, 1000);    
                     
                
            });

            
            current_progress = 0,
            step = 0.01; // the smaller this is the slower the progress bar

            function fakeBar(){
                interval = setInterval(function() {
                    current_progress += step;
                    progress = Math.round(Math.atan(current_progress) / (Math.PI / 2) * 100 * 1000) / 1000
                    $(".progress-bar").eq(0)
                        .css("width", progress + "%")
                        .attr("aria-valuenow", progress)
                        .text(progress + "%");
                    if (progress >= 100){
                        clearInterval(interval);
                    }else if(progress >= 70) {
                        step = 0.1
                    }
                }, 2000);
            }


        </script>


    </head>
    
    <title>Migração de Dados</title>


    <body>

    
        <div class='container'> <br>


            <!-- Botões de navegação -->
            <div class='container'>   
            <br>
            <nav aria-label="Navegação de página exemplo">
            <ul class="pagination justify-content-end">
                <li class="page-item disabled">

                </li>
                <li class="page-item"><a class="page-link" href="interfaceBancos.php">Bases de Dados</a></li>
                <li class="page-item"><a class="page-link" href="interfaceEtapas.php">(1) Cadastro</a></li>

                <li class="page-item active">
                <span class="page-link">
                    2 - Configuração 
                    <span class="sr-only">(atual)</span>
                </span>
                </li>

                <li class="page-item"><a class="page-link" href="interfaceEtapas3.php">(3) Higienização</a></li>
                <li class="page-item">

                </li>
            </ul>
        </nav>
            <legend style="text-align:center;">Etapa 2: Configuração</legend><br>

            

            
            <!---Segunda etapa etapa: definição das tabelas que serão migradas--->
           
            <form class="form-row align-items-center shadow p-3 mb-5 bg-white rounded" enctype="multipart/form-data" method="POST">

                <legend>Escolha quais etapas deseja realizar </legend>
                
                <!---Campos para seleção--->
                <fieldset class="shadow-sm p-3 mb-5 bg-white rounded">
          
                    <h6>Configuração</h6>
                    <!---Vínculo Paciênte Responsável--->
                    <div>
                        <input type='checkbox' id='cb_vinculoPacienteResponsavel' name='cb_vinculoPacienteResponsavel' />
                        <label for='cb_vinculoPacienteResponsavel'>Vínculo Paciênte Responsável</label>
                    </div>  


                    <!---Vínculo Domicílio Paciênte--->
                    <div>
                        <input type='checkbox' id='cb_vinculoPacienteDomicilio' name='cb_vinculoPacienteDomicilio' />
                        <label for='cb_vinculoPacienteDomicilio'>Vínculo Domicílio Paciênte</label>
                    </div>


                    <!---Vínculo Cadastro Individual Equipe--->
                    <div>
                        <input type='checkbox' id='cb_vinculoCadIndEquipe' name='cb_vinculoCadIndEquipe' />
                        <label for='cb_vinculoCadIndEquipe'>Vínculo Cadastro Individual Equipe</label>
                    </div>


                    <!---Vínculo Cadastro Domiciliar Equipe--->
                    <div>
                        <input type='checkbox' id='cb_vinculoDomicilioEquipe' name='cb_vinculoDomicilioEquipe' />
                        <label for='cb_vinculoDomicilioEquipe'>Vínculo Cadastro Domiciliar Equipe</label>
                    </div>


                    
                    <!---Vínculo Domicílio Sem Cadastro Individual--->
                    <div>
                        <input type='checkbox' id='cb_vinculoDomicilioSemCadInd' name='cb_vinculoDomicilioSemCadInd' />
                        <label for='cb_vinculoDomicilioSemCadInd'>Vínculo Domicílio Sem Cadastro Individual</label>
                    </div>

                    
                    <!---Vínculo Unidade Paciênte--->
                    <div>
                        <input type='checkbox' id='cb_vinculoPacienteUnidade' name='cb_vinculoPacienteUnidade' />
                        <label for='cb_vinculoPacienteUnidade'>Vínculo Paciênte Unidade</label>
                    </div>



                </fieldset>
                
                <!---Barra de Progresso--->

                <div class="progresso  bg-white rounded">
                
                
                <div class="progress">

                    <div id="barraProgresso" class="progress-bar progress-bar-striped progress-bar- " role="progress-bar" style="width: 0%" aria-valuenow="0" 
                    aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>                       

                </div><br> 

             
                <!---Botão submit--->
                <div>
                    <button type="submit" id="enviarForm" class="btn btn-danger" onclick="fakeBar()">Começar Configuração</button>
                    <!---<button type="button" id="cancelarAcao" class="btn btn-secondary" onclick="cancelarAcao()">Cancelar</button>-->

                   
                </div>

            </form>
            
     
            <!---Terceira etapa: acompanhamento do progresso--->
            
                <div class="progresso  shadow p-3 mb-5 bg-white rounded">
                    
                <legend>Status da Configuração</legend>


                <?php

                        if (!isset( $_SESSION['cabecalho'])){ 
                            $_SESSION['cabecalho'] = array();
                        }

                        if (!isset( $_SESSION['registro'])){ 
                            $_SESSION['registro'] = array(); 
                        }
                        $cabecalho = $_SESSION['cabecalho'];
                        $registro = $_SESSION['registro'];


                        $totalPacientesMigrados = new Migracao();
                        $totalPacientesMigrados = $totalPacientesMigrados->contagemPaciente();     
                        

                        $vincPacResponsavel = new Migracao();
                        $vincPacResponsavel = $vincPacResponsavel->qtdPacientesVincResp();      
                        
                        $vincPacDomicilio = new Migracao();
                        $vincPacDomicilio = $vincPacDomicilio->qtdPacientesVincDom();

                        $vincCadIndEquipe = new Migracao();
                        $vincCadIndEquipe = $vincCadIndEquipe->qtdPacientesVincEquipe();

                        $vincDomEquipe = new Migracao();
                        $vincDomEquipe = $vincDomEquipe->qtdDomVincEquipe();

                        $vincPacUnidade = new Migracao();
                        $vincPacUnidade = $vincPacUnidade->qtdPacientesVincUnidade();

                       
                        //Cabeçalho Tabela
                        cabecalhoTabelaStatus();

                        //Primeira linha tabela: Origem                       
                        corpoTabelaStatus($totalPacientesMigrados, 'Paciêntes Migrados');  

                        //Corpo da Tabela com resultados migrados
                        if($totalCadastrosIndividuais > 0){

                            corpoTabelaStatus($totalCadastrosIndividuais, 'Cadastros Individuais Migrados');     
                           
                        }
                        if( $vincPacResponsavel > 0){

                            corpoTabelaStatus($vincPacResponsavel, 'Cadastros Individuais Vinculados ao Responsável'); 
                    
                        }
                        if( $vincPacDomicilio > 0){
                            
                            corpoTabelaStatus($vincPacDomicilio, 'Cadastros Individuais Vinculados a um Domicílio'); 
                    
                        }
                        if( $vincCadIndEquipe > 0){

                            corpoTabelaStatus($vincCadIndEquipe, 'Cadastros Individuais Vinculados à uma Equipe'); 
                        
                        }

                        if( $vincDomEquipe > 0){

                            corpoTabelaStatus($vincDomEquipe, 'Cadastros Domiciliares Vinculados à uma Equipe'); 

                        }


                        if($vincPacUnidade > 0){

                            corpoTabelaStatus($vincPacUnidade, 'Cadastros Individuais Vinculados à uma Unidade');    
                            
                        }

                       
                        echo "</table>";
                        
                ?>
                </div>
                <div><?php logNaoMigrados($cabecalho, $registro, $etapa);?></div>
                    <!--o fim dessa div está dentro do php, porque dentro do php também há a geração de uma nova div-->
                <!--o fim dessa div está no head.php-->
                
        
       
    </body>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
        <script src="extensions/export/bootstrap-table-export.js"></script>

    </html>