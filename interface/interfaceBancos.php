<?php
    require_once "../config/head.php"; 
    

    set_time_limit(0);    
    ini_set('memory_limit', '-1');
    
    if (!isset($_SESSION)){
        session_start();
        $_SESSION['cabecalho'] = '';
        $_SESSION['registro'] = ''; 
        
    }else{
        unset($_SESSION);
        
    }
    

    function exibeErro($banco, $tipoErro){

        print "<div><span style='color:red; font-weight: bold; font-size: 20px;'> Error: $tipoErro. <br> Database: ";

        if($banco == "d"){
            print "Destino (Firebird)";

        }if($banco == "o"){
            print "Origem (Postgres)";
        }            
        
        print "</span></div>";
    }
?>
<html>
    
    
    <head> 
        <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    

    </head>
    
    <title>Migração de Dados</title>

    <div class='container'>   
        <br>
    
        <legend class="display-6" style="text-align:center;">Migração de Dados <br> PostgreSQL >> Firebird</legend>

       
        <!---Primeira etapa: definição das variaveis inseridas pelo usuario--->

		
        <?php
				
				if(isset($_GET['error'])){
                    $banco = '';
                    extract($_GET);                            
                    exibeErro($b, $error);
				}
			
		?>

        <form method='post' enctype='multipart/form-data' action='../config/sessao.php'>
                
                <!---Caminho Bancos--->
                    
                <div class="form-row align-items-center shadow p-3 mb-5 bg-white rounded">
                    <div>

                        <div class="col-auto ">
                            <label class="sr-only" for="inlineFormInput">Base e-SUS</label>
                            <input type="text" class="form-control mb-2" id="hostPostgresql" name="bancoOrigem" placeholder="Nome da Base">
                        </div> 

                        <div class="col-auto ">
                            <label class="sr-only" for="inlineFormInput">User</label>
                            <input type="text" class="form-control mb-2" id="hostPostgresql" name="usuarioBancoOrigem">
                        </div> 

                        <div class="col-auto ">
                            <label class="sr-only" for="inlineFormInput">Senha</label>        
                            <input type="password" class="form-control" id="hostPostgresql" name="senhaBancoOrigem">
                        </div> 

                    </div>

                    <br><Br>
                    

                    <div>

                        </div>    <div class="col-auto">
                            <label class="sr-only" for="inlineFormInput">Banco ConectaSUS</label>
                            <input type="text" class="form-control mb-2" id="hostFirebird" name="bancoDestino" placeholder="Caminho para a base">
                        </div>
                    

                        <div class="col-auto ">
                            <label class="sr-only" for="inlineFormInput">User</label>
                            <input type="text" class="form-control mb-2" id="hostFirebird" name="usuarioBancoDestino">
                        </div> 


                        <div class="col-auto ">
                            <label class="sr-only" for="inlineFormInput">Senha</label>        
                            <input type="password" class="form-control" id="hostFirebird" name="senhaBancoDestino">
                        </div> 

                        <br>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="autoSizingCheck"  value="1" name="usarPadraoBancoDestino" >
                            <label class="form-check-label" for="autoSizingCheck" >Usar usuário e senha padrão no Firebird</label> 
                        </div>


                    </div>
                
                
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2">Verificar</button>
                    </div>
                </div>
                
        
        </form>             
        
    </div>


        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
        <script src="extensions/export/bootstrap-table-export.js"></script>


</html>