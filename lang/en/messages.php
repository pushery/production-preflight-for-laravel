<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Pass',
        'warn' => 'Warning',
        'fail' => 'Fail',
        'undetermined' => 'Not checked',
    ],

    'acknowledged' => 'Acknowledged: :reason Found: :summary',
    'unreadable' => 'Not checked: :path cannot be read.',
    'empty' => '(empty)',
    'line' => ':key (line :line)',

    'stage' => [
        'undetermined' => 'Not checked: the stage the application declares is none this package knows. Name it under production-preflight.stages.',
        'development' => 'Nothing to check: the production rules apply in production and on staging, and the application declares a development stage.',
    ],

    'command' => [
        'crashed' => 'Not checked: the check stopped with :exception.',
        'evidence' => 'value: :evidence',
        'file' => 'File: :path',
        'unreadable' => 'Cannot read: :path',
        'unreadable_note' => 'That is not the same as clean: nothing was checked.',
        'unrecorded' => 'The deploy could not be recorded in :path, so the queue-restart check has nothing to compare with.',
        'values_in_deploy' => 'Values are not printed in a deploy: its output is kept. Run the command without --deploy to see them.',
        'exit' => [
            '0' => 'No check failed.',
            '1' => 'At least one check failed.',
            '2' => 'No check failed, and at least one could not look. That is not a pass.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Keys set twice',
        'pass' => 'No key is set twice.',
        'fail' => 'Keys set more than once: :count, with DIFFERENT values for at least one. The value set LAST applies, so a setting stands in the file and does not apply. Remove the extra lines instead of adding another.',
        'warn' => 'Keys set more than once: :count, each with equal values. The value set LAST applies. Remove the extra lines instead of adding another.',
        'finding' => 'set on lines :lines, :values',
        'different' => 'DIFFERENT values',
        'equal' => 'equal values',
        'evidence' => ':lines, in effect: :value',
        'line' => 'line :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Keys of the example file',
        'pass' => 'The .env and its example file name the same keys.',
        'fail' => 'Keys of the example file missing from .env: :missing. Each reads null, and its feature does nothing without a word. Keys of .env missing from the example file: :undocumented.',
        'warn' => 'Keys of .env missing from the example file: :undocumented. The next installation is set up from the example and will miss them.',
        'missing' => 'named in the example file, missing from .env',
        'undocumented' => 'set in .env, missing from the example file',
    ],

    'placeholder-values' => [
        'label' => 'Placeholder values',
        'pass' => 'No value of .env is a placeholder.',
        'fail' => 'Values that were never replaced: :count.',
        'placeholder' => 'the value is a placeholder',
        'from_example' => 'the value is the one the committed example file carries, so everyone who reads the repository knows it',
        'required' => 'required, and missing or empty',
    ],

    'unquoted-hash' => [
        'label' => 'Unquoted #',
        'pass' => 'No unquoted value holds a # that cuts it off.',
        'fail' => 'Values cut off at an unquoted #: :count. The application reads only the part before it.',
        'warn' => 'Values that read like text cut off at an unquoted #: :count.',
        'cut' => 'an unquoted # inside the value starts a comment, so the application reads only the part before it. Quote the value.',
        'ambiguous' => 'the # after the space starts a comment, so the application reads only the part before it. Quote the value, or write a space after the # if it is a comment.',
        'evidence' => 'the application reads :value, the file says :raw',
    ],

    'env-bytes' => [
        'label' => 'Bytes that do not show',
        'pass' => 'No byte order mark, no carriage return, no whitespace after a value.',
        'warn' => 'Laravel reads these bytes correctly. A shell or a tool that reads the file as text does not, and it then sees values other than the ones that stand there.',
        'mark' => 'byte order mark',
        'mark_statement' => 'the file starts with one, and a shell that sources the file reads it as part of the first name',
        'endings' => 'line endings',
        'endings_statement' => ':count lines end in a carriage return, which a shell keeps at the end of each value',
        'whitespace' => 'the value ends in whitespace, which a tool that takes the line literally, such as Docker\'s --env-file, keeps',
    ],

    'app-debug' => [
        'label' => 'Debug mode',
        'pass' => 'Debug mode is off.',
        'fail' => 'Debug mode is on in production.',
        'finding' => 'an error page shows the stack trace and the environment, the content of .env included, to whoever triggers the error',
    ],

    'app-key' => [
        'label' => 'Application key',
        'pass' => 'The application key is set, and it is neither a placeholder nor the one of the example file.',
        'fail' => 'The application key is missing or known to others, so sessions, cookies and encrypted payloads can be forged.',
        'missing' => 'no key is set',
        'placeholder' => 'the key is a placeholder an old Laravel release shipped in its example file',
        'committed' => 'the key is the one the committed example file carries',
    ],

    'mail-transport' => [
        'label' => 'Mail delivery',
        'pass' => 'The default mailer sends mail off the server.',
        'fail' => 'The default mailer delivers no mail: registrations, password resets and invoices vanish without a sound.',
        'warn' => 'The default mailer sends to this machine. A relay there has to pass the mail on.',
        'unconfigured' => 'the mailer is not configured, so sending raises an error',
        'log' => 'the log transport writes every message into the log and sends none',
        'array' => 'the array transport keeps every message in memory and sends none',
        'catcher' => 'the SMTP host is a mail catcher, where messages stop and no inbox receives them',
        'local' => 'the SMTP host is the machine itself: a valid relay, or a development setting nobody changed',
    ],

    'health-notifications' => [
        'label' => 'A second road for alerts',
        'pass' => 'A failing check reaches you through a channel besides mail.',
        'pulled' => 'Oh Dear pulls the results, independent of the application\'s mail.',
        'warn' => 'A failing check in operation would not reach you if mail broke.',
        'off' => 'notifications are switched off, and nothing pulls the results',
        'mail_only' => 'a failing check is delivered by mail alone, and when mail breaks, the message about it is the one that does not arrive. Add a Slack webhook or the Oh Dear endpoint.',
        'none' => 'no channel delivers a failing check',
    ],

    'test-mode-keys' => [
        'label' => 'Credentials in test mode',
        'pass' => 'No credential of the configuration is in test mode.',
        'fail' => 'Settings in test mode: :count. No real payment moves.',
        'stripe' => 'a Stripe key in test mode',
        'mollie' => 'a Mollie key in test mode',
        'sandbox' => 'sandbox mode is on',
    ],

    'queue-connection' => [
        'label' => 'Queue connection',
        'pass' => 'The default queue connection hands jobs to workers.',
        'fail' => 'The default queue connection runs no job.',
        'warn' => 'The default queue connection runs every job inside the request.',
        'unconfigured' => 'the default connection is not configured, so dispatching a job raises an error',
        'null' => 'the null driver discards every job it is given',
        'sync' => 'jobs run inside the request that dispatches them, so every response waits for them. A choice for a small installation: acknowledge it if it is yours.',
    ],

    'session-driver' => [
        'label' => 'Session driver',
        'pass' => 'Sessions outlive the request.',
        'fail' => 'Sessions last for one request.',
        'array' => 'the array driver forgets every session after the request, so every user is signed out after each page',
    ],

    'cache-store' => [
        'label' => 'Cache store',
        'pass' => 'The default cache store keeps what it is given.',
        'fail' => 'The default cache store keeps nothing past the request.',
        'unconfigured' => 'the default store is not configured, so the first cache access raises an error',
        'transient' => 'the rate limiters, the atomic locks and the restart signal of the queue workers live in the cache and are gone after each request',
    ],

    'secure-cookie' => [
        'label' => 'Secure session cookie',
        'pass' => 'The session cookie carries the Secure flag.',
        'fail' => 'The session cookie lacks the Secure flag on a site that runs over HTTPS.',
        'finding' => 'a browser also sends the cookie over plain HTTP, where anyone on the network can read it. Set SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url names http, where a Secure cookie would not be sent at all.',
        'no_scheme' => 'Not checked: app.url names no scheme, so whether the site runs over HTTPS is unknown.',
    ],

    'debug-tools' => [
        'label' => 'Debugging tools',
        'pass' => 'No debugging tool is loaded.',
        'fail' => 'A debugging tool is open in production.',
        'warn' => 'A debugging tool is loaded and dormant.',
        'telescope' => 'Telescope records requests, queries and mail, and its dashboard stands one gate away from the public',
        'debugbar' => 'the debug bar shows every visitor the queries, the session and the request of each page',
        'ignition' => 'dormant while debug mode is off, and an open view into the application the moment it is on',
    ],

    'log-level' => [
        'label' => 'Log level',
        'pass' => 'The default log channel does not log at debug level.',
        'warn' => 'The default log channel logs at debug level.',
        'unconfigured' => 'Not checked: the default log channel is not configured.',
        'debug' => 'every detail lands on the disk, and it fills at a pace one can calculate',
    ],

    'config-cache-fresh' => [
        'label' => 'Cached configuration',
        'pass' => 'The cached configuration is newer than .env.',
        'fail' => 'The application runs on a configuration from before the last change to .env.',
        'finding' => '.env changed after the configuration was cached, so its new values do not apply. Run php artisan config:cache.',
        'not_cached' => 'The configuration is not cached, so every request reads .env and a change applies at once.',
        'no_env' => 'The cached configuration is the whole configuration, and no .env stands beside it.',
    ],

    'queue-restart' => [
        'label' => 'Queue workers after the deploy',
        'pass' => 'The queue workers were told to restart after the last deploy.',
        'fail' => 'The queue workers run the code from before the last deploy.',
        'never' => 'no restart signal was ever sent, so the workers run the code they started with. Add php artisan queue:restart to the end of the deploy script.',
        'stale' => 'the last restart signal is older than the last deploy. Add php artisan queue:restart to the end of the deploy script.',
        'no_worker' => 'The default queue connection runs no worker to restart.',
        'no_deploy' => 'Not checked: no deploy is on record. Add php artisan preflight --deploy to the deploy script, ahead of php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Pending migrations',
        'pass' => 'Every migration has run.',
        'fail' => 'Migrations that have not run: :count. The code is new, the schema is not.',
        'pending' => 'has not run',
        'no_migrator' => 'Not checked: the application has no migrator.',
    ],

    'writable-directories' => [
        'label' => 'Writable directories',
        'pass' => 'Every directory Laravel writes to is writable.',
        'fail' => 'Directories Laravel cannot write to: :count. The first user action that writes falls over.',
        'missing' => 'the directory is missing',
        'read_only' => 'the directory is not writable for the user that runs this check',
    ],

    'storage-link' => [
        'label' => 'Storage link',
        'pass' => 'Every configured storage link points where it should.',
        'fail' => 'Files on the public disk answer 404.',
        'warn' => 'The next file stored on the public disk will answer 404.',
        'elsewhere' => 'the link points somewhere else',
        'directory' => 'a real directory stands where the link belongs, and a file stored later never shows up in it',
        'missing_with_files' => 'the link is missing, and its target already holds files. Run php artisan storage:link.',
        'missing' => 'the link is missing. Run php artisan storage:link.',
    ],

    'env-exposure' => [
        'label' => 'Secrets in the file system',
        'pass' => '.env is not open to every user of the server, and the web root holds neither .env nor .git.',
        'fail' => 'Files that hand out secrets: :count.',
        'world_readable' => 'every user of the server can read it',
        'world_writable' => 'every user of the server can write it',
        'web_root' => 'it lies in the web root, where the web server may serve it',
    ],

    'env-over-http' => [
        'label' => 'Secrets over HTTP',
        'pass' => 'The site serves neither .env nor .git/config.',
        'fail' => 'The site serves a secret file to anyone who asks.',
        'served' => 'the site answers 200 with the content of the file',
        'off' => 'Not checked: whether the site serves .env takes a request to the site, and the probe is off. Switch it on with PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Not checked: app.url is no http or https address to request.',
        'unanswered' => 'Not checked: the site did not answer.',
    ],

    'dev-dependencies' => [
        'label' => 'Development dependencies',
        'pass' => 'No development package is installed.',
        'fail' => 'Development packages installed: :count.',
        'finding' => 'test runners, debuggers and code generators are installed. Install with composer install --no-dev.',
        'unreadable' => 'Not checked: :path cannot be read.',
    ],

    'optimized-autoloader' => [
        'label' => 'Optimized autoloader',
        'pass' => 'The autoloader is optimized.',
        'warn' => 'The autoloader looks every class up on the disk.',
        'finding' => 'install with composer install --optimize-autoloader',
        'unreadable' => 'Not checked: composer.json or the class map of the autoloader cannot be read.',
    ],

    'php-version-parity' => [
        'label' => 'PHP version of the web server',
        'pass' => 'The command line and the web server run PHP :cli.',
        'fail' => 'The command line runs PHP :cli and the web server PHP :web.',
        'finding' => 'migrations and scheduled commands run under PHP :cli, the application under PHP :web',
        'unnamed' => 'Not checked: the web server\'s PHP is a binary this check runs, and none is named. Set PRODUCTION_PREFLIGHT_PHP_FPM, for example to /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Not checked: this host does not allow starting a process.',
        'unanswered' => 'Not checked: the named binary did not report a PHP version.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache is on and does not check files for changes.',
        'fail' => 'OPcache does not run in the web server.',
        'warn' => 'OPcache checks every file for changes.',
        'not_loaded' => 'the OPcache extension is not loaded',
        'off' => 'OPcache is switched off',
        'timestamps' => 'every request checks the files for changes. Switch it off and reload PHP on each deploy.',
        'command_line' => 'Not checked: the command line has OPcache settings of its own. The check answers in a web request, such as laravel-health\'s endpoint with fresh results.',
    ],

];
