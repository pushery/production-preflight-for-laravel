<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Réussi',
        'warn' => 'Avertissement',
        'fail' => 'Échec',
        'undetermined' => 'Non vérifié',
    ],

    'acknowledged' => 'Accepté : :reason Constaté : :summary',
    'unreadable' => 'Non vérifié : :path est illisible.',
    'empty' => '(vide)',
    'line' => ':key (ligne :line)',

    'stage' => [
        'undetermined' => 'Non vérifié : ce paquet ne connaît pas l’environnement que déclare l’application. Ajoutez-le sous production-preflight.stages.',
        'development' => 'Rien à vérifier : les règles de production s’appliquent en production et en préproduction, et l’application déclare un environnement de développement.',
    ],

    'command' => [
        'crashed' => 'Non vérifié : la vérification s’est arrêtée sur :exception.',
        'evidence' => 'valeur : :evidence',
        'file' => 'Fichier : :path',
        'unreadable' => 'Illisible : :path',
        'unreadable_note' => 'Ce n’est pas la même chose que propre : rien n’a été vérifié.',
        'unrecorded' => 'Le déploiement n’a pas pu être enregistré dans :path, la vérification queue-restart n’a donc rien à comparer.',
        'values_in_deploy' => 'Un déploiement n’affiche pas de valeurs : sa sortie est conservée. Lancez la commande sans --deploy pour les voir.',
        'exit' => [
            '0' => 'Aucune vérification n’a échoué.',
            '1' => 'Au moins une vérification a échoué.',
            '2' => 'Aucune vérification n’a échoué, et au moins une n’a pas pu regarder. Ce n’est pas une réussite.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Clés définies deux fois',
        'pass' => 'Aucune clé n’est définie deux fois.',
        'fail' => 'Clés définies plusieurs fois : :count, dont au moins une avec des valeurs DIFFÉRENTES. C’est la valeur définie en DERNIER qui s’applique : un réglage figure dans le fichier sans s’appliquer. Supprimez les lignes en trop au lieu d’en ajouter une autre.',
        'warn' => 'Clés définies plusieurs fois : :count, chacune avec des valeurs identiques. C’est la valeur définie en DERNIER qui s’applique. Supprimez les lignes en trop au lieu d’en ajouter une autre.',
        'finding' => 'définie aux lignes :lines, :values',
        'different' => 'valeurs DIFFÉRENTES',
        'equal' => 'valeurs identiques',
        'evidence' => ':lines, appliquée : :value',
        'line' => 'ligne :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Clés du fichier d’exemple',
        'pass' => 'Le .env et son fichier d’exemple nomment les mêmes clés.',
        'fail' => 'Clés du fichier d’exemple absentes de .env : :missing. Chacune vaut null, et sa fonctionnalité ne fait rien, sans un mot. Clés de .env absentes du fichier d’exemple : :undocumented.',
        'warn' => 'Clés de .env absentes du fichier d’exemple : :undocumented. La prochaine installation part de l’exemple et ne les aura pas.',
        'missing' => 'nommée dans le fichier d’exemple, absente de .env',
        'undocumented' => 'définie dans .env, absente du fichier d’exemple',
    ],

    'placeholder-values' => [
        'label' => 'Valeurs factices',
        'pass' => 'Aucune valeur de .env n’est une valeur factice.',
        'fail' => 'Valeurs jamais remplacées : :count.',
        'placeholder' => 'la valeur est une valeur factice',
        'from_example' => 'la valeur est celle du fichier d’exemple versionné, donc quiconque lit le dépôt la connaît',
        'required' => 'obligatoire, et absente ou vide',
    ],

    'unquoted-hash' => [
        'label' => '# sans guillemets',
        'pass' => 'Aucune valeur sans guillemets ne contient un # qui la coupe.',
        'fail' => 'Valeurs coupées à un # sans guillemets : :count. L’application ne lit que la partie qui précède.',
        'warn' => 'Valeurs qui ressemblent à un texte coupé à un # sans guillemets : :count.',
        'cut' => 'un # sans guillemets dans la valeur ouvre un commentaire, l’application ne lit donc que la partie qui précède. Mettez la valeur entre guillemets.',
        'ambiguous' => 'le # après l’espace ouvre un commentaire, l’application ne lit donc que la partie qui précède. Mettez la valeur entre guillemets, ou écrivez une espace après le # s’il s’agit d’un commentaire.',
        'evidence' => 'l’application lit :value, le fichier indique :raw',
    ],

    'env-bytes' => [
        'label' => 'Octets invisibles',
        'pass' => 'Aucune marque d’ordre des octets, aucun retour chariot, aucune espace après une valeur.',
        'warn' => 'Laravel lit ces octets correctement. Un shell ou un outil qui lit le fichier comme du texte ne le fait pas, et voit alors d’autres valeurs que celles qui figurent dans le fichier.',
        'mark' => 'marque d’ordre des octets',
        'mark_statement' => 'le fichier commence par une, et un shell qui charge le fichier la prend pour une partie du premier nom',
        'endings' => 'fins de ligne',
        'endings_statement' => ':count lignes se terminent par un retour chariot, qu’un shell conserve à la fin de chaque valeur',
        'whitespace' => 'la valeur se termine par des espaces, qu’un outil qui prend la ligne à la lettre conserve, comme --env-file de Docker',
    ],

    'app-debug' => [
        'label' => 'Mode débogage',
        'pass' => 'Le mode débogage est désactivé.',
        'fail' => 'Le mode débogage est activé en production.',
        'finding' => 'une page d’erreur montre la pile d’appels et l’environnement, contenu de .env compris, à quiconque déclenche l’erreur',
    ],

    'app-key' => [
        'label' => 'Clé de l’application',
        'pass' => 'La clé de l’application est définie, et ce n’est ni une valeur factice ni celle du fichier d’exemple.',
        'fail' => 'La clé de l’application manque ou est connue d’autres, sessions, cookies et données chiffrées peuvent donc être falsifiés.',
        'missing' => 'aucune clé n’est définie',
        'placeholder' => 'la clé est une valeur factice qu’une ancienne version de Laravel livrait dans son fichier d’exemple',
        'committed' => 'la clé est celle du fichier d’exemple versionné',
    ],

    'mail-transport' => [
        'label' => 'Envoi des e-mails',
        'pass' => 'Le mailer par défaut envoie les e-mails hors du serveur.',
        'fail' => 'Le mailer par défaut ne distribue aucun e-mail : inscriptions, réinitialisations de mot de passe et factures disparaissent sans bruit.',
        'warn' => 'Le mailer par défaut envoie vers cette machine. Un relais à cet endroit doit transmettre les e-mails.',
        'unconfigured' => 'le mailer n’est pas configuré, l’envoi lève donc une erreur',
        'log' => 'le transport log écrit chaque message dans le journal et n’en envoie aucun',
        'array' => 'le transport array garde chaque message en mémoire et n’en envoie aucun',
        'catcher' => 'l’hôte SMTP est un capteur d’e-mails, où les messages s’arrêtent sans qu’aucune boîte ne les reçoive',
        'local' => 'l’hôte SMTP est la machine elle-même : un relais valable, ou un réglage de développement que personne n’a changé',
    ],

    'health-notifications' => [
        'label' => 'Une seconde voie pour les alertes',
        'pass' => 'Une vérification en échec vous parvient par un canal autre que l’e-mail.',
        'pulled' => 'Oh Dear récupère les résultats, indépendamment des e-mails de l’application.',
        'warn' => 'Une vérification en échec en exploitation ne vous parviendrait pas si l’e-mail tombait en panne.',
        'off' => 'les notifications sont désactivées, et rien ne récupère les résultats',
        'mail_only' => 'une vérification en échec n’est distribuée que par e-mail, et quand l’e-mail tombe en panne, c’est justement le message à ce sujet qui n’arrive pas. Ajoutez un webhook Slack ou le point d’accès Oh Dear.',
        'none' => 'aucun canal ne distribue une vérification en échec',
    ],

    'test-mode-keys' => [
        'label' => 'Identifiants en mode test',
        'pass' => 'Aucun identifiant de la configuration n’est en mode test.',
        'fail' => 'Réglages en mode test : :count. Aucun paiement réel n’a lieu.',
        'stripe' => 'une clé Stripe en mode test',
        'mollie' => 'une clé Mollie en mode test',
        'sandbox' => 'le mode sandbox est activé',
    ],

    'queue-connection' => [
        'label' => 'Connexion de file d’attente',
        'pass' => 'La connexion de file par défaut confie les tâches à des workers.',
        'fail' => 'La connexion de file par défaut n’exécute aucune tâche.',
        'warn' => 'La connexion de file par défaut exécute chaque tâche dans la requête.',
        'unconfigured' => 'la connexion par défaut n’est pas configurée, mettre une tâche en file lève donc une erreur',
        'null' => 'le driver null rejette chaque tâche qu’il reçoit',
        'sync' => 'les tâches s’exécutent dans la requête qui les met en file, chaque réponse les attend donc. Un choix pour une petite installation : acceptez-le si c’est le vôtre.',
    ],

    'session-driver' => [
        'label' => 'Driver de session',
        'pass' => 'Les sessions survivent à la requête.',
        'fail' => 'Les sessions durent une requête.',
        'array' => 'le driver array oublie chaque session après la requête, chaque utilisateur est donc déconnecté après chaque page',
    ],

    'cache-store' => [
        'label' => 'Stockage du cache',
        'pass' => 'Le stockage de cache par défaut conserve ce qu’il reçoit.',
        'fail' => 'Le stockage de cache par défaut ne conserve rien au-delà de la requête.',
        'unconfigured' => 'le stockage par défaut n’est pas configuré, le premier accès au cache lève donc une erreur',
        'transient' => 'les limiteurs de débit, les verrous atomiques et le signal de redémarrage des workers vivent dans le cache et disparaissent après chaque requête',
    ],

    'secure-cookie' => [
        'label' => 'Cookie de session sécurisé',
        'pass' => 'Le cookie de session porte le drapeau Secure.',
        'fail' => 'Le cookie de session n’a pas le drapeau Secure sur un site servi en HTTPS.',
        'finding' => 'un navigateur envoie aussi le cookie en HTTP non chiffré, où n’importe qui sur le réseau peut le lire. Définissez SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url indique http, où un cookie Secure ne serait pas envoyé du tout.',
        'no_scheme' => 'Non vérifié : app.url n’indique aucun schéma, on ignore donc si le site est servi en HTTPS.',
    ],

    'debug-tools' => [
        'label' => 'Outils de débogage',
        'pass' => 'Aucun outil de débogage n’est chargé.',
        'fail' => 'Un outil de débogage est ouvert en production.',
        'warn' => 'Un outil de débogage est chargé et en sommeil.',
        'telescope' => 'Telescope enregistre requêtes, requêtes SQL et e-mails, et son tableau de bord n’est qu’à un gate du public',
        'debugbar' => 'la barre de débogage montre à chaque visiteur les requêtes, la session et la requête de chaque page',
        'ignition' => 'en sommeil tant que le mode débogage est désactivé, et une vue ouverte sur l’application dès qu’il est activé',
    ],

    'log-level' => [
        'label' => 'Niveau de journalisation',
        'pass' => 'Le canal de journal par défaut ne journalise pas au niveau debug.',
        'warn' => 'Le canal de journal par défaut journalise au niveau debug.',
        'unconfigured' => 'Non vérifié : le canal de journal par défaut n’est pas configuré.',
        'debug' => 'chaque détail atterrit sur le disque, qui se remplit à un rythme calculable',
    ],

    'config-cache-fresh' => [
        'label' => 'Configuration en cache',
        'pass' => 'La configuration en cache est plus récente que .env.',
        'fail' => 'L’application tourne sur une configuration antérieure à la dernière modification de .env.',
        'finding' => '.env a changé après la mise en cache de la configuration, ses nouvelles valeurs ne s’appliquent donc pas. Lancez php artisan config:cache.',
        'not_cached' => 'La configuration n’est pas en cache, chaque requête lit donc .env et une modification s’applique aussitôt.',
        'no_env' => 'La configuration en cache est toute la configuration, et aucun .env ne se trouve à côté.',
    ],

    'queue-restart' => [
        'label' => 'Workers de file après le déploiement',
        'pass' => 'Les workers de file ont reçu l’ordre de redémarrer après le dernier déploiement.',
        'fail' => 'Les workers de file exécutent le code d’avant le dernier déploiement.',
        'never' => 'aucun signal de redémarrage n’a jamais été envoyé, les workers exécutent donc le code avec lequel ils ont démarré. Ajoutez php artisan queue:restart à la fin du script de déploiement.',
        'stale' => 'le dernier signal de redémarrage est antérieur au dernier déploiement. Ajoutez php artisan queue:restart à la fin du script de déploiement.',
        'no_worker' => 'La connexion de file par défaut n’a aucun worker à redémarrer.',
        'no_deploy' => 'Non vérifié : aucun déploiement n’est enregistré. Ajoutez php artisan preflight --deploy au script de déploiement, avant php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Migrations en attente',
        'pass' => 'Toutes les migrations ont été exécutées.',
        'fail' => 'Migrations non exécutées : :count. Le code est nouveau, le schéma ne l’est pas.',
        'pending' => 'n’a pas été exécutée',
        'no_migrator' => 'Non vérifié : l’application n’a pas de migrateur.',
    ],

    'writable-directories' => [
        'label' => 'Répertoires accessibles en écriture',
        'pass' => 'Chaque répertoire où Laravel écrit est accessible en écriture.',
        'fail' => 'Répertoires où Laravel ne peut pas écrire : :count. La première action d’un utilisateur qui écrit échoue.',
        'missing' => 'le répertoire est absent',
        'read_only' => 'le répertoire n’est pas accessible en écriture pour l’utilisateur qui exécute cette vérification',
    ],

    'storage-link' => [
        'label' => 'Lien de stockage',
        'pass' => 'Chaque lien de stockage configuré pointe là où il le doit.',
        'fail' => 'Les fichiers du disque public répondent 404.',
        'warn' => 'Le prochain fichier enregistré sur le disque public répondra 404.',
        'elsewhere' => 'le lien pointe ailleurs',
        'directory' => 'un vrai répertoire se trouve à la place du lien, et un fichier enregistré plus tard n’y apparaît jamais',
        'missing_with_files' => 'le lien est absent, et sa cible contient déjà des fichiers. Lancez php artisan storage:link.',
        'missing' => 'le lien est absent. Lancez php artisan storage:link.',
    ],

    'env-exposure' => [
        'label' => 'Secrets dans le système de fichiers',
        'pass' => '.env n’est pas ouvert à tous les utilisateurs du serveur, et la racine web ne contient ni .env ni .git.',
        'fail' => 'Fichiers qui livrent des secrets : :count.',
        'world_readable' => 'tout utilisateur du serveur peut le lire',
        'world_writable' => 'tout utilisateur du serveur peut l’écrire',
        'web_root' => 'il se trouve dans la racine web, où le serveur web peut le servir',
    ],

    'env-over-http' => [
        'label' => 'Secrets par HTTP',
        'pass' => 'Le site ne sert ni .env ni .git/config.',
        'fail' => 'Le site sert un fichier secret à quiconque le demande.',
        'served' => 'le site répond 200 avec le contenu du fichier',
        'off' => 'Non vérifié : savoir si le site sert .env demande une requête au site, et la sonde est désactivée. Activez-la avec PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Non vérifié : app.url n’est pas une adresse http ou https à interroger.',
        'unanswered' => 'Non vérifié : le site n’a pas répondu.',
    ],

    'dev-dependencies' => [
        'label' => 'Dépendances de développement',
        'pass' => 'Aucun paquet de développement n’est installé.',
        'fail' => 'Paquets de développement installés : :count.',
        'finding' => 'des lanceurs de tests, des débogueurs et des générateurs de code sont installés. Installez avec composer install --no-dev.',
        'unreadable' => 'Non vérifié : :path est illisible.',
    ],

    'optimized-autoloader' => [
        'label' => 'Autoloader optimisé',
        'pass' => 'L’autoloader est optimisé.',
        'warn' => 'L’autoloader cherche chaque classe sur le disque.',
        'finding' => 'installez avec composer install --optimize-autoloader',
        'unreadable' => 'Non vérifié : composer.json ou la table des classes de l’autoloader est illisible.',
    ],

    'php-version-parity' => [
        'label' => 'Version PHP du serveur web',
        'pass' => 'La ligne de commande et le serveur web utilisent PHP :cli.',
        'fail' => 'La ligne de commande utilise PHP :cli et le serveur web PHP :web.',
        'finding' => 'les migrations et les commandes planifiées tournent sous PHP :cli, l’application sous PHP :web',
        'unnamed' => 'Non vérifié : le PHP du serveur web est un binaire que cette vérification exécute, et aucun n’est indiqué. Définissez PRODUCTION_PREFLIGHT_PHP_FPM, par exemple sur /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Non vérifié : cet hôte ne permet pas de lancer un processus.',
        'unanswered' => 'Non vérifié : le binaire indiqué n’a signalé aucune version de PHP.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache est activé et ne vérifie pas les modifications des fichiers.',
        'fail' => 'OPcache ne fonctionne pas dans le serveur web.',
        'warn' => 'OPcache vérifie les modifications de chaque fichier.',
        'not_loaded' => 'l’extension OPcache n’est pas chargée',
        'off' => 'OPcache est désactivé',
        'timestamps' => 'chaque requête vérifie si les fichiers ont changé. Désactivez-le et rechargez PHP à chaque déploiement.',
        'command_line' => 'Non vérifié : la ligne de commande a ses propres réglages OPcache. La vérification répond dans une requête web, comme le point d’accès de laravel-health avec des résultats frais.',
    ],

];
