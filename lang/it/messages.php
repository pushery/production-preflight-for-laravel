<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Superato',
        'warn' => 'Avviso',
        'fail' => 'Fallito',
        'undetermined' => 'Non verificato',
    ],

    'acknowledged' => 'Accettato: :reason Rilevato: :summary',
    'unreadable' => 'Non verificato: :path non è leggibile.',
    'empty' => '(vuoto)',
    'line' => ':key (riga :line)',

    'stage' => [
        'undetermined' => 'Non verificato: questo pacchetto non conosce l’ambiente dichiarato dall’applicazione. Aggiungilo in production-preflight.stages.',
        'development' => 'Niente da verificare: le regole di produzione valgono in produzione e in staging, e l’applicazione dichiara un ambiente di sviluppo.',
    ],

    'command' => [
        'crashed' => 'Non verificato: il controllo si è interrotto con :exception.',
        'evidence' => 'valore: :evidence',
        'file' => 'File: :path',
        'unreadable' => 'Non leggibile: :path',
        'unreadable_note' => 'Non equivale a pulito: non è stato verificato nulla.',
        'unrecorded' => 'Non è stato possibile registrare il deploy in :path, quindi il controllo queue-restart non ha nulla con cui confrontare.',
        'values_in_deploy' => 'In un deploy non si mostrano valori: il suo output viene conservato. Esegui il comando senza --deploy per vederli.',
        'exit' => [
            '0' => 'Nessun controllo è fallito.',
            '1' => 'Almeno un controllo è fallito.',
            '2' => 'Nessun controllo è fallito, e almeno uno non ha potuto guardare. Non è un superamento.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Chiavi impostate due volte',
        'pass' => 'Nessuna chiave è impostata due volte.',
        'fail' => 'Chiavi impostate più di una volta: :count, almeno una con valori DIVERSI. Vale il valore impostato per ULTIMO, quindi un’impostazione è nel file e non vale. Rimuovi le righe in eccesso invece di aggiungerne un’altra.',
        'warn' => 'Chiavi impostate più di una volta: :count, ognuna con valori uguali. Vale il valore impostato per ULTIMO. Rimuovi le righe in eccesso invece di aggiungerne un’altra.',
        'finding' => 'impostata alle righe :lines, :values',
        'different' => 'valori DIVERSI',
        'equal' => 'valori uguali',
        'evidence' => ':lines, vale: :value',
        'line' => 'riga :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Chiavi del file di esempio',
        'pass' => 'Il .env e il suo file di esempio nominano le stesse chiavi.',
        'fail' => 'Chiavi del file di esempio che mancano in .env: :missing. Ognuna restituisce null, e la sua funzione non fa nulla senza dire nulla. Chiavi di .env che mancano nel file di esempio: :undocumented.',
        'warn' => 'Chiavi di .env che mancano nel file di esempio: :undocumented. La prossima installazione nasce dall’esempio e non le avrà.',
        'missing' => 'nominata nel file di esempio, manca in .env',
        'undocumented' => 'impostata in .env, manca nel file di esempio',
    ],

    'placeholder-values' => [
        'label' => 'Valori segnaposto',
        'pass' => 'Nessun valore di .env è un segnaposto.',
        'fail' => 'Valori mai sostituiti: :count.',
        'placeholder' => 'il valore è un segnaposto',
        'from_example' => 'il valore è quello del file di esempio versionato, quindi lo conosce chiunque legga il repository',
        'required' => 'obbligatoria, e manca o è vuota',
    ],

    'unquoted-hash' => [
        'label' => '# senza virgolette',
        'pass' => 'Nessun valore senza virgolette contiene un # che lo tronca.',
        'fail' => 'Valori troncati a un # senza virgolette: :count. L’applicazione legge solo la parte precedente.',
        'warn' => 'Valori che sembrano testo troncato a un # senza virgolette: :count.',
        'cut' => 'un # senza virgolette nel valore apre un commento, quindi l’applicazione legge solo la parte precedente. Metti il valore tra virgolette.',
        'ambiguous' => 'il # dopo lo spazio apre un commento, quindi l’applicazione legge solo la parte precedente. Metti il valore tra virgolette, oppure scrivi uno spazio dopo il # se è un commento.',
        'evidence' => 'l’applicazione legge :value, il file dice :raw',
    ],

    'env-bytes' => [
        'label' => 'Byte invisibili',
        'pass' => 'Nessun byte order mark, nessun ritorno a capo, nessuno spazio dopo un valore.',
        'warn' => 'Laravel legge correttamente questi byte. Una shell o uno strumento che legge il file come testo no, e vede allora valori diversi da quelli scritti.',
        'mark' => 'byte order mark',
        'mark_statement' => 'il file ne inizia con uno, e una shell che carica il file lo considera parte del primo nome',
        'endings' => 'fine riga',
        'endings_statement' => ':count righe finiscono con un ritorno a capo, che una shell conserva alla fine di ogni valore',
        'whitespace' => 'il valore finisce con spazi, che uno strumento che prende la riga alla lettera conserva, come --env-file di Docker',
    ],

    'app-debug' => [
        'label' => 'Modalità debug',
        'pass' => 'La modalità debug è disattivata.',
        'fail' => 'La modalità debug è attiva in produzione.',
        'finding' => 'una pagina di errore mostra lo stack trace e l’ambiente, contenuto di .env compreso, a chiunque provochi l’errore',
    ],

    'app-key' => [
        'label' => 'Chiave dell’applicazione',
        'pass' => 'La chiave dell’applicazione è impostata, e non è né un segnaposto né quella del file di esempio.',
        'fail' => 'La chiave dell’applicazione manca o la conoscono altri, quindi sessioni, cookie e dati cifrati si possono falsificare.',
        'missing' => 'nessuna chiave è impostata',
        'placeholder' => 'la chiave è un segnaposto che una vecchia versione di Laravel includeva nel suo file di esempio',
        'committed' => 'la chiave è quella del file di esempio versionato',
    ],

    'mail-transport' => [
        'label' => 'Invio delle email',
        'pass' => 'Il mailer predefinito invia le email fuori dal server.',
        'fail' => 'Il mailer predefinito non consegna nessuna email: registrazioni, reimpostazioni della password e fatture spariscono senza rumore.',
        'warn' => 'Il mailer predefinito invia a questa stessa macchina. Un relay lì deve inoltrare le email.',
        'unconfigured' => 'il mailer non è configurato, quindi l’invio genera un errore',
        'log' => 'il trasporto log scrive ogni messaggio nel log e non ne invia nessuno',
        'array' => 'il trasporto array tiene ogni messaggio in memoria e non ne invia nessuno',
        'catcher' => 'l’host SMTP è un catturatore di email, dove i messaggi si fermano e nessuna casella li riceve',
        'local' => 'l’host SMTP è la macchina stessa: un relay valido, o un’impostazione di sviluppo che nessuno ha cambiato',
    ],

    'health-notifications' => [
        'label' => 'Una seconda strada per gli avvisi',
        'pass' => 'Un controllo fallito ti raggiunge attraverso un canale oltre all’email.',
        'pulled' => 'Oh Dear preleva i risultati, indipendentemente dall’email dell’applicazione.',
        'warn' => 'Un controllo fallito in esercizio non ti raggiungerebbe se l’email smettesse di funzionare.',
        'off' => 'le notifiche sono disattivate, e nulla preleva i risultati',
        'mail_only' => 'un controllo fallito viene consegnato solo per email, e quando l’email smette di funzionare, il messaggio al riguardo è proprio quello che non arriva. Aggiungi un webhook di Slack o l’endpoint di Oh Dear.',
        'none' => 'nessun canale consegna un controllo fallito',
    ],

    'test-mode-keys' => [
        'label' => 'Credenziali in modalità test',
        'pass' => 'Nessuna credenziale della configurazione è in modalità test.',
        'fail' => 'Impostazioni in modalità test: :count. Non si muove denaro reale.',
        'stripe' => 'una chiave Stripe in modalità test',
        'mollie' => 'una chiave Mollie in modalità test',
        'sandbox' => 'la modalità sandbox è attiva',
    ],

    'queue-connection' => [
        'label' => 'Connessione della coda',
        'pass' => 'La connessione della coda predefinita affida i job ai worker.',
        'fail' => 'La connessione della coda predefinita non esegue nessun job.',
        'warn' => 'La connessione della coda predefinita esegue ogni job dentro la richiesta.',
        'unconfigured' => 'la connessione predefinita non è configurata, quindi accodare un job genera un errore',
        'null' => 'il driver null scarta ogni job che riceve',
        'sync' => 'i job girano dentro la richiesta che li accoda, quindi ogni risposta li aspetta. Una scelta per un’installazione piccola: accettala se è la tua.',
    ],

    'session-driver' => [
        'label' => 'Driver di sessione',
        'pass' => 'Le sessioni sopravvivono alla richiesta.',
        'fail' => 'Le sessioni durano una richiesta.',
        'array' => 'il driver array dimentica ogni sessione dopo la richiesta, quindi ogni utente viene disconnesso dopo ogni pagina',
    ],

    'cache-store' => [
        'label' => 'Archivio della cache',
        'pass' => 'L’archivio della cache predefinito conserva ciò che riceve.',
        'fail' => 'L’archivio della cache predefinito non conserva nulla oltre la richiesta.',
        'unconfigured' => 'l’archivio predefinito non è configurato, quindi il primo accesso alla cache genera un errore',
        'transient' => 'i limitatori di frequenza, i lock atomici e il segnale di riavvio dei worker vivono nella cache e spariscono dopo ogni richiesta',
    ],

    'secure-cookie' => [
        'label' => 'Cookie di sessione sicuro',
        'pass' => 'Il cookie di sessione ha il flag Secure.',
        'fail' => 'Al cookie di sessione manca il flag Secure su un sito che funziona in HTTPS.',
        'finding' => 'un browser invia il cookie anche in HTTP non cifrato, dove chiunque sulla rete può leggerlo. Imposta SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url indica http, dove un cookie Secure non verrebbe inviato affatto.',
        'no_scheme' => 'Non verificato: app.url non indica nessuno schema, quindi non si sa se il sito funziona in HTTPS.',
    ],

    'debug-tools' => [
        'label' => 'Strumenti di debug',
        'pass' => 'Nessuno strumento di debug è caricato.',
        'fail' => 'Uno strumento di debug è aperto in produzione.',
        'warn' => 'Uno strumento di debug è caricato e inattivo.',
        'telescope' => 'Telescope registra richieste, query ed email, e la sua dashboard è a un gate dal pubblico',
        'debugbar' => 'la barra di debug mostra a ogni visitatore le query, la sessione e la richiesta di ogni pagina',
        'ignition' => 'inattivo finché la modalità debug è disattivata, e una vista aperta sull’applicazione appena viene attivata',
    ],

    'log-level' => [
        'label' => 'Livello di log',
        'pass' => 'Il canale di log predefinito non registra a livello debug.',
        'warn' => 'Il canale di log predefinito registra a livello debug.',
        'unconfigured' => 'Non verificato: il canale di log predefinito non è configurato.',
        'debug' => 'ogni dettaglio finisce sul disco, che si riempie a un ritmo calcolabile',
    ],

    'config-cache-fresh' => [
        'label' => 'Configurazione in cache',
        'pass' => 'La configurazione in cache è più recente di .env.',
        'fail' => 'L’applicazione gira su una configurazione precedente all’ultima modifica di .env.',
        'finding' => '.env è cambiato dopo che la configurazione è stata messa in cache, quindi i suoi nuovi valori non valgono. Esegui php artisan config:cache.',
        'not_cached' => 'La configurazione non è in cache, quindi ogni richiesta legge .env e una modifica vale subito.',
        'no_env' => 'La configurazione in cache è tutta la configurazione, e accanto non c’è nessun .env.',
    ],

    'queue-restart' => [
        'label' => 'Worker della coda dopo il deploy',
        'pass' => 'Ai worker della coda è stato chiesto di riavviarsi dopo l’ultimo deploy.',
        'fail' => 'I worker della coda eseguono il codice di prima dell’ultimo deploy.',
        'never' => 'non è mai stato inviato un segnale di riavvio, quindi i worker eseguono il codice con cui sono partiti. Aggiungi php artisan queue:restart alla fine dello script di deploy.',
        'stale' => 'l’ultimo segnale di riavvio è più vecchio dell’ultimo deploy. Aggiungi php artisan queue:restart alla fine dello script di deploy.',
        'no_worker' => 'La connessione della coda predefinita non ha worker da riavviare.',
        'no_deploy' => 'Non verificato: nessun deploy è registrato. Aggiungi php artisan preflight --deploy allo script di deploy, prima di php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Migrazioni in sospeso',
        'pass' => 'Tutte le migrazioni sono state eseguite.',
        'fail' => 'Migrazioni non eseguite: :count. Il codice è nuovo, lo schema no.',
        'pending' => 'non è stata eseguita',
        'no_migrator' => 'Non verificato: l’applicazione non ha un migrator.',
    ],

    'writable-directories' => [
        'label' => 'Cartelle scrivibili',
        'pass' => 'Ogni cartella in cui Laravel scrive è scrivibile.',
        'fail' => 'Cartelle in cui Laravel non può scrivere: :count. La prima azione dell’utente che scrive fallisce.',
        'missing' => 'la cartella manca',
        'read_only' => 'la cartella non è scrivibile per l’utente che esegue questo controllo',
    ],

    'storage-link' => [
        'label' => 'Link dello storage',
        'pass' => 'Ogni link dello storage configurato punta dove deve.',
        'fail' => 'I file del disco pubblico rispondono 404.',
        'warn' => 'Il prossimo file salvato sul disco pubblico risponderà 404.',
        'elsewhere' => 'il link punta altrove',
        'directory' => 'al posto del link c’è una cartella vera, e un file salvato in seguito non vi compare mai',
        'missing_with_files' => 'il link manca, e la sua destinazione contiene già dei file. Esegui php artisan storage:link.',
        'missing' => 'il link manca. Esegui php artisan storage:link.',
    ],

    'env-exposure' => [
        'label' => 'Segreti nel file system',
        'pass' => '.env non è aperto a tutti gli utenti del server, e la root web non contiene né .env né .git.',
        'fail' => 'File che consegnano segreti: :count.',
        'world_readable' => 'qualsiasi utente del server può leggerlo',
        'world_writable' => 'qualsiasi utente del server può scriverlo',
        'web_root' => 'si trova nella root web, dove il server web può servirlo',
    ],

    'env-over-http' => [
        'label' => 'Segreti via HTTP',
        'pass' => 'Il sito non serve né .env né .git/config.',
        'fail' => 'Il sito serve un file segreto a chiunque lo chieda.',
        'served' => 'il sito risponde 200 con il contenuto del file',
        'off' => 'Non verificato: sapere se il sito serve .env richiede una richiesta al sito, e la sonda è disattivata. Attivala con PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Non verificato: app.url non è un indirizzo http o https da interrogare.',
        'unanswered' => 'Non verificato: il sito non ha risposto.',
    ],

    'dev-dependencies' => [
        'label' => 'Dipendenze di sviluppo',
        'pass' => 'Nessun pacchetto di sviluppo è installato.',
        'fail' => 'Pacchetti di sviluppo installati: :count.',
        'finding' => 'sono installati runner di test, debugger e generatori di codice. Installa con composer install --no-dev.',
        'unreadable' => 'Non verificato: :path non è leggibile.',
    ],

    'optimized-autoloader' => [
        'label' => 'Autoloader ottimizzato',
        'pass' => 'L’autoloader è ottimizzato.',
        'warn' => 'L’autoloader cerca ogni classe sul disco.',
        'finding' => 'installa con composer install --optimize-autoloader',
        'unreadable' => 'Non verificato: composer.json o la mappa delle classi dell’autoloader non è leggibile.',
    ],

    'php-version-parity' => [
        'label' => 'Versione PHP del server web',
        'pass' => 'La riga di comando e il server web usano PHP :cli.',
        'fail' => 'La riga di comando usa PHP :cli e il server web PHP :web.',
        'finding' => 'le migrazioni e i comandi pianificati girano con PHP :cli, l’applicazione con PHP :web',
        'unnamed' => 'Non verificato: il PHP del server web è un binario che questo controllo esegue, e non ne è indicato nessuno. Imposta PRODUCTION_PREFLIGHT_PHP_FPM, per esempio su /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Non verificato: questo host non consente di avviare un processo.',
        'unanswered' => 'Non verificato: il binario indicato non ha riportato nessuna versione di PHP.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache è attivo e non controlla se i file cambiano.',
        'fail' => 'OPcache non funziona nel server web.',
        'warn' => 'OPcache controlla se ogni file è cambiato.',
        'not_loaded' => 'l’estensione OPcache non è caricata',
        'off' => 'OPcache è disattivato',
        'timestamps' => 'ogni richiesta controlla se i file sono cambiati. Disattivalo e ricarica PHP a ogni deploy.',
        'command_line' => 'Non verificato: la riga di comando ha impostazioni OPcache proprie. Il controllo risponde in una richiesta web, come l’endpoint di laravel-health con risultati aggiornati.',
    ],

];
