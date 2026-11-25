<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Bestanden',
        'warn' => 'Warnung',
        'fail' => 'Fehler',
        'undetermined' => 'Nicht geprüft',
    ],

    'acknowledged' => 'Quittiert: :reason Gefunden: :summary',
    'unreadable' => 'Nicht geprüft: :path ist nicht lesbar.',
    'empty' => '(leer)',
    'line' => ':key (Zeile :line)',

    'stage' => [
        'undetermined' => 'Nicht geprüft: Die Umgebung, die die Anwendung angibt, kennt dieses Paket nicht. Trage sie unter production-preflight.stages ein.',
        'development' => 'Nichts zu prüfen: Die Produktionsregeln gelten in Produktion und auf Staging, und die Anwendung gibt eine Entwicklungsumgebung an.',
    ],

    'command' => [
        'crashed' => 'Nicht geprüft: Die Prüfung brach mit :exception ab.',
        'evidence' => 'Wert: :evidence',
        'file' => 'Datei: :path',
        'unreadable' => 'Nicht lesbar: :path',
        'unreadable_note' => 'Das ist nicht dasselbe wie sauber: Geprüft wurde nichts.',
        'unrecorded' => 'Der Deploy ließ sich nicht in :path festhalten, deshalb hat die Prüfung queue-restart nichts zum Vergleichen.',
        'values_in_deploy' => 'In einem Deploy werden keine Werte ausgegeben: Seine Ausgabe bleibt erhalten. Ohne --deploy aufrufen, um sie zu sehen.',
        'exit' => [
            '0' => 'Keine Prüfung ist fehlgeschlagen.',
            '1' => 'Mindestens eine Prüfung ist fehlgeschlagen.',
            '2' => 'Keine Prüfung ist fehlgeschlagen, und mindestens eine konnte nicht nachsehen. Das ist kein Bestehen.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Doppelt gesetzte Schlüssel',
        'pass' => 'Kein Schlüssel ist doppelt gesetzt.',
        'fail' => 'Mehrfach gesetzte Schlüssel: :count, mindestens einer mit VERSCHIEDENEN Werten. Es gilt der ZULETZT gesetzte Wert, also steht eine Einstellung in der Datei und gilt nicht. Die überzähligen Zeilen entfernen, statt eine weitere anzuhängen.',
        'warn' => 'Mehrfach gesetzte Schlüssel: :count, jeweils mit gleichen Werten. Es gilt der ZULETZT gesetzte Wert. Die überzähligen Zeilen entfernen, statt eine weitere anzuhängen.',
        'finding' => 'gesetzt in den Zeilen :lines, :values',
        'different' => 'VERSCHIEDENE Werte',
        'equal' => 'gleiche Werte',
        'evidence' => ':lines, es gilt: :value',
        'line' => 'Zeile :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Schlüssel der Beispieldatei',
        'pass' => 'Die .env und ihre Beispieldatei nennen dieselben Schlüssel.',
        'pass_optional' => 'Die .env nennt jeden Schlüssel ihrer Beispieldatei, bis auf :optional, die die Anwendung als optional erklärt.',
        'fail' => 'Schlüssel der Beispieldatei, die in .env fehlen: :missing. Jeder liefert null, und sein Feature tut ohne ein Wort nichts. Schlüssel der .env, die in der Beispieldatei fehlen: :undocumented.',
        'warn' => 'Schlüssel der .env, die in der Beispieldatei fehlen: :undocumented. Die nächste Installation entsteht aus dem Beispiel und wird sie nicht haben.',
        'missing' => 'in der Beispieldatei genannt, fehlt in .env',
        'undocumented' => 'in .env gesetzt, fehlt in der Beispieldatei',
    ],

    'placeholder-values' => [
        'label' => 'Platzhalter-Werte',
        'pass' => 'Kein Wert der .env ist ein Platzhalter.',
        'fail' => 'Werte, die nie ersetzt wurden: :count.',
        'placeholder' => 'der Wert ist ein Platzhalter',
        'from_example' => 'der Wert ist der aus der eingecheckten Beispieldatei, also kennt ihn jeder, der das Repository liest',
        'required' => 'erforderlich, und fehlt oder ist leer',
    ],

    'unquoted-hash' => [
        'label' => 'Unquotiertes #',
        'pass' => 'Kein unquotierter Wert enthält ein #, das ihn abschneidet.',
        'fail' => 'An einem unquotierten # abgeschnittene Werte: :count. Die Anwendung liest nur den Teil davor.',
        'warn' => 'Werte, die wie an einem unquotierten # abgeschnittener Text aussehen: :count.',
        'cut' => 'ein unquotiertes # im Wert beginnt einen Kommentar, also liest die Anwendung nur den Teil davor. Den Wert in Anführungszeichen setzen.',
        'ambiguous' => 'das # nach dem Leerzeichen beginnt einen Kommentar, also liest die Anwendung nur den Teil davor. Den Wert in Anführungszeichen setzen, oder nach dem # ein Leerzeichen schreiben, wenn es ein Kommentar ist.',
        'evidence' => 'die Anwendung liest :value, in der Datei steht :raw',
    ],

    'env-bytes' => [
        'label' => 'Unsichtbare Bytes',
        'pass' => 'Keine Byte-Order-Mark, kein Wagenrücklauf, kein Leerraum nach einem Wert.',
        'warn' => 'Laravel liest diese Bytes richtig. Eine Shell oder ein Werkzeug, das die Datei als Text liest, tut es nicht und sieht dann andere Werte als die, die dastehen.',
        'mark' => 'Byte-Order-Mark',
        'mark_statement' => 'die Datei beginnt mit einer, und eine Shell, die die Datei einliest, hält sie für einen Teil des ersten Namens',
        'endings' => 'Zeilenenden',
        'endings_statement' => '{1} eine Zeile endet mit einem Wagenrücklauf, den eine Shell am Ende ihres Werts behält|[2,*] :count Zeilen enden mit einem Wagenrücklauf, den eine Shell am Ende jedes Werts behält',
        'whitespace' => 'der Wert endet mit Leerraum, den ein Werkzeug behält, das die Zeile wörtlich nimmt, etwa Dockers --env-file',
    ],

    'development-stage-server' => [
        'label' => 'Entwicklungsumgebung auf einem Server',
        'pass' => 'app.url nennt einen Host, unter dem ein Rechner zum Entwickeln ausgeliefert wird.',
        'not_development' => 'Nichts zu prüfen: Nur eine Entwicklungsumgebung wird gegen ihren Host gelesen, und die Anwendung gibt die Umgebung :stage an.',
        'no_host' => 'app.url nennt keinen Host, deshalb lässt sich nicht beurteilen, ob ein Server eine Entwicklungsumgebung angibt.',
        'fail' => 'APP_ENV nennt eine Entwicklungsumgebung und app.url einen Host, unter dem kein Rechner zum Entwickeln ausgeliefert wird: ein Server, der eine Entwicklungsumgebung angibt und auf dem jeder Produktions-Check besteht.',
        'finding' => 'nennt keinen Host, unter dem ein Rechner zum Entwickeln ausgeliefert wird, APP_ENV trägt also wohl noch den Wert aus .env.example',
    ],

    'app-debug' => [
        'label' => 'Debug-Modus',
        'pass' => 'Der Debug-Modus ist aus.',
        'fail' => 'Der Debug-Modus ist in Produktion an.',
        'finding' => 'eine Fehlerseite zeigt den Stacktrace und die Umgebung, den Inhalt der .env eingeschlossen, jedem, der den Fehler auslöst',
    ],

    'app-key' => [
        'label' => 'Anwendungsschlüssel',
        'pass' => 'Der Anwendungsschlüssel ist gesetzt, und weder er noch ein früherer Schlüssel ist ein Platzhalter oder ein Schlüssel aus der Beispieldatei.',
        'fail' => 'Der Anwendungsschlüssel fehlt, oder ein Schlüssel, den die Anwendung annimmt, ist anderen bekannt, also lassen sich Sessions, Cookies, verschlüsselte Daten und signierte URLs fälschen.',
        'missing' => 'kein Schlüssel ist gesetzt',
        'placeholder' => 'der Schlüssel ist ein Platzhalter, den eine alte Laravel-Version in ihrer Beispieldatei auslieferte',
        'committed' => 'der Schlüssel ist der aus der eingecheckten Beispieldatei',
        'previous_placeholder' => 'der frühere Schlüssel an Stelle :position ist ein Platzhalter, den eine alte Laravel-Version in ihrer Beispieldatei auslieferte, und die Anwendung nimmt Cookies, verschlüsselte Daten und signierte URLs damit weiter an',
        'previous_committed' => 'der frühere Schlüssel an Stelle :position ist einer aus der eingecheckten Beispieldatei, und die Anwendung nimmt Cookies, verschlüsselte Daten und signierte URLs damit weiter an',
    ],

    'mail-transport' => [
        'label' => 'Mailversand',
        'pass' => 'Der Standard-Mailer verschickt Mails vom Server.',
        'fail' => 'Der Standard-Mailer stellt keine Mail zu: Registrierungen, Passwort-Resets und Rechnungen verschwinden lautlos.',
        'warn' => 'Der Standard-Mailer sendet an diesen Rechner. Ein Relay dort muss die Mails weitergeben.',
        'unconfigured' => 'der Mailer ist nicht konfiguriert, also wirft das Senden einen Fehler',
        'url' => 'die URL des Mailers nennt keinen Transport, den Laravel lesen kann, also wirft das Senden einen Fehler',
        'log' => 'der Transport log schreibt jede Nachricht ins Log und versendet keine',
        'array' => 'der Transport array hält jede Nachricht im Speicher und versendet keine',
        'catcher' => 'der SMTP-Host ist ein Mail-Catcher, bei dem die Nachrichten enden und kein Postfach sie erhält',
        'local' => 'der SMTP-Host ist der Rechner selbst: ein gültiges Relay, oder eine Entwicklungseinstellung, die niemand geändert hat',
    ],

    'health-notifications' => [
        'label' => 'Ein zweiter Weg für Alarme',
        'pass' => 'Eine fehlschlagende Prüfung erreicht dich über einen Kanal neben der Mail.',
        'pulled' => 'Oh Dear holt die Ergebnisse ab, unabhängig von der Mail der Anwendung.',
        'pulled_own' => 'Ein Monitor holt die Ergebnisse unter dem Endpunkt ab, den production-preflight.health_endpoint nennt, unabhängig von der Mail der Anwendung.',
        'warn' => 'Eine fehlschlagende Prüfung im Betrieb würde dich nicht erreichen, wenn die Mail ausfällt.',
        'off' => 'Benachrichtigungen sind ausgeschaltet, und nichts holt die Ergebnisse ab',
        'mail_only' => 'eine fehlschlagende Prüfung wird nur per Mail zugestellt, und wenn die Mail ausfällt, kommt genau die Nachricht darüber nicht an. Einen Slack-Webhook oder den Oh-Dear-Endpunkt hinzufügen, oder einen eigenen Endpunkt, den ein Monitor abholt, in production-preflight.health_endpoint nennen.',
        'none' => 'kein Kanal stellt eine fehlschlagende Prüfung zu',
        'unserved' => 'Oh Dear ist eingeschaltet, und laravel-health stellt ihm keinen Endpunkt bereit, unter dem es die Ergebnisse abholen kann.',
        'unserved_secret' => 'laravel-health stellt den Oh-Dear-Endpunkt nur mit einem Secret bereit, und keins ist gesetzt, also findet Oh Dear nichts zum Abholen. OH_DEAR_HEALTH_CHECK_SECRET auf das Secret setzen, das Oh Dear in seinen Application-Health-Einstellungen zeigt.',
        'unserved_url' => 'laravel-health stellt den Oh-Dear-Endpunkt nur unter einer URL bereit, und keine ist gesetzt, also findet Oh Dear nichts zum Abholen. Sie auf den Pfad setzen, den Oh Dear abfragt, standardmäßig /oh-dear-health-check-results.',
    ],

    'test-mode-keys' => [
        'label' => 'Zugangsdaten im Testmodus',
        'pass' => 'Keine Zugangsdaten der Konfiguration sind im Testmodus.',
        'staging' => 'Nichts zu prüfen: Ein Staging-Server nimmt seine Zahlungen mit Testschlüsseln, und ein Live-Schlüssel bewegte dort echtes Geld.',
        'fail' => 'Einstellungen im Testmodus: :count. Es bewegt sich kein echtes Geld.',
        'stripe' => 'ein Stripe-Schlüssel im Testmodus',
        'mollie' => 'ein Mollie-Schlüssel im Testmodus',
        'sandbox' => 'der Sandbox-Modus ist an',
    ],

    'queue-connection' => [
        'label' => 'Queue-Verbindung',
        'pass' => 'Die Standard-Queue-Verbindung übergibt Jobs an Worker.',
        'fail' => 'Die Standard-Queue-Verbindung führt keinen Job aus.',
        'warn' => 'Die Standard-Queue-Verbindung führt jeden Job innerhalb des Requests aus.',
        'unconfigured' => 'die Standard-Verbindung ist nicht konfiguriert, also wirft das Einreihen eines Jobs einen Fehler',
        'null' => 'der Treiber null verwirft jeden Job, den er bekommt',
        'sync' => 'Jobs laufen innerhalb des Requests, der sie einreiht, also wartet jede Antwort auf sie. Eine Wahl für eine kleine Installation: quittieren, wenn es deine ist.',
        'unattended' => 'Die Standard-Queue-Verbindung führt jeden Job ohne Worker aus.',
        'deferred' => 'der Treiber deferred führt jeden Job in dem PHP-Prozess aus, der ihn eingereiht hat, nachdem die Antwort gesendet ist: ein fehlgeschlagener Job wird nicht wiederholt, und ein Job, der nicht fertig ist, geht verloren, wenn dieser Prozess endet. Eine Wahl für eine kleine Installation: quittieren, wenn es deine ist.',
        'background' => 'der Treiber background führt jeden Job in einem PHP-Prozess aus, der für ihn gestartet wird: ein fehlgeschlagener Job wird nicht wiederholt, und ein Job, der nicht fertig ist, geht verloren, wenn dieser Prozess endet. Eine Wahl für eine kleine Installation: quittieren, wenn es deine ist.',
    ],

    'session-driver' => [
        'label' => 'Session-Treiber',
        'pass' => 'Sessions überdauern den Request.',
        'fail' => 'Sessions überdauern den Request nicht.',
        'unconfigured' => 'der Session-Treiber ist nicht konfiguriert, also wirft der erste Request, der eine Session startet, einen Fehler',
        'array' => 'der Treiber array vergisst jede Session nach dem Request, also ist jeder Nutzer nach jeder Seite abgemeldet',
    ],

    'cache-store' => [
        'label' => 'Cache-Store',
        'pass' => 'Der Standard-Cache-Store behält, was er bekommt.',
        'fail' => 'Der Standard-Cache-Store behält nichts über den Request hinaus.',
        'unconfigured' => 'der Standard-Store ist nicht konfiguriert, also wirft der erste Cache-Zugriff einen Fehler',
        'transient' => 'die Rate-Limiter, die atomaren Locks und das Restart-Signal der Queue-Worker liegen im Cache und sind nach jedem Request weg',
        'fail_session' => 'Der Standard-Cache-Store hält den Cache in der Session jedes Besuchers.',
        'session' => 'ein Throttle zählt je Besucher und beginnt für einen Client ohne Cookie von vorn, Cache::lock() und ein Unique-Job enden in einem Fehler, weil der Session-Store keine Locks kennt, und ein Befehl, auch queue:restart, schreibt in eine Session, die niemand speichert, sodass kein Worker das Restart-Signal hört',
        'warn' => 'Der Standard-Cache-Store fällt auf einen zurück, der nichts über den Request hinaus behält oder den Cache je Besucher hält.',
        'falls_back' => 'sobald die Stores davor ausfallen, bedient der Failover-Store aus einem, der nichts über den Request hinaus behält oder den Cache in der Session jedes Besuchers hält, und die Rate-Limiter, die atomaren Locks und das Restart-Signal der Queue-Worker gehen verloren, solange er das tut',
    ],

    'secure-cookie' => [
        'label' => 'Sicheres Session-Cookie',
        'pass' => 'Das Session-Cookie trägt das Secure-Flag.',
        'fail' => 'Dem Session-Cookie fehlt das Secure-Flag auf einer Seite, die über HTTPS läuft.',
        'finding' => 'ein Browser schickt das Cookie auch über unverschlüsseltes HTTP, wo jeder im Netz es lesen kann. SESSION_SECURE_COOKIE=true setzen.',
        'http' => 'app.url nennt http, wo ein Secure-Cookie gar nicht gesendet würde.',
        'no_scheme' => 'Nicht geprüft: app.url nennt kein Schema, also ist unbekannt, ob die Seite über HTTPS läuft.',
    ],

    'debug-tools' => [
        'label' => 'Debugging-Werkzeuge',
        'pass' => 'Kein Debugging-Werkzeug ist geladen.',
        'fail' => 'Ein Debugging-Werkzeug ist in Produktion offen.',
        'warn' => 'Ein Debugging-Werkzeug ist geladen und ruht.',
        'telescope' => 'Telescope zeichnet Requests, Abfragen und Mails auf, und sein Dashboard steht ein Gate von der Öffentlichkeit entfernt',
        'debugbar' => 'die Debugbar zeigt jedem Besucher die Abfragen, die Session und den Request jeder Seite',
        'ignition' => 'ruht, solange der Debug-Modus aus ist, und ist in dem Moment, in dem er an ist, ein offener Blick in die Anwendung',
    ],

    'log-level' => [
        'label' => 'Log-Level',
        'pass' => 'Der Standard-Log-Kanal loggt nicht auf Debug-Level.',
        'warn' => 'Der Standard-Log-Kanal loggt auf Debug-Level.',
        'unconfigured' => 'Nicht geprüft: Der Standard-Log-Kanal ist nicht konfiguriert.',
        'debug' => 'jedes Detail landet auf der Platte, und sie läuft in berechenbarem Tempo voll',
    ],

    'config-cache-fresh' => [
        'label' => 'Gecachte Konfiguration',
        'pass' => 'Die gecachte Konfiguration ist neuer als .env.',
        'within_grace' => '.env hat sich geändert, nachdem die Konfiguration gecacht wurde, vor weniger als :minutes Minuten. So lange hat ein Deploy Zeit, sie neu zu cachen.',
        'fail' => 'Die Anwendung läuft auf einer Konfiguration von vor der letzten Änderung an .env.',
        'finding' => '.env hat sich geändert, nachdem die Konfiguration gecacht wurde, also gelten ihre neuen Werte nicht. php artisan config:cache ausführen.',
        'not_cached' => 'Die Konfiguration ist nicht gecacht, also liest jeder Request .env, und eine Änderung gilt sofort.',
        'no_env' => 'Die gecachte Konfiguration ist die ganze Konfiguration, und keine .env steht daneben.',
    ],

    'queue-restart' => [
        'label' => 'Queue-Worker nach dem Deploy',
        'pass' => 'Die Queue-Worker wurden nach dem letzten Deploy zum Neustart aufgefordert.',
        'fail' => 'Die Queue-Worker laufen mit dem Code von vor dem letzten Deploy.',
        'fail_missing' => 'Die Queue-Worker laufen womöglich mit dem Code von vor dem letzten Deploy: Im Cache-Store liegt kein Restart-Signal.',
        'missing' => 'entweder lief queue:restart nach dem Deploy nicht, oder der Cache hat das Signal seitdem verloren, geleert von cache:clear oder optimize:clear, ohne Persistenz neu gestartet oder verdrängt, und die Worker sind neu gestartet, als es verschwand. php artisan reload oder php artisan queue:restart ans Ende des Deploy-Skripts setzen, nach php artisan preflight --deploy und nach jedem cache:clear oder optimize:clear.',
        'stale' => 'das letzte Restart-Signal ist älter als der letzte Deploy. php artisan reload oder php artisan queue:restart ans Ende des Deploy-Skripts setzen, nach php artisan preflight --deploy.',
        'no_worker' => 'Die Standard-Queue-Verbindung hat keinen Worker, der neu starten müsste.',
        'no_deploy' => 'Nicht geprüft: Kein Deploy ist festgehalten. php artisan preflight --deploy ins Deploy-Skript setzen, vor php artisan reload oder php artisan queue:restart.',
        'within_grace' => 'Der letzte Deploy wurde vor weniger als :minutes Minuten notiert. So lange hat ein Deploy Zeit, die Queue-Worker an seinem Ende neu zu starten.',
    ],

    'pending-migrations' => [
        'label' => 'Ausstehende Migrationen',
        'pass' => 'Jede Migration ist gelaufen.',
        'pass_skipped' => 'Jede Migration ist gelaufen, bis auf :migrations, die die Anwendung überspringt.',
        'fail' => 'Migrationen, die nicht gelaufen sind: :count. Der Code ist neu, das Schema nicht.',
        'pending' => 'ist nicht gelaufen',
        'no_migrator' => 'Nicht geprüft: Die Anwendung hat keinen Migrator.',
    ],

    'writable-directories' => [
        'label' => 'Beschreibbare Verzeichnisse',
        'pass' => 'Jedes Verzeichnis, in das Laravel schreibt, ist beschreibbar.',
        'fail' => 'Pfade, in die Laravel auf :server nicht schreiben kann, gefragt als :user: :count. Die erste Nutzeraktion, die schreibt, fällt um.',
        'fail_unknown_user' => 'Pfade, in die Laravel auf :server nicht schreiben kann: :count. Der Nutzer, als der diese Prüfung läuft, ließ sich nicht benennen. Die erste Nutzeraktion, die schreibt, fällt um.',
        'missing' => 'das Verzeichnis fehlt',
        'not_creatable' => 'das Verzeichnis fehlt, und das Verzeichnis darüber ist für den Nutzer, der diese Prüfung ausführt, nicht beschreibbar, also kann Laravel es nicht anlegen',
        'read_only' => 'das Verzeichnis ist für den Nutzer, der diese Prüfung ausführt, nicht beschreibbar',
        'log_read_only' => 'die neueste Log-Datei ist für den Nutzer, der diese Prüfung ausführt, nicht beschreibbar, und eine Zeile, die er schreibt, geht verloren',
        'log_directory_read_only' => 'das Verzeichnis der neuesten Log-Datei ist für den Nutzer, der diese Prüfung ausführt, nicht beschreibbar, und die Datei des nächsten Tages kann nicht entstehen',
    ],

    'storage-link' => [
        'label' => 'Storage-Link',
        'pass' => 'Jeder konfigurierte Storage-Link zeigt, wohin er soll.',
        'fail' => 'Dateien auf der Public-Disk antworten mit 404.',
        'warn' => 'Die nächste Datei auf der Public-Disk wird mit 404 antworten.',
        'elsewhere' => 'der Link zeigt woandershin',
        'directory' => 'wo der Link hingehört, steht ein echtes Verzeichnis, und eine später gespeicherte Datei erscheint darin nie',
        'missing_with_files' => 'der Link fehlt, und sein Ziel enthält schon Dateien. php artisan storage:link ausführen.',
        'missing' => 'der Link fehlt. php artisan storage:link ausführen.',
    ],

    'env-exposure' => [
        'label' => 'Geheimnisse im Dateisystem',
        'pass' => '.env ist nicht für jeden Nutzer des Servers offen, und das Web-Root enthält weder .env noch .git.',
        'fail' => 'Dateien, die Geheimnisse herausgeben: :count.',
        'world_readable' => 'jeder Nutzer des Servers kann sie lesen',
        'world_writable' => 'jeder Nutzer des Servers kann sie schreiben',
        'web_root' => 'sie liegt im Web-Root, wo der Webserver sie ausliefern kann',
    ],

    'env-over-http' => [
        'label' => 'Geheimnisse über HTTP',
        'pass' => 'Die Seite liefert weder .env noch .git/config aus.',
        'fail' => 'Die Seite liefert jedem, der fragt, eine geheime Datei aus.',
        'served' => 'die Seite antwortet mit 200 und dem Inhalt der Datei',
        'off' => 'Nicht geprüft: Ob die Seite .env ausliefert, braucht eine Anfrage an die Seite, und die Probe ist aus. Mit PRODUCTION_PREFLIGHT_HTTP_PROBE=true einschalten.',
        'no_url' => 'Nicht geprüft: app.url ist keine http- oder https-Adresse, die sich abfragen ließe.',
        'unanswered' => 'Nicht geprüft: Die Seite hat nicht geantwortet.',
        'redirected' => 'Nicht geprüft: Die Seite leitet die Anfrage an eine andere Adresse um, und die Probe folgt keiner Umleitung. app.url auf die Adresse setzen, auf die sie umleitet.',
        'moved' => 'umgeleitet nach :target',
    ],

    'dev-dependencies' => [
        'label' => 'Entwicklungsabhängigkeiten',
        'pass' => 'Kein Entwicklungspaket ist installiert.',
        'fail' => 'Installierte Entwicklungspakete: :count.',
        'finding' => 'Test-Runner, Debugger und Codegeneratoren sind installiert. Mit composer install --no-dev installieren.',
        'unreadable' => 'Nicht geprüft: :path ist nicht lesbar.',
    ],

    'optimized-autoloader' => [
        'label' => 'Optimierter Autoloader',
        'pass' => 'Der Autoloader ist optimiert.',
        'warn' => 'Der Autoloader sucht jede Klasse auf der Platte.',
        'finding' => 'mit composer install --optimize-autoloader installieren',
        'unreadable' => 'Nicht geprüft: composer.json oder die Class-Map des Autoloaders ist nicht lesbar.',
    ],

    'php-version-parity' => [
        'label' => 'PHP-Version des Webservers',
        'pass' => 'Die Kommandozeile und der Webserver laufen mit PHP :cli.',
        'fail' => 'Die Kommandozeile läuft mit PHP :cli und der Webserver mit PHP :web.',
        'finding' => 'Migrationen und geplante Befehle laufen unter PHP :cli, die Anwendung unter PHP :web',
        'unnamed' => 'Nicht geprüft: Das PHP des Webservers ist ein Binary, das diese Prüfung ausführt, und keins ist genannt. PRODUCTION_PREFLIGHT_PHP_FPM setzen, zum Beispiel auf /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Nicht geprüft: Dieser Host erlaubt nicht, einen Prozess zu starten.',
        'unanswered' => 'Nicht geprüft: Das genannte Binary hat keine PHP-Version gemeldet.',
        'web_request' => 'Nicht geprüft: Ein Web-Request läuft mit dem PHP des Webservers, das die Prüfung mit sich selbst vergleichen würde. Die Prüfung antwortet auf der Kommandozeile, etwa im geplanten Lauf von laravel-health.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache ist an und prüft Dateien nicht auf Änderungen.',
        'fail' => 'OPcache läuft im Webserver nicht.',
        'warn' => 'OPcache prüft jede Datei auf Änderungen.',
        'not_loaded' => 'die OPcache-Erweiterung ist nicht geladen',
        'off' => 'OPcache ist ausgeschaltet',
        'timestamps' => 'jeder Request prüft die Dateien auf Änderungen. Ausschalten und PHP bei jedem Deploy neu laden.',
        'command_line' => 'Nicht geprüft: Die Kommandozeile hat eigene OPcache-Einstellungen. Die Prüfung antwortet in einem Web-Request, etwa am Endpunkt von laravel-health mit frischen Ergebnissen.',
    ],

    'exception-arguments' => [
        'label' => 'Argumente in Traces von Exceptions',
        'pass' => 'Die Traces von Exceptions auf der Kommandozeile halten keine Argumente fest. PHP-FPM liest eine eigene php.ini und wird beurteilt, wenn die Prüfung in einem Web-Request läuft.',
        'pass_web' => 'Die Traces von Exceptions im Webserver halten keine Argumente fest. Die Kommandozeile liest eine eigene php.ini und wird beurteilt, wenn die Prüfung auf der Kommandozeile läuft.',
        'fail' => 'Die Traces von Exceptions auf der Kommandozeile halten die Argumente jedes Aufrufs fest.',
        'fail_web' => 'Die Traces von Exceptions im Webserver halten die Argumente jedes Aufrufs fest.',
        'finding' => 'Laravel schreibt den Trace jeder gemeldeten Exception ins Log, und ein kurzes Geheimnis steht dort ganz. zend.exception_ignore_args = On in der php.ini der Kommandozeile und von PHP-FPM setzen und PHP neu laden.',
    ],

];
