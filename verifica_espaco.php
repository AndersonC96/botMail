<?php
    // Inclui as configurações
    $config = include('config.php');
    $apiKey = $config['apiKey'];
    $dominio = $config['dominio'];
    $emailsConfig = $config['emails'];
    // Funções auxiliares
    function obterUsuarios($apiKey, $dominio) {
        // Implementação conforme a resposta anterior
    }
    function verificarEspaco($apiKey, $email) {
        // Implementação conforme a resposta anterior
    }
    function enviarAlerta($apiKey, $email, $assunto, $mensagem) {
        // Implementação conforme a resposta anterior
    }
    // Função para verificar a última data de envio
    function podeEnviarEmail($email, $limite, $frequencia) {
        $arquivo = "logs/{$email}_limite_{$limite}.log";
        if (file_exists($arquivo)) {
            $ultimaDataEnvio = file_get_contents($arquivo);
            $diasDesdeUltimoEnvio = (time() - strtotime($ultimaDataEnvio)) / (60 * 60 * 24);
            return $diasDesdeUltimoEnvio >= $frequencia;
        }
        return true;
    }
    // Função para registrar a data de envio
    function registrarEnvio($email, $limite) {
        $arquivo = "logs/{$email}_limite_{$limite}.log";
        file_put_contents($arquivo, date('Y-m-d H:i:s'));
    }
    // Verifica usuários e envia alertas
    $usuarios = obterUsuarios($apiKey, $dominio);
    foreach ($usuarios as $usuario) {
        $espacoLivreGB = verificarEspaco($apiKey, $usuario['email']);
        if ($espacoLivreGB !== null) {
            foreach ($emailsConfig as $config) {
                if ($espacoLivreGB <= $config['limite'] && podeEnviarEmail($usuario['email'], $config['limite'], $config['frequencia'])) {
                    enviarAlerta($apiKey, $usuario['email'], $config['assunto'], $config['mensagem']);
                    registrarEnvio($usuario['email'], $config['limite']);
                    break; // Enviar apenas o e-mail de menor espaço relevante
                }
            }
        }
    }