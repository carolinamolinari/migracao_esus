<?php 

require_once 'funcoesFirebird.php'; 

if(isset($_POST['acao'])){

    $acao = $_POST['acao'];

    $migracao = new Migracao(); // Suponha que você possa criar uma instância de Migracao aqui.
    $contagem = new Contagem($migracao);

    if($acao === "contagemPacientesInseridos"){
        $contagem->contagemPacientesInseridos();        
    }
    
    if($acao === ""){
        $contagem->contagemVinculoPacienteresponsavel();

    }
}

class Contagem
{ 
    public $cadastroPaciente;
    public $vinculoPacienteResponsavel;

    public function __construct(Migracao $migracao){
        $this->cadastroPaciente = $migracao;
    }

    public function contagemPacientesInseridos() {
        $resultado = $this->cadastroPaciente->contagemPaciente();
      
        // Formata o resultado como JSON
        $response = array('resultado' => $resultado);

        // Envia o JSON de volta para o AJAX
        header('Content-Type: application/json');
        echo json_encode($response);
        
    }


    public function contagemVinculoPacienteresponsavel() {
        $resultado = $this->vinculoPacienteResponsavel->contagemVinculoPacienteResponsavel();
      
        // Formata o resultado como JSON
        $response = array('resultado' => $resultado);

        // Envia o JSON de volta para o AJAX
        header('Content-Type: application/json');
        echo json_encode($response);
        
    }
}
?>
