<?php
    session_destroy();

    if (!isset($_SESSION)){
        session_start();
        
        $_SESSION['cabecalho'] = array();
        $_SESSION['registro'] = array(); 

    }else{
        unset($_SESSION);
    }
    
    header("location:interface/interfaceBancos.php"); 
?>