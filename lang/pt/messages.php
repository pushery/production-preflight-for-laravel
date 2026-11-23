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
        'pass_optional' => 'O .env nomeia todas as chaves do seu ficheiro de exemplo, exceto :optional, que a aplicação declara opcionais.',
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
        'endings_statement' => '{1} uma linha termina num retorno de carro, que uma shell mantém no fim do seu valor|[2,*] :count linhas terminam num retorno de carro, que uma shell mantém no fim de cada valor',
        'whitespace' => 'o valor termina em espaços, que uma ferramenta que toma a linha à letra mantém, como o --env-file do Docker',
    ],

    'development-stage-server' => [
        'label' => 'Ambiente de desenvolvimento num servidor',
        'pass' => 'app.url indica um host sob o qual uma máquina de desenvolvimento é servida.',
        'not_development' => 'Nada a verificar: só um ambiente de desenvolvimento é lido em relação ao seu host, e a aplicação declara o ambiente :stage.',
        'no_host' => 'app.url não indica nenhum host, por isso não se pode avaliar se um servidor declara um ambiente de desenvolvimento.',
        'fail' => 'APP_ENV indica um ambiente de desenvolvimento e app.url um host sob o qual nenhuma máquina de desenvolvimento é servida: um servidor que declara um ambiente de desenvolvimento, no qual todas as verificações de produção passam.',
        'finding' => 'não indica nenhum host sob o qual uma máquina de desenvolvimento é servida, por isso APP_ENV mantém provavelmente o valor de .env.example',
    ],

    'app-debug' => [
        'label' => 'Modo de depuração',
        'pass' => 'O modo de depuração está desligado.',
        'fail' => 'O modo de depuração está ligado em produção.',
        'finding' => 'uma página de erro mostra o stack trace e o ambiente, incluindo o conteúdo do .env, a quem provocar o erro',
    ],

    'app-key' => [
        'label' => 'Chave da aplicação',
        'pass' => 'A chave da aplicação está definida, e nem ela nem uma chave anterior é um marcador ou uma chave do ficheiro de exemplo.',
        'fail' => 'A chave da aplicação falta, ou uma chave que a aplicação aceita é conhecida por outros, por isso sessões, cookies, dados cifrados e URL assinados podem ser falsificados.',
        'missing' => 'nenhuma chave está definida',
        'placeholder' => 'a chave é um marcador que uma versão antiga do Laravel incluía no seu ficheiro de exemplo',
        'committed' => 'a chave é a do ficheiro de exemplo versionado',
        'previous_placeholder' => 'a chave anterior na posição :position é um marcador que uma versão antiga do Laravel incluía no seu ficheiro de exemplo, e a aplicação continua a aceitar cookies, dados cifrados e URL assinados feitos com ela',
        'previous_committed' => 'a chave anterior na posição :position é uma do ficheiro de exemplo versionado, e a aplicação continua a aceitar cookies, dados cifrados e URL assinados feitos com ela',
    ],

    'mail-transport' => [
        'label' => 'Envio de email',
        'pass' => 'O mailer predefinido envia o email para fora do servidor.',
        'fail' => 'O mailer predefinido não entrega nenhum email: registos, redefinições de palavra-passe e faturas desaparecem sem ruído.',
        'warn' => 'O mailer predefinido envia para esta mesma máquina. Um relay aí tem de reencaminhar o email.',
        'unconfigured' => 'o mailer não está configurado, por isso enviar gera um erro',
        'url' => 'o URL do mailer não indica nenhum transporte que o Laravel consiga ler, por isso enviar gera um erro',
        'log' => 'o transporte log escreve cada mensagem no log e não envia nenhuma',
        'array' => 'o transporte array guarda cada mensagem em memória e não envia nenhuma',
        'catcher' => 'o host SMTP é um capturador de email, onde as mensagens param e nenhuma caixa as recebe',
        'local' => 'o host SMTP é a própria máquina: um relay válido, ou uma definição de desenvolvimento que ninguém alterou',
    ],

    'health-notifications' => [
        'label' => 'Um segundo caminho para os alertas',
        'pass' => 'Uma verificação falhada chega até si por um canal além do email.',
        'pulled' => 'O Oh Dear recolhe os resultados, independentemente do email da aplicação.',
        'pulled_own' => 'Um monitor recolhe os resultados no endpoint que production-preflight.health_endpoint indica, independentemente do email da aplicação.',
        'warn' => 'Uma verificação falhada em operação não chegaria até si se o email deixasse de funcionar.',
        'off' => 'as notificações estão desligadas, e nada recolhe os resultados',
        'mail_only' => 'uma verificação falhada só é entregue por email, e quando o email deixa de funcionar, a mensagem sobre isso é precisamente a que não chega. Adicione um webhook do Slack ou o endpoint do Oh Dear, ou indique em production-preflight.health_endpoint um endpoint próprio que um monitor consulte.',
        'none' => 'nenhum canal entrega uma verificação falhada',
        'unserved' => 'O Oh Dear está ligado, e o laravel-health não lhe disponibiliza nenhum endpoint onde recolher os resultados.',
        'unserved_secret' => 'o laravel-health só disponibiliza o endpoint do Oh Dear com um segredo, e nenhum está definido, por isso o Oh Dear não encontra nada para recolher. Defina OH_DEAR_HEALTH_CHECK_SECRET com o segredo que o Oh Dear mostra nas definições Application Health.',
        'unserved_url' => 'o laravel-health só disponibiliza o endpoint do Oh Dear num url, e nenhum está definido, por isso o Oh Dear não encontra nada para recolher. Defina-o com o caminho que o Oh Dear consulta, por omissão /oh-dear-health-check-results.',
    ],

    'test-mode-keys' => [
        'label' => 'Credenciais em modo de teste',
        'pass' => 'Nenhuma credencial da configuração está em modo de teste.',
        'staging' => 'Nada a verificar: um servidor de staging recebe pagamentos com chaves de teste, e uma chave real movimentaria ali dinheiro de verdade.',
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
        'unattended' => 'A ligação de fila predefinida executa cada job sem nenhum worker.',
        'deferred' => 'o driver deferred executa cada job no processo PHP que o colocou na fila, depois de a resposta ser enviada: um job falhado não é repetido, e um job por terminar perde-se quando esse processo termina. Uma escolha para uma instalação pequena: assuma-a se for a sua.',
        'background' => 'o driver background executa cada job num processo PHP iniciado para ele: um job falhado não é repetido, e um job por terminar perde-se quando esse processo termina. Uma escolha para uma instalação pequena: assuma-a se for a sua.',
    ],

    'session-driver' => [
        'label' => 'Driver de sessão',
        'pass' => 'As sessões sobrevivem ao pedido.',
        'fail' => 'As sessões não sobrevivem ao pedido.',
        'unconfigured' => 'o driver de sessão não está configurado, por isso o primeiro pedido que inicia uma sessão gera um erro',
        'array' => 'o driver array esquece cada sessão depois do pedido, por isso cada utilizador fica desligado depois de cada página',
    ],

    'cache-store' => [
        'label' => 'Armazenamento de cache',
        'pass' => 'O armazenamento de cache predefinido guarda o que recebe.',
        'fail' => 'O armazenamento de cache predefinido não guarda nada para além do pedido.',
        'unconfigured' => 'o armazenamento predefinido não está configurado, por isso o primeiro acesso à cache gera um erro',
        'transient' => 'os limitadores de pedidos, os bloqueios atómicos e o sinal de reinício dos workers vivem na cache e desaparecem depois de cada pedido',
        'fail_session' => 'O armazenamento de cache predefinido guarda a cache na sessão de cada visitante.',
        'session' => 'um limitador de pedidos conta por visitante e recomeça do zero para um cliente sem cookie, Cache::lock() e um job único terminam num erro porque o armazenamento de sessão não tem bloqueios, e um comando, incluindo queue:restart, escreve numa sessão que ninguém guarda, por isso nenhum worker recebe o sinal de reinício',
        'warn' => 'O armazenamento de cache predefinido recorre a um que não guarda nada para além do pedido ou que guarda a cache por visitante.',
        'falls_back' => 'assim que os armazenamentos anteriores falham, o armazenamento failover serve a partir de um que não guarda nada para além do pedido ou que guarda a cache na sessão de cada visitante, e os limitadores de pedidos, os bloqueios atómicos e o sinal de reinício dos workers perdem-se enquanto isso dura',
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
        'within_grace' => 'o .env mudou depois de a configuração ter ido para a cache, há menos de :minutes minutos. Um deploy tem esse tempo para a voltar a pôr em cache.',
        'fail' => 'A aplicação corre com uma configuração anterior à última alteração do .env.',
        'finding' => 'o .env mudou depois de a configuração ter ido para a cache, por isso os novos valores não se aplicam. Execute php artisan config:cache.',
        'not_cached' => 'A configuração não está em cache, por isso cada pedido lê o .env e uma alteração aplica-se de imediato.',
        'no_env' => 'A configuração em cache é toda a configuração, e não há nenhum .env ao lado.',
    ],

    'queue-restart' => [
        'label' => 'Workers da fila depois do deploy',
        'pass' => 'Foi pedido aos workers da fila que reiniciassem depois do último deploy.',
        'fail' => 'Os workers da fila correm o código de antes do último deploy.',
        'fail_missing' => 'Os workers da fila talvez corram o código de antes do último deploy: não há nenhum sinal de reinício no armazenamento de cache.',
        'missing' => 'ou queue:restart não correu depois do deploy, ou a cache perdeu o sinal desde então, limpa por cache:clear ou optimize:clear, reiniciada sem persistência ou despejada, e os workers reiniciaram quando ele desapareceu. Acrescente php artisan reload, ou php artisan queue:restart, ao fim do script de deploy, depois de php artisan preflight --deploy e depois de qualquer cache:clear ou optimize:clear.',
        'stale' => 'o último sinal de reinício é mais antigo do que o último deploy. Acrescente php artisan reload, ou php artisan queue:restart, ao fim do script de deploy, depois de php artisan preflight --deploy.',
        'no_worker' => 'A ligação de fila predefinida não tem nenhum worker para reiniciar.',
        'no_deploy' => 'Não verificado: nenhum deploy está registado. Acrescente php artisan preflight --deploy ao script de deploy, antes de php artisan reload ou php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Migrações pendentes',
        'pass' => 'Todas as migrações foram executadas.',
        'pass_skipped' => 'Todas as migrações foram executadas, exceto :migrations, que a aplicação salta.',
        'fail' => 'Migrações que não foram executadas: :count. O código é novo, o esquema não.',
        'pending' => 'não foi executada',
        'no_migrator' => 'Não verificado: a aplicação não tem migrador.',
    ],

    'writable-directories' => [
        'label' => 'Pastas com escrita',
        'pass' => 'Todas as pastas onde o Laravel escreve permitem escrita.',
        'fail' => 'Caminhos onde o Laravel não consegue escrever em :server, verificado como :user: :count. A primeira ação de um utilizador que escreve falha.',
        'fail_unknown_user' => 'Caminhos onde o Laravel não consegue escrever em :server: :count. Não foi possível nomear o utilizador com que esta verificação corre. A primeira ação de um utilizador que escreve falha.',
        'missing' => 'a pasta falta',
        'not_creatable' => 'a pasta falta, e a pasta que a contém não permite escrita ao utilizador que executa esta verificação, por isso o Laravel não a consegue criar',
        'read_only' => 'a pasta não permite escrita ao utilizador que executa esta verificação',
        'log_read_only' => 'o ficheiro de registo mais recente não permite escrita ao utilizador que executa esta verificação, e uma linha que ele regista perde-se',
        'log_directory_read_only' => 'a pasta do ficheiro de registo mais recente não permite escrita ao utilizador que executa esta verificação, e o ficheiro do dia seguinte não pode ser criado',
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
        'redirected' => 'Não verificado: o site redireciona o pedido para outro endereço, e a sonda não segue nenhum redirecionamento. Defina em app.url o endereço para onde redireciona.',
        'moved' => 'redirecionado para :target',
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
        'web_request' => 'Não verificado: um pedido web corre com o PHP do servidor web, que a verificação compararia consigo mesmo. A verificação responde na linha de comandos, como na execução agendada do laravel-health.',
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

    'exception-arguments' => [
        'label' => 'Argumentos nos rastos de exceções',
        'pass' => 'Os rastos das exceções na linha de comandos não guardam argumentos. O PHP-FPM lê um php.ini próprio e é avaliado quando a verificação corre num pedido web.',
        'pass_web' => 'Os rastos das exceções no servidor web não guardam argumentos. A linha de comandos lê um php.ini próprio e é avaliada quando a verificação corre na linha de comandos.',
        'fail' => 'Os rastos das exceções na linha de comandos guardam os argumentos de cada chamada.',
        'fail_web' => 'Os rastos das exceções no servidor web guardam os argumentos de cada chamada.',
        'finding' => 'O Laravel regista o rasto de cada exceção reportada, e um segredo curto aparece lá inteiro. Defina zend.exception_ignore_args = On no php.ini da linha de comandos e do PHP-FPM, e recarregue o PHP.',
    ],

];
