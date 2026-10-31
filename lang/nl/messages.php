<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Geslaagd',
        'warn' => 'Waarschuwing',
        'fail' => 'Mislukt',
        'undetermined' => 'Niet gecontroleerd',
    ],

    'acknowledged' => 'Geaccepteerd: :reason Gevonden: :summary',
    'unreadable' => 'Niet gecontroleerd: :path is niet leesbaar.',
    'empty' => '(leeg)',
    'line' => ':key (regel :line)',

    'stage' => [
        'undetermined' => 'Niet gecontroleerd: dit pakket kent de omgeving niet die de applicatie opgeeft. Voeg hem toe onder production-preflight.stages.',
        'development' => 'Niets te controleren: de productieregels gelden in productie en op staging, en de applicatie geeft een ontwikkelomgeving op.',
    ],

    'command' => [
        'crashed' => 'Niet gecontroleerd: de controle stopte met :exception.',
        'evidence' => 'waarde: :evidence',
        'file' => 'Bestand: :path',
        'unreadable' => 'Niet leesbaar: :path',
        'unreadable_note' => 'Dat is niet hetzelfde als schoon: er is niets gecontroleerd.',
        'unrecorded' => 'De deploy kon niet worden vastgelegd in :path, dus de controle queue-restart heeft niets om mee te vergelijken.',
        'values_in_deploy' => 'In een deploy worden geen waarden getoond: de uitvoer blijft bewaard. Voer de opdracht uit zonder --deploy om ze te zien.',
        'exit' => [
            '0' => 'Geen controle is mislukt.',
            '1' => 'Minstens één controle is mislukt.',
            '2' => 'Geen controle is mislukt, en minstens één kon niet kijken. Dat is geen geslaagd.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Dubbel gezette sleutels',
        'pass' => 'Geen sleutel is dubbel gezet.',
        'fail' => 'Sleutels die vaker dan één keer zijn gezet: :count, minstens één met VERSCHILLENDE waarden. De LAATST gezette waarde geldt, dus er staat een instelling in het bestand die niet geldt. Verwijder de overbodige regels in plaats van er nog een toe te voegen.',
        'warn' => 'Sleutels die vaker dan één keer zijn gezet: :count, elk met gelijke waarden. De LAATST gezette waarde geldt. Verwijder de overbodige regels in plaats van er nog een toe te voegen.',
        'finding' => 'gezet op de regels :lines, :values',
        'different' => 'VERSCHILLENDE waarden',
        'equal' => 'gelijke waarden',
        'evidence' => ':lines, geldt: :value',
        'line' => 'regel :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Sleutels van het voorbeeldbestand',
        'pass' => 'De .env en het voorbeeldbestand noemen dezelfde sleutels.',
        'pass_optional' => 'De .env noemt elke sleutel van het voorbeeldbestand, behalve :optional, die de applicatie als optioneel opgeeft.',
        'fail' => 'Sleutels van het voorbeeldbestand die in .env ontbreken: :missing. Elke geeft null, en de bijbehorende functie doet zonder een woord niets. Sleutels van .env die in het voorbeeldbestand ontbreken: :undocumented.',
        'warn' => 'Sleutels van .env die in het voorbeeldbestand ontbreken: :undocumented. De volgende installatie ontstaat uit het voorbeeld en zal ze missen.',
        'missing' => 'genoemd in het voorbeeldbestand, ontbreekt in .env',
        'undocumented' => 'gezet in .env, ontbreekt in het voorbeeldbestand',
    ],

    'placeholder-values' => [
        'label' => 'Plaatshouderwaarden',
        'pass' => 'Geen waarde van .env is een plaatshouder.',
        'fail' => 'Waarden die nooit zijn vervangen: :count.',
        'placeholder' => 'de waarde is een plaatshouder',
        'from_example' => 'de waarde is die uit het ingecheckte voorbeeldbestand, dus iedereen die de repository leest kent hem',
        'required' => 'verplicht, en ontbreekt of is leeg',
    ],

    'unquoted-hash' => [
        'label' => '# zonder aanhalingstekens',
        'pass' => 'Geen waarde zonder aanhalingstekens bevat een # die hem afkapt.',
        'fail' => 'Waarden afgekapt bij een # zonder aanhalingstekens: :count. De applicatie leest alleen het deel ervoor.',
        'warn' => 'Waarden die eruitzien als tekst die bij een # zonder aanhalingstekens is afgekapt: :count.',
        'cut' => 'een # zonder aanhalingstekens in de waarde begint een commentaar, dus de applicatie leest alleen het deel ervoor. Zet de waarde tussen aanhalingstekens.',
        'ambiguous' => 'de # na de spatie begint een commentaar, dus de applicatie leest alleen het deel ervoor. Zet de waarde tussen aanhalingstekens, of schrijf een spatie na de # als het een commentaar is.',
        'evidence' => 'de applicatie leest :value, in het bestand staat :raw',
    ],

    'env-bytes' => [
        'label' => 'Onzichtbare bytes',
        'pass' => 'Geen byte order mark, geen carriage return, geen witruimte na een waarde.',
        'warn' => 'Laravel leest deze bytes correct. Een shell of een tool die het bestand als tekst leest doet dat niet, en ziet dan andere waarden dan die er staan.',
        'mark' => 'byte order mark',
        'mark_statement' => 'het bestand begint ermee, en een shell die het bestand inleest houdt hem voor een deel van de eerste naam',
        'endings' => 'regeleinden',
        'endings_statement' => ':count regels eindigen op een carriage return, die een shell aan het einde van elke waarde behoudt',
        'whitespace' => 'de waarde eindigt op witruimte, die een tool behoudt die de regel letterlijk neemt, zoals --env-file van Docker',
    ],

    'development-stage-server' => [
        'label' => 'Ontwikkelomgeving op een server',
        'pass' => 'De applicatie wordt geserveerd op :host, een machine voor ontwikkeling.',
        'not_development' => 'Niets te controleren: alleen een ontwikkelomgeving wordt tegen haar host gelezen, en de applicatie geeft de omgeving :stage op.',
        'no_host' => 'app.url noemt geen host, dus valt niet te beoordelen of een server een ontwikkelomgeving opgeeft.',
        'fail' => 'APP_ENV is :env en app.url noemt :host: een server die een ontwikkelomgeving opgeeft, waarop elke productiecontrole slaagt.',
        'finding' => ':host is geen host waaronder een machine voor ontwikkeling wordt geserveerd, dus APP_ENV draagt waarschijnlijk nog de waarde uit .env.example',
    ],

    'app-debug' => [
        'label' => 'Debugmodus',
        'pass' => 'De debugmodus staat uit.',
        'fail' => 'De debugmodus staat aan in productie.',
        'finding' => 'een foutpagina toont de stack trace en de omgeving, inclusief de inhoud van .env, aan iedereen die de fout veroorzaakt',
    ],

    'app-key' => [
        'label' => 'Applicatiesleutel',
        'pass' => 'De applicatiesleutel is gezet, en is geen plaatshouder en niet die van het voorbeeldbestand.',
        'fail' => 'De applicatiesleutel ontbreekt of is bekend bij anderen, dus sessies, cookies en versleutelde gegevens kunnen worden vervalst.',
        'missing' => 'er is geen sleutel gezet',
        'placeholder' => 'de sleutel is een plaatshouder die een oude Laravel-versie in het voorbeeldbestand meeleverde',
        'committed' => 'de sleutel is die uit het ingecheckte voorbeeldbestand',
    ],

    'mail-transport' => [
        'label' => 'Mailbezorging',
        'pass' => 'De standaardmailer verstuurt mail van de server af.',
        'fail' => 'De standaardmailer bezorgt geen enkele mail: registraties, wachtwoordresets en facturen verdwijnen geruisloos.',
        'warn' => 'De standaardmailer verstuurt naar deze machine zelf. Een relay daar moet de mail doorsturen.',
        'unconfigured' => 'de mailer is niet geconfigureerd, dus versturen geeft een fout',
        'log' => 'het transport log schrijft elk bericht in het log en verstuurt er geen',
        'array' => 'het transport array houdt elk bericht in het geheugen en verstuurt er geen',
        'catcher' => 'de SMTP-host is een mailcatcher, waar berichten stoppen en geen postvak ze ontvangt',
        'local' => 'de SMTP-host is de machine zelf: een geldige relay, of een ontwikkelinstelling die niemand heeft aangepast',
    ],

    'health-notifications' => [
        'label' => 'Een tweede weg voor meldingen',
        'pass' => 'Een mislukte controle bereikt je via een kanaal naast mail.',
        'pulled' => 'Oh Dear haalt de resultaten op, los van de mail van de applicatie.',
        'pulled_own' => 'Een monitor haalt de resultaten op bij :endpoint, los van de mail van de applicatie.',
        'warn' => 'Een mislukte controle in bedrijf zou je niet bereiken als de mail uitvalt.',
        'off' => 'meldingen staan uit, en niets haalt de resultaten op',
        'mail_only' => 'een mislukte controle wordt alleen per mail bezorgd, en als de mail uitvalt, is juist het bericht daarover het bericht dat niet aankomt. Voeg een Slack-webhook of het Oh Dear-endpoint toe, of noem in production-preflight.health_endpoint een eigen endpoint dat een monitor ophaalt.',
        'none' => 'geen kanaal bezorgt een mislukte controle',
    ],

    'test-mode-keys' => [
        'label' => 'Inloggegevens in testmodus',
        'pass' => 'Geen inloggegevens van de configuratie staan in testmodus.',
        'staging' => 'Niets te controleren: een stagingserver neemt betalingen aan met testsleutels, en een live sleutel zou daar echt geld bewegen.',
        'fail' => 'Instellingen in testmodus: :count. Er wordt geen echt geld verplaatst.',
        'stripe' => 'een Stripe-sleutel in testmodus',
        'mollie' => 'een Mollie-sleutel in testmodus',
        'sandbox' => 'de sandboxmodus staat aan',
    ],

    'queue-connection' => [
        'label' => 'Queueverbinding',
        'pass' => 'De standaard-queueverbinding geeft jobs aan workers.',
        'fail' => 'De standaard-queueverbinding voert geen enkele job uit.',
        'warn' => 'De standaard-queueverbinding voert elke job binnen het request uit.',
        'unconfigured' => 'de standaardverbinding is niet geconfigureerd, dus een job in de queue zetten geeft een fout',
        'null' => 'de driver null gooit elke job weg die hij krijgt',
        'sync' => 'jobs draaien binnen het request dat ze in de queue zet, dus elk antwoord wacht erop. Een keuze voor een kleine installatie: accepteer hem als het de jouwe is.',
    ],

    'session-driver' => [
        'label' => 'Sessiedriver',
        'pass' => 'Sessies overleven het request.',
        'fail' => 'Sessies duren één request.',
        'array' => 'de driver array vergeet elke sessie na het request, dus elke gebruiker is na elke pagina uitgelogd',
    ],

    'cache-store' => [
        'label' => 'Cachestore',
        'pass' => 'De standaard-cachestore bewaart wat hij krijgt.',
        'fail' => 'De standaard-cachestore bewaart niets na het request.',
        'unconfigured' => 'de standaardstore is niet geconfigureerd, dus de eerste cachetoegang geeft een fout',
        'transient' => 'de rate limiters, de atomaire locks en het herstartsignaal van de queueworkers staan in de cache en zijn na elk request weg',
    ],

    'secure-cookie' => [
        'label' => 'Beveiligde sessiecookie',
        'pass' => 'De sessiecookie heeft de Secure-vlag.',
        'fail' => 'De sessiecookie mist de Secure-vlag op een site die via HTTPS draait.',
        'finding' => 'een browser stuurt de cookie ook over onversleuteld HTTP, waar iedereen op het netwerk hem kan lezen. Zet SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url noemt http, waar een Secure-cookie helemaal niet zou worden verstuurd.',
        'no_scheme' => 'Niet gecontroleerd: app.url noemt geen schema, dus het is onbekend of de site via HTTPS draait.',
    ],

    'debug-tools' => [
        'label' => 'Debugtools',
        'pass' => 'Er is geen debugtool geladen.',
        'fail' => 'Een debugtool staat open in productie.',
        'warn' => 'Een debugtool is geladen en slaapt.',
        'telescope' => 'Telescope legt requests, queries en mail vast, en het dashboard staat één gate van het publiek af',
        'debugbar' => 'de debugbalk toont elke bezoeker de queries, de sessie en het request van elke pagina',
        'ignition' => 'slaapt zolang de debugmodus uit staat, en is een open blik in de applicatie zodra die aan staat',
    ],

    'log-level' => [
        'label' => 'Logniveau',
        'pass' => 'Het standaard-logkanaal logt niet op debugniveau.',
        'warn' => 'Het standaard-logkanaal logt op debugniveau.',
        'unconfigured' => 'Niet gecontroleerd: het standaard-logkanaal is niet geconfigureerd.',
        'debug' => 'elk detail belandt op de schijf, die in een berekenbaar tempo volloopt',
    ],

    'config-cache-fresh' => [
        'label' => 'Gecachte configuratie',
        'pass' => 'De gecachte configuratie is nieuwer dan .env.',
        'fail' => 'De applicatie draait op een configuratie van voor de laatste wijziging aan .env.',
        'finding' => '.env is gewijzigd nadat de configuratie is gecachet, dus de nieuwe waarden gelden niet. Voer php artisan config:cache uit.',
        'not_cached' => 'De configuratie is niet gecachet, dus elk request leest .env en een wijziging geldt meteen.',
        'no_env' => 'De gecachte configuratie is de hele configuratie, en er staat geen .env naast.',
    ],

    'queue-restart' => [
        'label' => 'Queueworkers na de deploy',
        'pass' => 'De queueworkers zijn na de laatste deploy gevraagd te herstarten.',
        'fail' => 'De queueworkers draaien de code van voor de laatste deploy.',
        'never' => 'er is nooit een herstartsignaal verstuurd, dus de workers draaien de code waarmee ze zijn gestart. Zet php artisan queue:restart aan het einde van het deployscript.',
        'stale' => 'het laatste herstartsignaal is ouder dan de laatste deploy. Zet php artisan queue:restart aan het einde van het deployscript.',
        'no_worker' => 'De standaard-queueverbinding heeft geen worker om te herstarten.',
        'no_deploy' => 'Niet gecontroleerd: er is geen deploy vastgelegd. Zet php artisan preflight --deploy in het deployscript, vóór php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Openstaande migraties',
        'pass' => 'Elke migratie is uitgevoerd.',
        'fail' => 'Migraties die niet zijn uitgevoerd: :count. De code is nieuw, het schema niet.',
        'pending' => 'is niet uitgevoerd',
        'no_migrator' => 'Niet gecontroleerd: de applicatie heeft geen migrator.',
    ],

    'writable-directories' => [
        'label' => 'Schrijfbare mappen',
        'pass' => 'Elke map waarin Laravel schrijft is schrijfbaar.',
        'fail' => 'Paden waarin Laravel op :server niet kan schrijven: :count. De eerste gebruikersactie die schrijft, mislukt.',
        'missing' => 'de map ontbreekt',
        'read_only' => 'de map is niet schrijfbaar voor de gebruiker die deze controle uitvoert',
        'log_read_only' => 'het nieuwste logbestand is niet schrijfbaar voor de gebruiker die deze controle uitvoert, en een regel die hij logt gaat verloren',
    ],

    'storage-link' => [
        'label' => 'Storagelink',
        'pass' => 'Elke geconfigureerde storagelink wijst waar hij moet.',
        'fail' => 'Bestanden op de publieke schijf antwoorden met 404.',
        'warn' => 'Het volgende bestand op de publieke schijf zal met 404 antwoorden.',
        'elsewhere' => 'de link wijst ergens anders heen',
        'directory' => 'waar de link hoort staat een echte map, en een later opgeslagen bestand verschijnt er nooit in',
        'missing_with_files' => 'de link ontbreekt, en het doel bevat al bestanden. Voer php artisan storage:link uit.',
        'missing' => 'de link ontbreekt. Voer php artisan storage:link uit.',
    ],

    'env-exposure' => [
        'label' => 'Geheimen in het bestandssysteem',
        'pass' => '.env staat niet open voor elke gebruiker van de server, en de webroot bevat geen .env en geen .git.',
        'fail' => 'Bestanden die geheimen prijsgeven: :count.',
        'world_readable' => 'elke gebruiker van de server kan het lezen',
        'world_writable' => 'elke gebruiker van de server kan het schrijven',
        'web_root' => 'het staat in de webroot, waar de webserver het kan uitleveren',
    ],

    'env-over-http' => [
        'label' => 'Geheimen via HTTP',
        'pass' => 'De site levert geen .env en geen .git/config uit.',
        'fail' => 'De site levert een geheim bestand uit aan iedereen die erom vraagt.',
        'served' => 'de site antwoordt met 200 en de inhoud van het bestand',
        'off' => 'Niet gecontroleerd: of de site .env uitlevert, vraagt een request aan de site, en de probe staat uit. Zet hem aan met PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Niet gecontroleerd: app.url is geen http- of https-adres om op te vragen.',
        'unanswered' => 'Niet gecontroleerd: de site gaf geen antwoord.',
    ],

    'dev-dependencies' => [
        'label' => 'Ontwikkelafhankelijkheden',
        'pass' => 'Er is geen ontwikkelpakket geïnstalleerd.',
        'fail' => 'Geïnstalleerde ontwikkelpakketten: :count.',
        'finding' => 'testrunners, debuggers en codegeneratoren zijn geïnstalleerd. Installeer met composer install --no-dev.',
        'unreadable' => 'Niet gecontroleerd: :path is niet leesbaar.',
    ],

    'optimized-autoloader' => [
        'label' => 'Geoptimaliseerde autoloader',
        'pass' => 'De autoloader is geoptimaliseerd.',
        'warn' => 'De autoloader zoekt elke klasse op de schijf op.',
        'finding' => 'installeer met composer install --optimize-autoloader',
        'unreadable' => 'Niet gecontroleerd: composer.json of de class map van de autoloader is niet leesbaar.',
    ],

    'php-version-parity' => [
        'label' => 'PHP-versie van de webserver',
        'pass' => 'De opdrachtregel en de webserver draaien PHP :cli.',
        'fail' => 'De opdrachtregel draait PHP :cli en de webserver PHP :web.',
        'finding' => 'migraties en geplande opdrachten draaien onder PHP :cli, de applicatie onder PHP :web',
        'unnamed' => 'Niet gecontroleerd: de PHP van de webserver is een binary die deze controle uitvoert, en er is er geen genoemd. Zet PRODUCTION_PREFLIGHT_PHP_FPM, bijvoorbeeld op /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Niet gecontroleerd: deze host staat niet toe dat een proces wordt gestart.',
        'unanswered' => 'Niet gecontroleerd: de genoemde binary meldde geen PHP-versie.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache staat aan en controleert bestanden niet op wijzigingen.',
        'fail' => 'OPcache draait niet in de webserver.',
        'warn' => 'OPcache controleert elk bestand op wijzigingen.',
        'not_loaded' => 'de OPcache-extensie is niet geladen',
        'off' => 'OPcache staat uit',
        'timestamps' => 'elk request controleert de bestanden op wijzigingen. Zet het uit en laad PHP bij elke deploy opnieuw.',
        'command_line' => 'Niet gecontroleerd: de opdrachtregel heeft eigen OPcache-instellingen. De controle antwoordt in een webrequest, zoals het endpoint van laravel-health met verse resultaten.',
    ],

];
