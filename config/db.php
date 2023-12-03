<?php

    
    set_time_limit(0);    
    ini_set('memory_limit', '-1');

    if (session_status() !== PHP_SESSION_ACTIVE ){

        session_start();
        
    }

    class DBConnectionPostgres{ //Banco de Origem    
        
        public $bancoOrigem;
        public $usuarioBancoOrigem;
        public $senhaBancoOrigem;

        public $conn;

        public function __construct() {
            $this->bancoOrigem = $_SESSION['bancoOrigem'];
            $this->usuarioBancoOrigem = $_SESSION['usuarioBancoOrigem'];
            $this->senhaBancoOrigem = $_SESSION['senhaBancoOrigem'];
        }


        public function connect(){ 
   
            try{

                $dsn = "pgsql:host=localhost;port=5432;dbname=" . $this->bancoOrigem . ";user=" . $this->usuarioBancoOrigem . ";password=" . $this->senhaBancoOrigem; 
                
                $this->conn = new PDO($dsn, $this->usuarioBancoOrigem, $this->senhaBancoOrigem);
               
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                return true;

            }catch(PDOException $e){ 
                
                return $e->getMessage();
            }          

        }

        public function query($query) {
            try {
                // Prepara e executa a consulta
                $stmt = $this->conn->prepare($query);
                $stmt->execute();    
                return $stmt;

            } catch (PDOException $e) {

                return $e->getMessage();

            }
        }
    
        public function data($stmt) {
            try {

                // Obtém os dados do resultado da consulta
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                return $data;

            } catch (PDOException $e) {

                return $e->getMessage();

            }
        }
    
    }
   
    class DBConnectionV1 {

        public $host; 
        public $user;  
        public $password;  
        private $conn; // Tornamos a conexão privada
    
        public function __construct() {
            $this->host = $_SESSION['bancoDestino'];
            $this->user = $_SESSION['usuarioBancoDestino'];
            $this->password = $_SESSION['senhaBancoDestino'];
            $this->conn = null; // Inicialmente, a conexão é nula
        }
    
        public function connect() {

            try {

                if ($this->conn === null) { // Cria a conexão apenas se ainda não existir

                    $dsn = "firebird:dbname=" . $this->host;
                    $this->conn = new PDO($dsn, $this->user, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro para exceções

                }

                return true;

            } catch (PDOException $e) {
                
                return $e->getMessage();
            }
        }
    
        public function executa($query, $transacao = null) {
                   
            if ($transacao == null) {

                try {

                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $data;

                } catch(PDOException $e){
                   
                    //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                    $registro = array();
                    $cabecalho = ['query', 'Motivo' ];
                    $nome_arquivo = 'log_erros.csv';
                    $etapa = 'Execução de query e sem transação e com retorno';
                    $dados = ['query' => $query, 'etapa'=>$etapa];
          
                    $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                    
                    // Verifica se o arquivo CSV existe
                    if (file_exists($nome_arquivo)) {
                       
                        $arquivo = fopen($nome_arquivo, 'a');
    
                        foreach($registro as $row){     
    
                            fputcsv($arquivo, $row, ';');
    
                        }                    
                       
                        fclose($arquivo);
    
                        
                    } else {
    
                        criaOutPut($nome_arquivo, $cabecalho, $registro); 
                        
                    }
                    return null;
                }
            } else {  
                try {

                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();  

                    $codigoGerado = $stmt->fetchColumn();
                    
                    if(!empty($codigoGerado)){

                        $codigoGerado = $stmt->fetchColumn(); // Recupera o valor gerado
                        return $codigoGerado;   

                    }else{
                        return $stmt;
                    }
                    

                }catch(PDOException $e){
                   
                    //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                    $registro = array();
                    $cabecalho = ['query', 'Motivo' ];
                    $nome_arquivo = 'log_erros.csv';
                    $etapa = 'Execução de query com transação e com retorno ';
                    $dados = ['query' => $query, 'etapa'=>$etapa];
          
                    $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                    
                    // Verifica se o arquivo CSV existe
                    if (file_exists($nome_arquivo)) {
                       
                        $arquivo = fopen($nome_arquivo, 'a');
    
                        foreach($registro as $row){     
    
                            fputcsv($arquivo, $row, ';');
    
                        }                    
                       
                        fclose($arquivo);
    
                        
                    } else {
    
                        criaOutPut($nome_arquivo, $cabecalho, $registro); 
                        
                    }
                    return null;
                }
            }
        }
        
    
        public function query($query) {
            try {
              
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt;

            } catch (PDOException $e) {
               
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Execução da Query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
        }
    
        public function data($stmt) {

            try{
                $data = $stmt->fetch(PDO::FETCH_ASSOC);              
               
                $stmt->closeCursor();                  
                
                return $data;

            }catch(PDOException $e){
                   
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Retorno de dados da execução da query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
           
        }

        public function multipleData($stmt) {

            try{
                $data = $stmt->fetch(PDO::FETCH_ASSOC);                        
                
                return $data;

            }catch(PDOException $e){
                   
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Retorno de dados da execução da query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
           
        }
    
        // Método para fechar a conexão
        public function closeConnection() {
            if ($this->conn !== null) {
                $this->conn = null;
            }
        }
    }

    class DBConnectionV1_CNSNACRKM {

        public $host; 
        public $user;  
        public $password;  
        private $conn; // Tornamos a conexão privada
    
        public function __construct() {

            // Especificar o caractere antes do qual deseja pegar o texto
            $bancoPrincipal = 'SISTEMA.FDB'; 

            // Encontrar a posição do caractere na string
            $posicao_caractere = strpos($_SESSION['bancoDestino'], $bancoPrincipal);

            // Verificar se o caractere foi encontrado
            if ($posicao_caractere !== false) {

                // Obter todo o texto antes do caractere
                $texto_anterior = substr($_SESSION['bancoDestino'], 0, $posicao_caractere);
     
                $bancoCnsnack = $texto_anterior . "CNSNAC_RKM.FDB";
            } 


            $this->host = $bancoCnsnack;


            $this->user =  $_SESSION['usuarioBancoDestino'];
            $this->password = $_SESSION['senhaBancoDestino'];
            $this->conn = null; // Inicialmente, a conexão é nula
        }
    
        public function connect() {

            try {

                if ($this->conn === null) { // Cria a conexão apenas se ainda não existir

                    $dsn = "firebird:dbname=" . $this->host;
                    $this->conn = new PDO($dsn, $this->user, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Define o modo de erro para exceções

                }

                return true;

            } catch (PDOException $e) {
                
                return $e->getMessage();
            }
        }
    
        public function executa($query, $transacao = null) {
                   
            if ($transacao == null) {

                try {

                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();
                    $data = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stmt->closeCursor();
                    return $data;

                } catch(PDOException $e){
                   
                    //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                    $registro = array();
                    $cabecalho = ['query', 'Motivo' ];
                    $nome_arquivo = 'log_erros.csv';
                    $etapa = 'Execução de query e sem transação e com retorno';
                    $dados = ['query' => $query, 'etapa'=>$etapa];
          
                    $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                    
                    // Verifica se o arquivo CSV existe
                    if (file_exists($nome_arquivo)) {
                       
                        $arquivo = fopen($nome_arquivo, 'a');
    
                        foreach($registro as $row){     
    
                            fputcsv($arquivo, $row, ';');
    
                        }                    
                       
                        fclose($arquivo);
    
                        
                    } else {
    
                        criaOutPut($nome_arquivo, $cabecalho, $registro); 
                        
                    }
                    return null;
                }
            } else {  
                try {

                    $stmt = $this->conn->prepare($query);
                    $stmt->execute();  

                    $codigoGerado = $stmt->fetchColumn();
                    
                    if(!empty($codigoGerado)){

                        $codigoGerado = $stmt->fetchColumn(); // Recupera o valor gerado
                        return $codigoGerado;   

                    }else{
                        return $stmt;
                    }
                    

                }catch(PDOException $e){
                   
                    //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                    $registro = array();
                    $cabecalho = ['query', 'Motivo' ];
                    $nome_arquivo = 'log_erros.csv';
                    $etapa = 'Execução de query com transação e com retorno ';
                    $dados = ['query' => $query, 'etapa'=>$etapa];
          
                    $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                    
                    // Verifica se o arquivo CSV existe
                    if (file_exists($nome_arquivo)) {
                       
                        $arquivo = fopen($nome_arquivo, 'a');
    
                        foreach($registro as $row){     
    
                            fputcsv($arquivo, $row, ';');
    
                        }                    
                       
                        fclose($arquivo);
    
                        
                    } else {
    
                        criaOutPut($nome_arquivo, $cabecalho, $registro); 
                        
                    }
                    return null;
                }
            }
        }        
    
        public function query($query) {
            try {
              
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt;

            } catch (PDOException $e) {
               
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Execução da Query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
        }
    
        public function data($stmt) {

            try{
                $data = $stmt->fetch(PDO::FETCH_ASSOC);              
               
                $stmt->closeCursor();                  
                
                return $data;

            }catch(PDOException $e){
                   
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Retorno de dados da execução da query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
           
        }

        public function multipleData($stmt) {

            try{
                $data = $stmt->fetch(PDO::FETCH_ASSOC);                        
                
                return $data;

            }catch(PDOException $e){
                   
                //LOG DE ERROS COMO CSV - QUERY E ERRO PHP
                $registro = array();
                $cabecalho = ['query', 'Motivo' ];
                $nome_arquivo = 'log_erros.csv';
                $etapa = 'Retorno de dados da execução da query';
                $dados = ['query' => $query, 'etapa'=>$etapa];
      
                $registro[] = registrosNaoMigrados($dados, $e->getMessage());
                
                // Verifica se o arquivo CSV existe
                if (file_exists($nome_arquivo)) {
                   
                    $arquivo = fopen($nome_arquivo, 'a');

                    foreach($registro as $row){     

                        fputcsv($arquivo, $row, ';');

                    }                    
                   
                    fclose($arquivo);

                    
                } else {

                    criaOutPut($nome_arquivo, $cabecalho, $registro); 
                    
                }
                return null;
            }
           
        }
    
        // Método para fechar a conexão
        public function closeConnection() {
            if ($this->conn !== null) {
                $this->conn = null;
            }
        }
    }
    
?>