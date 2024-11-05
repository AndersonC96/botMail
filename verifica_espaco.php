<?php
    // Inclui as configurações do arquivo config.php
    $config = include('config.php');
    $apiKey = $config['apiKey'];
    $dominio = $config['dominio'];
    $emailsConfig = $config['emails'];
    // Função para obter a lista de usuários
    function obterUsuarios($apiKey, $dominio) {
        $url = "https://mail.zoho.com/api/organization/$dominio/users";
        $headers = [
            "Authorization: Zoho-oauthtoken $apiKey"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Erro de cURL: " . curl_error($ch) . "\n";
            curl_close($ch);
            return [];
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if (isset($data['data'])) {
                return $data['data'];
            }
            echo "Erro ao processar resposta de usuários: $response\n";
        } else {
            echo "Erro ao obter usuários. Código HTTP: $httpCode. Resposta: $response\n";
        }
        return [];
    }
    // Função para verificar o espaço de armazenamento de um usuário
    function verificarEspaco($apiKey, $email) {
        $url = "https://mail.zoho.com/api/accounts/$email/quota";
        $headers = [
            "Authorization: Zoho-oauthtoken $apiKey"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Erro de cURL ao verificar espaço: " . curl_error($ch) . "\n";
            curl_close($ch);
            return null;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            $data = json_decode($response, true);
            if (isset($data['data'])) {
                $usedStorageGB = $data['data']['usedStorage'] / (1024 ** 3);
                $totalStorageGB = $data['data']['totalStorage'] / (1024 ** 3);
                return $totalStorageGB - $usedStorageGB;
            }
            echo "Erro ao processar resposta de espaço para $email: $response\n";
        } else {
            echo "Erro ao verificar espaço para $email. Código HTTP: $httpCode. Resposta: $response\n";
        }
        return null;
    }
    // Função para enviar e-mail de alerta
    function enviarAlerta($apiKey, $email, $assunto, $mensagem, $nomeUsuario) {
        $url = "https://mail.zoho.com/api/accounts/$email/messages";
        $headers = [
            "Authorization: Zoho-oauthtoken $apiKey",
            "Content-Type: application/json"
        ];
        // Substitui a variável [Nome do Usuário] pelo nome real do usuário
        $mensagemFormatada = str_replace('[Nome do Usuário]', $nomeUsuario, $mensagem);
        $data = [
            "fromAddress" => "admin@seu_dominio.com",
            "toAddress" => [$email],
            "subject" => $assunto,
            "content" => $mensagemFormatada
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Erro de cURL ao enviar e-mail: " . curl_error($ch) . "\n";
            curl_close($ch);
            return;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200) {
            echo "E-mail de alerta enviado para $email\n";
        } else {
            echo "Erro ao enviar e-mail para $email. Código HTTP: $httpCode. Resposta: $response\n";
        }
    }
    // Função para verificar a última data de envio de e-mail
    function podeEnviarEmail($email, $limite, $frequencia) {
        $arquivo = "logs/{$email}_limite_{$limite}.log";
        if (file_exists($arquivo)) {
            $ultimaDataEnvio = file_get_contents($arquivo);
            $diasDesdeUltimoEnvio = (time() - strtotime($ultimaDataEnvio)) / (60 * 60 * 24);
            return $diasDesdeUltimoEnvio >= $frequencia;
        }
        return true;
    }
    // Função para registrar a data de envio de e-mail
    function registrarEnvio($email, $limite) {
        $arquivo = "logs/{$email}_limite_{$limite}.log";
        if (!is_dir('logs')) {
            mkdir('logs', 0777, true);
        }
        file_put_contents($arquivo, date('Y-m-d H:i:s'));
    }
    // Verifica usuários e envia alertas
    $usuarios = obterUsuarios($apiKey, $dominio);
    foreach ($usuarios as $usuario) {
        $espacoLivreGB = verificarEspaco($apiKey, $usuario['email']);
        if ($espacoLivreGB !== null) {
            foreach ($emailsConfig as $config) {
                if ($espacoLivreGB <= $config['limite'] && podeEnviarEmail($usuario['email'], $config['limite'], $config['frequencia'])) {
                    enviarAlerta($apiKey, $usuario['email'], $config['assunto'], $config['mensagem'], $usuario['displayName']);
                    registrarEnvio($usuario['email'], $config['limite']);
                    break; // Enviar apenas o e-mail de menor espaço relevante
                }
            }
        } else {
            echo "Erro: Espaço livre não pôde ser determinado para " . $usuario['email'] . "\n";
        }
    }