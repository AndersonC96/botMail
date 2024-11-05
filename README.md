# Monitoramento de Espaço de Armazenamento e Envio de Alertas por E-mail

Este projeto em PHP foi desenvolvido para monitorar o espaço de armazenamento de usuários em um domínio específico do Zoho Mail e enviar alertas por e-mail quando o espaço disponível atingir certos limites críticos. A solução inclui um sistema de envio de e-mails com mensagens personalizadas e controle de frequência de envio.

## Estrutura do Projeto

O projeto é composto pelos seguintes arquivos:

* `config.php`: Arquivo de configuração que contém as credenciais da API, o domínio do Zoho e as configurações dos e-mails (limites de espaço, assuntos, mensagens e frequência de envio).
* `verifica_espaco.php`: Script principal que realiza a verificação do espaço de armazenamento e envia alertas conforme as regras definidas no config.php.
* `logs/`: Diretório onde os logs de data de envio de e-mails são armazenados para garantir a frequência de envio definida.

## Pré-requisitos

* **PHP** (versão 7.4 ou superior)
* **Extensão cURL para PHP**
* **Conta e chave de API do Zoho Mail** com permissões apropriadas
* **Permissões de escrita** no diretório logs/

## Instalação e Configuração

1. Clone ou baixe o repositório:

```bash
    git clone https://github.com/seu-usuario/monitoramento-zoho.git
    cd monitoramento-zoho
```

2. Configure o arquivo `config.php`:

* Substitua `'SUA_CHAVE_DE_API'` pela chave de API obtida na sua conta do Zoho.
* Altere `'seu_dominio.com'` para o domínio que deseja monitorar.
* Personalize as mensagens e a frequência de envio de e-mails conforme sua necessidade.

**Exemplo de config.php**:

```bash
<?php
    return [
        'apiKey' => 'SUA_CHAVE_DE_API',
        'dominio' => 'seu_dominio.com',
        'emails' => [
            [
                'limite' => 1,
                'assunto' => 'Aviso: Seu espaço de armazenamento está quase cheio (menos de 1GB)',
                'mensagem' => "Olá [Nome do Usuário], Estamos te informando que seu espaço de armazenamento está com menos de 1GB disponível. Recomendamos que você faça uma revisão e apague ou transfira alguns arquivos para liberar mais espaço e garantir o funcionamento adequado da sua conta.",
                'frequencia' => 30 // Em dias
            ],
            [
                'limite' => 0.5,
                'assunto' => 'Alerta: Espaço de armazenamento abaixo de 500MB',
                'mensagem' => "Olá [Nome do Usuário], O espaço de armazenamento da sua conta está agora abaixo de 500MB. A continuidade do uso pode ser afetada em breve. Por favor, considere liberar espaço o mais rápido possível para evitar interrupções.",
                'frequencia' => 15 // Em dias
            ],
            // Outros níveis de alerta...
        ]
    ];
```

3. Crie o diretório de logs (caso não seja criado automaticamente):

```bash
mkdir logs
```

## Uso

Execute o script `verifica_espaco.php` para iniciar a verificação de espaço e envio de alertas:

```bash
php verifica_espaco.php
```

## Funcionamento

1. Obtém a lista de usuários do domínio usando a API do Zoho Mail.
2. Verifica o espaço de armazenamento de cada usuário e calcula o espaço livre.
3. Envia e-mails de alerta quando o espaço livre está abaixo dos limites definidos no config.php.
4. Registra a data de envio em arquivos de log para controlar a frequência de envio.

## Personalização

* **Mensagens de E-mail**: Personalize os textos de e-mail no config.php. Use [Nome do Usuário] no corpo da mensagem, que será substituído dinamicamente pelo nome do usuário.
* **Frequência de Envio**: Defina quantos dias devem passar antes que o próximo alerta seja enviado para o mesmo usuário, ajustando o campo frequencia em cada configuração de e-mail.

## Estrutura de Diretórios

```bash
monitoramento-zoho/
├── config.php
├── verifica_espaco.php
├── logs/
└── README.md
```

## Explicação Técnica

**Funções Principais**

`obterUsuarios($apiKey, $dominio)`: Obtém a lista de usuários do domínio usando a API do Zoho.
`verificarEspaco($apiKey, $email)`: Verifica o espaço de armazenamento disponível para um usuário específico.
`enviarAlerta($apiKey, $email, $assunto, $mensagem, $nomeUsuario)`: Envia um e-mail de alerta personalizado para o usuário.
`podeEnviarEmail($email, $limite, $frequencia)`: Verifica se já passou tempo suficiente desde o último envio de e-mail para respeitar a frequência definida.
`registrarEnvio($email, $limite)`: Registra a data de envio do e-mail em um arquivo de log.

## Logs

Os logs são armazenados em arquivos separados para cada usuário e cada limite de espaço. O nome do arquivo segue o formato: `logs/{email}_limite_{limite}.log`.

## Considerações de Segurança

* Proteja suas credenciais de API e evite deixá-las expostas em repositórios públicos.
* Restrinja permissões de escrita no diretório `logs` para evitar manipulações externas.