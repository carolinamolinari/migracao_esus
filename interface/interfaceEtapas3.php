<?php 

        require_once '../config/head.php';   
        require_once '../funcoes/funcoesFirebird.php';
      
        if (!isset($_SESSION)){
            session_start();
            
            $_SESSION['cabecalho'] = array();
            $_SESSION['registro'] = array(); 
        }
       
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
                        "cb_deletaDomicilioSemUso": $('#cb_deletaDomicilioSemUso').is(':checked') ? 'on' : 'off',
                        "cb_deletaEnderecoSemUso": $('#cb_deletaEnderecoSemUso').is(':checked') ? 'on' : 'off',
                        "cb_criaDomicilioEndSemDom": $('#cb_criaDomicilioEndSemDom').is(':checked') ? 'on' : 'off',
                        "cb_criaDomicilioPacSemDom": $('#cb_criaDomicilioPacSemDom').is(':checked') ? 'on' : 'off',
                        "cb_criaDomicilioSemRespFam": $('#cb_criaDomicilioSemRespFam').is(':checked') ? 'on' : 'off'
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
                    <li class="page-item"><a class="page-link" href="interfaceEtapas.php">(1) cadastro</a></li>
                    <li class="page-item"><a class="page-link" href="interfaceEtapas2.php">(2) configuração</a></li>  
                    <li class="page-item active">
                    <span class="page-link">
                        3 - Higienização
                        <span class="sr-only">(atual)</span>
                    </span>
                    </li>              
                    <li class="page-item">

                    </li>
                </ul>
            </nav>
            <legend style="text-align:center;">Etapa 3: Higienização</legend><br>

            

            
            <!---Segunda etapa etapa: definição das tabelas que serão migradas--->
           
            <form class="form-row align-items-center shadow p-3 mb-5 bg-white rounded" enctype="multipart/form-data" method="POST">

                <legend>Escolha quais etapas deseja realizar </legend>
                
                <!---Campos para seleção--->
                <fieldset class="shadow-sm p-3 mb-5 bg-white rounded">
                    <h6>Higienização</h6>
                   

                
                      


                    <!---Cria Domicílio para Endereços Sem Domicílio--->
                    <div>
                        <input type='checkbox' id='cb_criaDomicilioEndSemDom' name='cb_criaDomicilioEndSemDom' />
                        <label for='cb_criaDomicilioEndSemDom'>Cria Domicílio para Endereços Sem Domicílio</label>
                    </div>  


                    <!---Cria Domicílio para Paciêntes Sem Domicílio--->
                    <div>
                        <input type='checkbox' id='cb_criaDomicilioPacSemDom' name='cb_criaDomicilioPacSemDom' />
                        <label for='cb_criaDomicilioPacSemDom'>Cria Domicílio para Paciêntes Sem Domicílio</label>
                    </div>  


                    <!---Vínculo Domicílio Sem Responsável Familiar--->
                    <div>
                        <input type='checkbox' id='cb_criaDomicilioSemRespFam' name='cb_criaDomicilioSemRespFam' />
                        <label for='cb_criaDomicilioSemRespFam'>Víncular Domicílio Sem Responsável Familiar</label>
                    </div>  

                     <!---Delete Domicilios sem Uso--->
                     <div>
                        <input type='checkbox' id='cb_deletaDomicilioSemUso' name='cb_deletaDomicilioSemUso' />
                        <label for='cb_deletaDomicilioSemUso'>Delete Domicilios sem Uso</label>
                    </div>

                     <!---Delete Endereços Sem Uso--->
                     <div>
                        <input type='checkbox' id='cb_deletaEnderecoSemUso' name='cb_deletaEnderecoSemUso' />
                        <label for='cb_deletaEnderecoSemUso'>Delete Endereços Sem Uso</label>
                    </div> 

                    <br>

                
                </fieldset> 
                
               
                <div class="progresso  bg-white rounded">
                
                
                <div class="progress">

                    <div id="barraProgresso" class="progress-bar progress-bar-striped progress-bar- " role="progress-bar" style="width: 0%" aria-valuenow="0" 
                    aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>                       

                </div><br> 

             
                <!---Botão submit--->
                <div>
                    <button type="submit" id="enviarForm" class="btn btn-danger" onclick="fakeBar()">Começar Higienização</button>
                    <!---<button type="button" id="cancelarAcao" class="btn btn-secondary" onclick="cancelarAcao()">Cancelar</button>-->

                   
                </div>

            </form>
            
     
            <!---Terceira etapa: acompanhamento do progresso--->
            
                <div class="progresso  shadow p-3 mb-5 bg-white rounded">
                    
                <legend>Status da Higienização</legend>


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

                        $qtdDomicilios = new Migracao();
                        $qtdDomicilios = $qtdDomicilios->contagemCadDomicilio();


                        $domiciliosSemUso = new Migracao();
                        $domiciliosSemUso = $domiciliosSemUso->qtdDomiciliosSemUso();

                        $enderecosSemUso = new Migracao();
                        $enderecosSemUso = $enderecosSemUso->qtdEnderecosSemUso();

                        $pacienteComEnderecosSemDom = new Migracao();
                        $pacienteComEnderecosSemDom = $pacienteComEnderecosSemDom->qtdPacComEnderecosSemDom();
                        
                        //Cabeçalho Tabela
                        cabecalhoTabelaStatus();

                        //Primeira linha tabela: Origem                       
                        corpoTabelaStatus($totalPacientesMigrados, 'Paciêntes Migrados');                                          
                        

                        //Corpo da Tabela com resultados migrados
                        if($totalCadastrosIndividuais > 0){

                            corpoTabelaStatus($totalCadastrosIndividuais, 'Cadastros Individuais Migrados');  
                            
                        }
                        if($qtdDomicilios > 0){

                            corpoTabelaStatus($qtdDomicilios, 'Quantidade de Domicílios');
                        }

                        if($domiciliosSemUso >= 0){

                            corpoTabelaStatus($domiciliosSemUso, 'Domicílios Sem Uso');                            
                        }
                      
                        if($enderecosSemUso >= 0){

                            corpoTabelaStatus($enderecosSemUso, 'Endereços Sem Uso'); 
                        }

                        //Cria Domicílio para Endereços Sem Domicílio: É POSSÍVEL VERIFICAR ESSA QUANTIDADE NA QUANTIDADE DE DOMICLÍOS
                        if($pacienteComEnderecosSemDom >= 0){

                            corpoTabelaStatus($pacienteComEnderecosSemDom, 'Paciêntes Com Endereço e Sem Domicílio'); 
                           
                        }
                       

                       
                        echo "</table>";
                        
                ?>


               
                    <!--o fim dessa div está dentro do php, porque dentro do php também há a geração de uma nova div-->
                <!--o fim dessa div está no head.php-->
                
        
       
    </body>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
        <script src="extensions/export/bootstrap-table-export.js"></script>

    </html>