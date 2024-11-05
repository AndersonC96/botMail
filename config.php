<?php
// Configuração das credenciais e limites de espaço
return [
    'apiKey' => 'SUA_CHAVE_DE_API',
    'dominio' => 'seu_dominio.com',
    'emails' => [
        [
            'limite' => 1, // Em GB
            'assunto' => 'Aviso: Seu espaço de armazenamento está quase cheio (menos de 1GB)',
            'mensagem' => "Olá [Nome do Usuário], Estamos te informando que seu espaço de armazenamento está com menos de 1GB disponível. Recomendamos que você faça uma revisão e apague ou transfira alguns arquivos para liberar mais espaço e garantir o funcionamento adequado da sua conta.",
            'frequencia' => 30 // Em dias
        ],
        [
            'limite' => 0.5, // Em GB (500MB)
            'assunto' => 'Alerta: Espaço de armazenamento abaixo de 500MB',
            'mensagem' => "Olá [Nome do Usuário], O espaço de armazenamento da sua conta está agora abaixo de 500MB. A continuidade do uso pode ser afetada em breve. Por favor, considere liberar espaço o mais rápido possível para evitar interrupções.",
            'frequencia' => 15 // Em dias
        ],
        [
            'limite' => 0.1, // Em GB (100MB)
            'assunto' => 'Atenção: Espaço de armazenamento crítico (menos de 100MB)',
            'mensagem' => "Olá [Nome do Usuário], O espaço de armazenamento da sua conta está muito baixo, com menos de 100MB restantes. Para manter a integridade e o bom funcionamento da sua conta, sugerimos uma ação imediata para liberar espaço.",
            'frequencia' => 7 // Em dias
        ],
        [
            'limite' => 0.05, // Em GB (50MB)
            'assunto' => 'Urgente: Seu espaço de armazenamento está quase esgotado!',
            'mensagem' => "Olá [Nome do Usuário], Este é um alerta importante: seu espaço de armazenamento está com 50MB ou menos. Você pode enfrentar problemas de funcionamento e até perda de dados. Por favor, libere espaço imediatamente para evitar interrupções.",
            'frequencia' => 1 // Em dias
        ]
    ]
];
