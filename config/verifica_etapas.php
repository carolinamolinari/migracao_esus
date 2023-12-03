<?php
    require_once 'head.php'; 
    require_once 'require_class.php';
    

    /*$indexes = new Index();
    $indexes->criar_indexes();*/


    $cb_cadastroPaciente = '';
    $cb_cadastroIndividual = '';
    $cb_cadastroDomiciliar = '';

    $cb_vinculoPacienteResponsavel = '';
    $cb_vinculoPacienteDomicilio = '';
    $cb_vinculoCadIndEquipe = '';  
    $cb_vinculoDomicilioEquipe = '';      
    $cb_vinculoDomicilioSemCadInd = ''; //Mesma coisa que sem resp fam
    $cb_vinculoPacienteSemRespFam = '';
    $cb_vinculoPacienteUnidade = '';

    $cb_deletaDomicilioSemUso = '';
    $cb_deletaEnderecoSemUso = '';
    $cb_criaDomicilioEndSemDom = ''; 
    $cb_criaDomicilioPacSemDom = '';
    $cb_criaDomicilioSemRespFam = ''; 


    extract($_POST);


/* CADASTRO */
    if($cb_cadastroPaciente == 'on'){
        //++$qtdEtapas; 
        $cadastroPaciente = new CadastroPaciente();
        $cadastroPaciente->cadastro_paciente();
        
        
    }

    if($cb_cadastroIndividual == 'on'){
        //++$qtdEtapas;
        $cadastroIndividual = new CadastroIndividual();
        $cadastroIndividual->cadastro_individual();
        
    }

    if($cb_cadastroDomiciliar == 'on'){
        //++$qtdEtapas;
        $cadastroDomiciliar = new CadastroDomiciliar();
        $cadastroDomiciliar->cadastro_domiciliar();
        
    }

/* CONFIGURAÇÃO */
    if($cb_vinculoPacienteResponsavel == 'on'){
          
        $vinculoPacienteResponsavel = new VinculoPacienteResponsavel();
        $vinculoPacienteResponsavel->vinculo_paciente_responsavel();
        
    }

    if($cb_vinculoPacienteDomicilio == 'on'){
           
        $vinculoPacienteDomicilio = new VinculoPacienteDomicilio();
        $vinculoPacienteDomicilio->vinculo_paciente_domicilio();
        
    }

    if($cb_vinculoCadIndEquipe == 'on'){
           
       
        $vinculoPacienteEquipe = new VinculoCadIndEquipe();
        $vinculoPacienteEquipe->vinculo_paciente_equipe();
        
    }

    if($cb_vinculoDomicilioEquipe == 'on'){
           
        $vinculoDomicilioEquipe = new VinculoDomicilioEquipe();
        $vinculoDomicilioEquipe->vinculo_domicilio_equipe();
       
        
    }

    if($cb_vinculoDomicilioSemCadInd == 'on'){
         
        $vinculoDomicilioSemRespFam = new VinculoDomicilioSemRespFam();
        $vinculoDomicilioSemRespFam->vinculo_domicilio_sem_resp_fam();  
      
       
        
    }
    if($cb_vinculoPacienteUnidade == 'on'){
        echo "oi";
        $vinculoPacienteUnidade = new VinculoPacienteUnidade();
        $vinculoPacienteUnidade->vinculo_paciente_unidade();  
      
    }

    if($cb_vinculoPacienteSemRespFam == 'on'){
           
        $vinculoPacienteSemDomicilio = new VinculoPacienteUnidade();
        $vinculoPacienteSemDomicilio->vinculo_paciente_unidade();
       
        
    }

/* HIGIENIZAÇÃO */
    if($cb_deletaDomicilioSemUso == 'on'){
           
        $deletaDomicilioSemUso = new DeletaDomicilioSemUso();
        $deletaDomicilioSemUso->deleta_domicilio_sem_uso();
        
    }
    if($cb_deletaEnderecoSemUso == 'on'){
           
        $deletaEnderecoSemUso = new DeletaEnderecoSemUso();
        $deletaEnderecoSemUso->deleta_endereco_sem_uso();
        
    }
    if($cb_criaDomicilioEndSemDom == 'on'){
           
        $criaDomicilioEnderecoSemDom = new CriaDomicilioEnderecoSemDom();
        $criaDomicilioEnderecoSemDom->cria_domicilio_end_sem_dom();
        
    }
    if($cb_criaDomicilioPacSemDom == 'on'){
           
        $criaDomicilioPacienteSemDom = new CriaDomicilioPacienteSemDom();
        $criaDomicilioPacienteSemDom->cria_domicilio_pac_sem_dom();
        
    }
    if($cb_criaDomicilioSemRespFam == 'on'){
           
        $vinculoDomicilioSemRespFam = new VinculoDomicilioSemRespFam();
        $vinculoDomicilioSemRespFam->vinculo_domicilio_sem_resp_fam();
        
    }



?>