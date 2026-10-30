<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Aprovado',
        'warn' => 'Aviso',
        'fail' => 'Falha',
        'undetermined' => 'Não verificado',
    ],

    'acknowledged' => 'Assumido: :reason Encontrado: :summary',
    'unreadable' => 'Não verificado: não é possível ler :path.',
    'empty' => '(vazio)',
    'line' => ':key (linha :line)',

    'stage' => [
        'undetermined' => 'Não verificado: este pacote não conhece o ambiente que a aplicação declara. Adicione-o em production-preflight.stages.',
        'development' => 'Nada a verificar: as regras de produção aplicam-se em produção e em staging, e a aplicação declara um ambiente de desenvolvimento.',
    ],

    'command' => [
        'crashed' => 'Não verificado: a verificação parou com :exception.',
        'evidence' => 'valor: :evidence',
        'file' => 'Ficheiro: :path',
        'unreadable' => 'Não é possível ler: :path',
        'unreadable_note' => 'Isto não é o mesmo que limpo: nada foi verificado.',
        'unrecorded' => 'Não foi possível registar o deploy em :path, por isso a verificação queue-restart não tem com que comparar.',
        'values_in_deploy' => 'Um deploy não mostra valores: a sua saída é guardada. Execute o comando sem --deploy para os ver.',
        'exit' => [
            '0' => 'Nenhuma verificação falhou.',
            '1' => 'Pelo menos uma verificação falhou.',
            '2' => 'Nenhuma verificação falhou, e pelo menos uma não conseguiu olhar. Isto não é uma aprovação.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Chaves definidas duas vezes',
        'pass' => 'Nenhuma chave está definida duas vezes.',
        'fail' => 'Chaves definidas mais de uma vez: :count, pelo menos uma com valores DIFERENTES. Aplica-se o valor definido em ÚLTIMO lugar, por isso há uma definição no ficheiro que não se aplica. Remova as linhas a mais em vez de acrescentar outra.',
        'warn' => 'Chaves definidas mais de uma vez: :count, cada uma com valores iguais. Aplica-se o valor definido em ÚLTIMO lugar. Remova as linhas a mais em vez de acrescentar outra.',
        'finding' => 'definida nas linhas :lines, :values',
        'different' => 'valores DIFERENTES',
        'equal' => 'valores iguais',
        'evidence' => ':lines, aplica-se: :value',
        'line' => 'linha :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Chaves do ficheiro de exemplo',
        'pass' => 'O .env e o seu ficheiro de exemplo nomeiam as mesmas chaves.',
        'fail' => 'Chaves do ficheiro de exemplo que faltam no .env: :missing. Cada uma devolve null, e a sua funcionalidade não faz nada sem dizer nada. Chaves do .env que faltam no ficheiro de exemplo: :undocumented.',
        'warn' => 'Chaves do .env que faltam no ficheiro de exemplo: :undocumented. A próxima instalação nasce do exemplo e não as terá.',
        'missing' => 'nomeada no ficheiro de exemplo, falta no .env',
        'undocumented' => 'definida no .env, falta no ficheiro de exemplo',
    ],

    'placeholder-values' => [
        'label' => 'Valores de marcador',
        'pass' => 'Nenhum valor do .env é um marcador.',
        'fail' => 'Valores que nunca foram substituídos: :count.',
        'placeholder' => 'o valor é um marcador',
        'from_example' => 'o valor é o do ficheiro de exemplo versionado, por isso qualquer pessoa que leia o repositório o conhece',
        'required' => 'obrigatória, e falta ou está vazia',
    ],

    'unquoted-hash' => [
        'label' => '# sem aspas',
        'pass' => 'Nenhum valor sem aspas contém um # que o corte.',
        'fail' => 'Valores cortados num # sem aspas: :count. A aplicação só lê a parte anterior.',
        'warn' => 'Valores que parecem texto cortado num # sem aspas: :count.',
        'cut' => 'um # sem aspas dentro do valor inicia um comentário, por isso a aplicação só lê a parte anterior. Coloque o valor entre aspas.',
        'ambiguous' => 'o # depois do espaço inicia um comentário, por isso a aplicação só lê a parte anterior. Coloque o valor entre aspas, ou escreva um espaço depois do # se for um comentário.',
        'evidence' => 'a aplicação lê :value, o ficheiro diz :raw',
    ],

    'env-bytes' => [
        'label' => 'Bytes invisíveis',
        'pass' => 'Sem marca de ordem de bytes, sem retorno de carro, sem espaços depois de um valor.',
        'warn' => 'O Laravel lê estes bytes corretamente. Uma shell ou uma ferramenta que lê o ficheiro como texto não, e vê então valores diferentes dos que lá estão.',
        'mark' => 'marca de ordem de bytes',
        'mark_statement' => 'o ficheiro começa com uma, e uma shell que carrega o ficheiro toma-a como parte do primeiro nome',
        'endings' => 'fins de linha',
        'endings_statement' => ':count linhas terminam num retorno de carro, que uma shell mantém no fim de cada valor',
        'whitespace' => 'o valor termina em espaços, que uma ferramenta que toma a linha à letra mantém, como o --env-file do Docker',
    ],

    'app-debug' => [
        'label' => 'Modo de depuração',
        'pass' => 'O modo de depuração está desligado.',
        'fail' => 'O modo de depuração está ligado em produção.',
        'finding' => 'uma página de erro mostra o stack trace e o ambiente, incluindo o conteúdo do .env, a quem provocar o erro',
    ],

    'app-key' => [
        'label' => 'Chave da aplicação',
        'pass' => 'A chave da aplicação está definida, e não é nem um marcador nem a do ficheiro de exemplo.',
        'fail' => 'A chave da aplicação falta ou é conhecida por outros, por isso sessões, cookies e dados cifrados podem ser falsificados.',
        'missing' => 'nenhuma chave está definida',
        'placeholder' => 'a chave é um marcador que uma versão antiga do Laravel incluía no seu ficheiro de exemplo',
        'committed' => 'a chave é a do ficheiro de exemplo versionado',
    ],

    'mail-transport' => [
        'label' => 'Envio de email',
        'pass' => 'O mailer predefinido envia o email para fora do servidor.',
        'fail' => 'O mailer predefinido não entrega nenhum email: registos, redefinições de palavra-passe e faturas desaparecem sem ruído.',
        'warn' => 'O mailer predefinido envia para esta mesma máquina. Um relay aí tem de reencaminhar o email.',
        'unconfigured' => 'o mailer não está configurado, por isso enviar gera um erro',
        'log' => 'o transporte log escreve cada mensagem no log e não envia nenhuma',
        'array' => 'o transporte array guarda cada mensagem em memória e não envia nenhuma',
        'catcher' => 'o host SMTP é um capturador de email, onde as mensagens param e nenhuma caixa as recebe',
        'local' => 'o host SMTP é a própria máquina: um relay válido, ou uma definição de desenvolvimento que ninguém alterou',
    ],

    'health-notifications' => [
        'label' => 'Um segundo caminho para os alertas',
        'pass' => 'Uma verificação falhada chega até si por um canal além do email.',
        'pulled' => 'O Oh Dear recolhe os resultados, independentemente do email da aplicação.',
        'warn' => 'Uma verificação falhada em operação não chegaria até si se o email deixasse de funcionar.',
        'off' => 'as notificações estão desligadas, e nada recolhe os resultados',
        'mail_only' => 'uma verificação falhada só é entregue por email, e quando o email deixa de funcionar, a mensagem sobre isso é precisamente a que não chega. Adicione um webhook do Slack ou o endpoint do Oh Dear.',
        'none' => 'nenhum canal entrega uma verificação falhada',
    ],

    'test-mode-keys' => [
        'label' => 'Credenciais em modo de teste',
        'pass' => 'Nenhuma credencial da configuração está em modo de teste.',
        'fail' => 'Definições em modo de teste: :count. Não se move dinheiro real.',
        'stripe' => 'uma chave do Stripe em modo de teste',
        'mollie' => 'uma chave do Mollie em modo de teste',
        'sandbox' => 'o modo sandbox está ligado',
    ],

    'queue-connection' => [
        'label' => 'Ligação da fila',
        'pass' => 'A ligação de fila predefinida entrega os jobs a workers.',
        'fail' => 'A ligação de fila predefinida não executa nenhum job.',
        'warn' => 'A ligação de fila predefinida executa cada job dentro do pedido.',
        'unconfigured' => 'a ligação predefinida não está configurada, por isso colocar um job na fila gera um erro',
        'null' => 'o driver null descarta cada job que recebe',
        'sync' => 'os jobs correm dentro do pedido que os coloca na fila, por isso cada resposta espera por eles. Uma escolha para uma instalação pequena: assuma-a se for a sua.',
    ],

    'session-driver' => [
        'label' => 'Driver de sessão',
        'pass' => 'As sessões sobrevivem ao pedido.',
        'fail' => 'As sessões duram um pedido.',
        'array' => 'o driver array esquece cada sessão depois do pedido, por isso cada utilizador fica desligado depois de cada página',
    ],

    'cache-store' => [
        'label' => 'Armazenamento de cache',
        'pass' => 'O armazenamento de cache predefinido guarda o que recebe.',
        'fail' => 'O armazenamento de cache predefinido não guarda nada para além do pedido.',
        'unconfigured' => 'o armazenamento predefinido não está configurado, por isso o primeiro acesso à cache gera um erro',
        'transient' => 'os limitadores de pedidos, os bloqueios atómicos e o sinal de reinício dos workers vivem na cache e desaparecem depois de cada pedido',
    ],

    'secure-cookie' => [
        'label' => 'Cookie de sessão segura',
        'pass' => 'A cookie de sessão tem a flag Secure.',
        'fail' => 'Falta a flag Secure à cookie de sessão num site que funciona em HTTPS.',
        'finding' => 'um navegador também envia a cookie por HTTP sem cifra, onde qualquer pessoa na rede a pode ler. Defina SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url indica http, onde uma cookie Secure nem sequer seria enviada.',
        'no_scheme' => 'Não verificado: app.url não indica nenhum esquema, por isso não se sabe se o site funciona em HTTPS.',
    ],

    'debug-tools' => [
        'label' => 'Ferramentas de depuração',
        'pass' => 'Nenhuma ferramenta de depuração está carregada.',
        'fail' => 'Uma ferramenta de depuração está aberta em produção.',
        'warn' => 'Uma ferramenta de depuração está carregada e adormecida.',
        'telescope' => 'o Telescope regista pedidos, consultas e emails, e o seu painel está a um gate do público',
        'debugbar' => 'a barra de depuração mostra a cada visitante as consultas, a sessão e o pedido de cada página',
        'ignition' => 'adormecido enquanto o modo de depuração está desligado, e uma vista aberta da aplicação assim que é ligado',
    ],

    'log-level' => [
        'label' => 'Nível de log',
        'pass' => 'O canal de log predefinido não regista ao nível debug.',
        'warn' => 'O canal de log predefinido regista ao nível debug.',
        'unconfigured' => 'Não verificado: o canal de log predefinido não está configurado.',
        'debug' => 'cada detalhe vai parar ao disco, que enche a um ritmo calculável',
    ],

    'config-cache-fresh' => [
        'label' => 'Configuração em cache',
        'pass' => 'A configuração em cache é mais recente do que o .env.',
        'fail' => 'A aplicação corre com uma configuração anterior à última alteração do .env.',
        'finding' => 'o .env mudou depois de a configuração ter ido para a cache, por isso os novos valores não se aplicam. Execute php artisan config:cache.',
        'not_cached' => 'A configuração não está em cache, por isso cada pedido lê o .env e uma alteração aplica-se de imediato.',
        'no_env' => 'A configuração em cache é toda a configuração, e não há nenhum .env ao lado.',
    ],

    'queue-restart' => [
        'label' => 'Workers da fila depois do deploy',
        'pass' => 'Foi pedido aos workers da fila que reiniciassem depois do último deploy.',
        'fail' => 'Os workers da fila correm o código de antes do último deploy.',
        'never' => 'nunca foi enviado um sinal de reinício, por isso os workers correm o código com que arrancaram. Acrescente php artisan queue:restart ao fim do script de deploy.',
        'stale' => 'o último sinal de reinício é mais antigo do que o último deploy. Acrescente php artisan queue:restart ao fim do script de deploy.',
        'no_worker' => 'A ligação de fila predefinida não tem nenhum worker para reiniciar.',
        'no_deploy' => 'Não verificado: nenhum deploy está registado. Acrescente php artisan preflight --deploy ao script de deploy, antes de php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Migrações pendentes',
        'pass' => 'Todas as migrações foram executadas.',
        'fail' => 'Migrações que não foram executadas: :count. O código é novo, o esquema não.',
        'pending' => 'não foi executada',
        'no_migrator' => 'Não verificado: a aplicação não tem migrador.',
    ],

    'writable-directories' => [
        'label' => 'Pastas com escrita',
        'pass' => 'Todas as pastas onde o Laravel escreve permitem escrita.',
        'fail' => 'Pastas onde o Laravel não consegue escrever: :count. A primeira ação de um utilizador que escreve falha.',
        'missing' => 'a pasta falta',
        'read_only' => 'a pasta não permite escrita ao utilizador que executa esta verificação',
    ],

    'storage-link' => [
        'label' => 'Ligação do storage',
        'pass' => 'Cada ligação de storage configurada aponta para onde deve.',
        'fail' => 'Os ficheiros do disco público respondem 404.',
        'warn' => 'O próximo ficheiro guardado no disco público responderá 404.',
        'elsewhere' => 'a ligação aponta para outro lado',
        'directory' => 'onde devia estar a ligação há uma pasta verdadeira, e um ficheiro guardado mais tarde nunca aparece nela',
        'missing_with_files' => 'a ligação falta, e o seu destino já contém ficheiros. Execute php artisan storage:link.',
        'missing' => 'a ligação falta. Execute php artisan storage:link.',
    ],

    'env-exposure' => [
        'label' => 'Segredos no sistema de ficheiros',
        'pass' => 'O .env não está aberto a todos os utilizadores do servidor, e a raiz web não contém nem .env nem .git.',
        'fail' => 'Ficheiros que entregam segredos: :count.',
        'world_readable' => 'qualquer utilizador do servidor pode lê-lo',
        'world_writable' => 'qualquer utilizador do servidor pode escrevê-lo',
        'web_root' => 'está na raiz web, onde o servidor web o pode servir',
    ],

    'env-over-http' => [
        'label' => 'Segredos por HTTP',
        'pass' => 'O site não serve nem .env nem .git/config.',
        'fail' => 'O site serve um ficheiro secreto a quem o pedir.',
        'served' => 'o site responde 200 com o conteúdo do ficheiro',
        'off' => 'Não verificado: saber se o site serve o .env exige um pedido ao site, e a sonda está desligada. Ligue-a com PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Não verificado: app.url não é um endereço http ou https a consultar.',
        'unanswered' => 'Não verificado: o site não respondeu.',
    ],

    'dev-dependencies' => [
        'label' => 'Dependências de desenvolvimento',
        'pass' => 'Nenhum pacote de desenvolvimento está instalado.',
        'fail' => 'Pacotes de desenvolvimento instalados: :count.',
        'finding' => 'estão instalados executores de testes, depuradores e geradores de código. Instale com composer install --no-dev.',
        'unreadable' => 'Não verificado: não é possível ler :path.',
    ],

    'optimized-autoloader' => [
        'label' => 'Autoloader otimizado',
        'pass' => 'O autoloader está otimizado.',
        'warn' => 'O autoloader procura cada classe no disco.',
        'finding' => 'instale com composer install --optimize-autoloader',
        'unreadable' => 'Não verificado: não é possível ler o composer.json ou o mapa de classes do autoloader.',
    ],

    'php-version-parity' => [
        'label' => 'Versão PHP do servidor web',
        'pass' => 'A linha de comandos e o servidor web usam PHP :cli.',
        'fail' => 'A linha de comandos usa PHP :cli e o servidor web PHP :web.',
        'finding' => 'as migrações e os comandos agendados correm com PHP :cli, a aplicação com PHP :web',
        'unnamed' => 'Não verificado: o PHP do servidor web é um binário que esta verificação executa, e nenhum foi indicado. Defina PRODUCTION_PREFLIGHT_PHP_FPM, por exemplo como /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Não verificado: este servidor não permite iniciar um processo.',
        'unanswered' => 'Não verificado: o binário indicado não comunicou nenhuma versão de PHP.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'O OPcache está ligado e não verifica se os ficheiros mudam.',
        'fail' => 'O OPcache não funciona no servidor web.',
        'warn' => 'O OPcache verifica se cada ficheiro mudou.',
        'not_loaded' => 'a extensão OPcache não está carregada',
        'off' => 'o OPcache está desligado',
        'timestamps' => 'cada pedido verifica se os ficheiros mudaram. Desligue-o e recarregue o PHP em cada deploy.',
        'command_line' => 'Não verificado: a linha de comandos tem as suas próprias definições de OPcache. A verificação responde num pedido web, como o endpoint do laravel-health com resultados recentes.',
    ],

];
