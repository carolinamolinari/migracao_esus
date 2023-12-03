<?php
    require_once 'db.php';

    echo ' 
            <head> 
            <meta charset="UTF-8">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
            <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

            </head>'
    ;

    if (session_status() !== PHP_SESSION_ACTIVE){
        session_start();
        unset($_SESSION['registro']);
        
    }

    $_SESSION['bancoOrigem'] = $_POST['bancoOrigem'];
    $_SESSION['bancoDestino'] = $_POST['bancoDestino'];
    $_SESSION['usuarioBancoOrigem'] = $_POST['usuarioBancoOrigem'];
    $_SESSION['senhaBancoOrigem'] = $_POST['senhaBancoOrigem'];
    $_SESSION['ipMaquina'] = $_POST['ipMaquina'];


    //Verificando se o usuario selecionou a opção de usar o padrão e definindo as variaveis para tal  

    if(isset($_POST['usarPadraoBancoDestino'])){

        $_SESSION['usuarioBancoDestino'] = "SYSDBA"; 
        $_SESSION['senhaBancoDestino'] = "masterkey";

    }else{

        $_SESSION['usuarioBancoDestino'] = $_POST['usuarioBancoDestino'];
        $_SESSION['senhaBancoDestino'] = $_POST['senhaBancoDestino'];
        
    }

    $connectionPostgres = new DBConnectionPostgres();
    $retornoPostgres = $connectionPostgres->connect();


    $connectionFirebird = new DBConnectionV1();
    $retornoFirebird = $connectionFirebird->connect();
    
    if($retornoPostgres == 1 && $retornoFirebird == 1){
        header("location:../interface/interfaceEtapas.php");
    }
    else{

        if($retornoPostgres <> 1){
            $retornoErro = $retornoPostgres;
            $retornoErro = explode(":", $retornoErro);   
            $retornoErro = $retornoErro[1];
            
            
            header("location:../interface/interfaceBancos.php?error=$retornoErro&b=o");
        }
        else{
            $retornoErro = $retornoFirebird;
           
            $retornoErro = explode("SQLSTATE[HY000] ", $retornoErro);             
            $retornoErro = explode("]", $retornoErro[1]);
            $retornoErro = $retornoErro[1];
            
            header("location:../interface/interfaceBancos.php?error=$retornoErro&b=d");

        }
    }

    echo ' 
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
        <script src="extensions/export/bootstrap-table-export.js"></script>
    ';
?>
