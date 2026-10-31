<?php

declare(strict_types=1);

return [

    'verdict' => [
        'pass' => 'Correcto',
        'warn' => 'Aviso',
        'fail' => 'Fallo',
        'undetermined' => 'Sin comprobar',
    ],

    'acknowledged' => 'Asumido: :reason Encontrado: :summary',
    'unreadable' => 'Sin comprobar: no se puede leer :path.',
    'empty' => '(vacío)',
    'line' => ':key (línea :line)',

    'stage' => [
        'undetermined' => 'Sin comprobar: este paquete no conoce el entorno que declara la aplicación. Añádelo en production-preflight.stages.',
        'development' => 'Nada que comprobar: las reglas de producción se aplican en producción y en staging, y la aplicación declara un entorno de desarrollo.',
    ],

    'command' => [
        'crashed' => 'Sin comprobar: la comprobación se detuvo con :exception.',
        'evidence' => 'valor: :evidence',
        'file' => 'Archivo: :path',
        'unreadable' => 'No se puede leer: :path',
        'unreadable_note' => 'Eso no es lo mismo que limpio: no se comprobó nada.',
        'unrecorded' => 'No se pudo registrar el despliegue en :path, así que la comprobación queue-restart no tiene con qué comparar.',
        'values_in_deploy' => 'En un despliegue no se muestran valores: su salida se conserva. Ejecuta el comando sin --deploy para verlos.',
        'exit' => [
            '0' => 'Ninguna comprobación ha fallado.',
            '1' => 'Al menos una comprobación ha fallado.',
            '2' => 'Ninguna comprobación ha fallado, y al menos una no pudo mirar. Eso no es un aprobado.',
        ],
    ],

    'duplicate-env-keys' => [
        'label' => 'Claves definidas dos veces',
        'pass' => 'Ninguna clave está definida dos veces.',
        'fail' => 'Claves definidas más de una vez: :count, al menos una con valores DISTINTOS. Se aplica el valor definido en ÚLTIMO lugar, así que hay un ajuste en el archivo que no se aplica. Elimina las líneas sobrantes en lugar de añadir otra.',
        'warn' => 'Claves definidas más de una vez: :count, cada una con valores iguales. Se aplica el valor definido en ÚLTIMO lugar. Elimina las líneas sobrantes en lugar de añadir otra.',
        'finding' => 'definida en las líneas :lines, :values',
        'different' => 'valores DISTINTOS',
        'equal' => 'valores iguales',
        'evidence' => ':lines, se aplica: :value',
        'line' => 'línea :line = :value',
    ],

    'env-example-keys' => [
        'label' => 'Claves del archivo de ejemplo',
        'pass' => 'El .env y su archivo de ejemplo nombran las mismas claves.',
        'pass_optional' => 'El .env nombra todas las claves de su archivo de ejemplo, salvo :optional, que la aplicación declara opcionales.',
        'fail' => 'Claves del archivo de ejemplo que faltan en .env: :missing. Cada una devuelve null, y su función no hace nada sin decir nada. Claves de .env que faltan en el archivo de ejemplo: :undocumented.',
        'warn' => 'Claves de .env que faltan en el archivo de ejemplo: :undocumented. La próxima instalación se crea a partir del ejemplo y no las tendrá.',
        'missing' => 'nombrada en el archivo de ejemplo, falta en .env',
        'undocumented' => 'definida en .env, falta en el archivo de ejemplo',
    ],

    'placeholder-values' => [
        'label' => 'Valores de marcador',
        'pass' => 'Ningún valor de .env es un marcador.',
        'fail' => 'Valores que nunca se sustituyeron: :count.',
        'placeholder' => 'el valor es un marcador',
        'from_example' => 'el valor es el del archivo de ejemplo versionado, así que lo conoce cualquiera que lea el repositorio',
        'required' => 'obligatoria, y falta o está vacía',
    ],

    'unquoted-hash' => [
        'label' => '# sin comillas',
        'pass' => 'Ningún valor sin comillas contiene un # que lo corte.',
        'fail' => 'Valores cortados en un # sin comillas: :count. La aplicación solo lee la parte anterior.',
        'warn' => 'Valores que parecen texto cortado en un # sin comillas: :count.',
        'cut' => 'un # sin comillas dentro del valor inicia un comentario, así que la aplicación solo lee la parte anterior. Pon el valor entre comillas.',
        'ambiguous' => 'el # tras el espacio inicia un comentario, así que la aplicación solo lee la parte anterior. Pon el valor entre comillas, o escribe un espacio tras el # si es un comentario.',
        'evidence' => 'la aplicación lee :value, el archivo dice :raw',
    ],

    'env-bytes' => [
        'label' => 'Bytes que no se ven',
        'pass' => 'Sin marca de orden de bytes, sin retorno de carro, sin espacios tras un valor.',
        'warn' => 'Laravel lee estos bytes correctamente. Una shell o una herramienta que lee el archivo como texto no, y entonces ve valores distintos de los que figuran.',
        'mark' => 'marca de orden de bytes',
        'mark_statement' => 'el archivo empieza con una, y una shell que carga el archivo la toma como parte del primer nombre',
        'endings' => 'finales de línea',
        'endings_statement' => ':count líneas terminan en un retorno de carro, que una shell conserva al final de cada valor',
        'whitespace' => 'el valor termina en espacios, que conserva una herramienta que toma la línea al pie de la letra, como --env-file de Docker',
    ],

    'development-stage-server' => [
        'label' => 'Entorno de desarrollo en un servidor',
        'pass' => 'La aplicación se sirve en :host, que es una máquina de desarrollo.',
        'not_development' => 'Nada que comprobar: solo un entorno de desarrollo se lee contra su host, y la aplicación declara el entorno :stage.',
        'no_host' => 'app.url no nombra ningún host, así que no se puede juzgar si un servidor declara un entorno de desarrollo.',
        'fail' => 'APP_ENV es :env y app.url nombra :host: un servidor que declara un entorno de desarrollo, en el que pasa toda comprobación de producción.',
        'finding' => ':host no es un host bajo el que se sirva una máquina de desarrollo, así que APP_ENV probablemente conserva el valor de .env.example',
    ],

    'app-debug' => [
        'label' => 'Modo de depuración',
        'pass' => 'El modo de depuración está desactivado.',
        'fail' => 'El modo de depuración está activado en producción.',
        'finding' => 'una página de error muestra la traza y el entorno, incluido el contenido de .env, a quien provoque el error',
    ],

    'app-key' => [
        'label' => 'Clave de la aplicación',
        'pass' => 'La clave de la aplicación está definida, y no es ni un marcador ni la del archivo de ejemplo.',
        'fail' => 'La clave de la aplicación falta o la conocen otros, así que se pueden falsificar sesiones, cookies y datos cifrados.',
        'missing' => 'no hay ninguna clave definida',
        'placeholder' => 'la clave es un marcador que una versión antigua de Laravel incluía en su archivo de ejemplo',
        'committed' => 'la clave es la del archivo de ejemplo versionado',
    ],

    'mail-transport' => [
        'label' => 'Envío de correo',
        'pass' => 'El mailer predeterminado envía el correo fuera del servidor.',
        'fail' => 'El mailer predeterminado no entrega ningún correo: registros, restablecimientos de contraseña y facturas desaparecen sin hacer ruido.',
        'warn' => 'El mailer predeterminado envía a esta misma máquina. Un relay allí tiene que reenviar el correo.',
        'unconfigured' => 'el mailer no está configurado, así que enviar provoca un error',
        'log' => 'el transporte log escribe cada mensaje en el log y no envía ninguno',
        'array' => 'el transporte array guarda cada mensaje en memoria y no envía ninguno',
        'catcher' => 'el host SMTP es un capturador de correo, donde los mensajes se quedan y ningún buzón los recibe',
        'local' => 'el host SMTP es la propia máquina: un relay válido, o un ajuste de desarrollo que nadie cambió',
    ],

    'health-notifications' => [
        'label' => 'Un segundo camino para las alertas',
        'pass' => 'Una comprobación fallida te llega por un canal además del correo.',
        'pulled' => 'Oh Dear recoge los resultados, con independencia del correo de la aplicación.',
        'pulled_own' => 'Un monitor recoge los resultados en :endpoint, con independencia del correo de la aplicación.',
        'warn' => 'Una comprobación fallida en operación no te llegaría si el correo dejara de funcionar.',
        'off' => 'las notificaciones están desactivadas, y nada recoge los resultados',
        'mail_only' => 'una comprobación fallida solo se entrega por correo, y cuando el correo deja de funcionar, el mensaje sobre ello es justo el que no llega. Añade un webhook de Slack o el endpoint de Oh Dear, o nombra en production-preflight.health_endpoint un endpoint propio que recoja un monitor.',
        'none' => 'ningún canal entrega una comprobación fallida',
    ],

    'test-mode-keys' => [
        'label' => 'Credenciales en modo de prueba',
        'pass' => 'Ninguna credencial de la configuración está en modo de prueba.',
        'staging' => 'Nada que comprobar: un servidor de staging cobra con claves de prueba, y una clave real movería allí dinero de verdad.',
        'fail' => 'Ajustes en modo de prueba: :count. No se mueve dinero real.',
        'stripe' => 'una clave de Stripe en modo de prueba',
        'mollie' => 'una clave de Mollie en modo de prueba',
        'sandbox' => 'el modo sandbox está activado',
    ],

    'queue-connection' => [
        'label' => 'Conexión de cola',
        'pass' => 'La conexión de cola predeterminada entrega los trabajos a workers.',
        'fail' => 'La conexión de cola predeterminada no ejecuta ningún trabajo.',
        'warn' => 'La conexión de cola predeterminada ejecuta cada trabajo dentro de la petición.',
        'unconfigured' => 'la conexión predeterminada no está configurada, así que encolar un trabajo provoca un error',
        'null' => 'el driver null descarta cada trabajo que recibe',
        'sync' => 'los trabajos se ejecutan dentro de la petición que los encola, así que cada respuesta los espera. Una elección para una instalación pequeña: asúmelo si es la tuya.',
    ],

    'session-driver' => [
        'label' => 'Driver de sesión',
        'pass' => 'Las sesiones sobreviven a la petición.',
        'fail' => 'Las sesiones duran una petición.',
        'array' => 'el driver array olvida cada sesión tras la petición, así que cada usuario queda desconectado tras cada página',
    ],

    'cache-store' => [
        'label' => 'Almacén de caché',
        'pass' => 'El almacén de caché predeterminado conserva lo que recibe.',
        'fail' => 'El almacén de caché predeterminado no conserva nada más allá de la petición.',
        'unconfigured' => 'el almacén predeterminado no está configurado, así que el primer acceso a la caché provoca un error',
        'transient' => 'los limitadores de peticiones, los bloqueos atómicos y la señal de reinicio de los workers viven en la caché y desaparecen tras cada petición',
    ],

    'secure-cookie' => [
        'label' => 'Cookie de sesión segura',
        'pass' => 'La cookie de sesión lleva el flag Secure.',
        'fail' => 'A la cookie de sesión le falta el flag Secure en un sitio que funciona con HTTPS.',
        'finding' => 'un navegador también envía la cookie por HTTP sin cifrar, donde cualquiera en la red puede leerla. Define SESSION_SECURE_COOKIE=true.',
        'http' => 'app.url indica http, donde una cookie Secure ni siquiera se enviaría.',
        'no_scheme' => 'Sin comprobar: app.url no indica ningún esquema, así que no se sabe si el sitio funciona con HTTPS.',
    ],

    'debug-tools' => [
        'label' => 'Herramientas de depuración',
        'pass' => 'No hay ninguna herramienta de depuración cargada.',
        'fail' => 'Una herramienta de depuración está abierta en producción.',
        'warn' => 'Una herramienta de depuración está cargada y en reposo.',
        'telescope' => 'Telescope registra peticiones, consultas y correos, y su panel está a un gate del público',
        'debugbar' => 'la barra de depuración muestra a cada visitante las consultas, la sesión y la petición de cada página',
        'ignition' => 'en reposo mientras el modo de depuración está desactivado, y una vista abierta de la aplicación en cuanto se activa',
    ],

    'log-level' => [
        'label' => 'Nivel de log',
        'pass' => 'El canal de log predeterminado no registra en nivel debug.',
        'warn' => 'El canal de log predeterminado registra en nivel debug.',
        'unconfigured' => 'Sin comprobar: el canal de log predeterminado no está configurado.',
        'debug' => 'cada detalle acaba en el disco, que se llena a un ritmo calculable',
    ],

    'config-cache-fresh' => [
        'label' => 'Configuración en caché',
        'pass' => 'La configuración en caché es más reciente que .env.',
        'fail' => 'La aplicación funciona con una configuración anterior al último cambio en .env.',
        'finding' => '.env cambió después de que la configuración se guardara en caché, así que sus nuevos valores no se aplican. Ejecuta php artisan config:cache.',
        'not_cached' => 'La configuración no está en caché, así que cada petición lee .env y un cambio se aplica al instante.',
        'no_env' => 'La configuración en caché es toda la configuración, y no hay ningún .env al lado.',
    ],

    'queue-restart' => [
        'label' => 'Workers de cola tras el despliegue',
        'pass' => 'Se pidió a los workers de cola que se reiniciaran tras el último despliegue.',
        'fail' => 'Los workers de cola ejecutan el código de antes del último despliegue.',
        'never' => 'nunca se envió una señal de reinicio, así que los workers ejecutan el código con el que arrancaron. Añade php artisan queue:restart al final del script de despliegue.',
        'stale' => 'la última señal de reinicio es anterior al último despliegue. Añade php artisan queue:restart al final del script de despliegue.',
        'no_worker' => 'La conexión de cola predeterminada no tiene ningún worker que reiniciar.',
        'no_deploy' => 'Sin comprobar: no hay ningún despliegue registrado. Añade php artisan preflight --deploy al script de despliegue, antes de php artisan queue:restart.',
    ],

    'pending-migrations' => [
        'label' => 'Migraciones pendientes',
        'pass' => 'Todas las migraciones se han ejecutado.',
        'fail' => 'Migraciones que no se han ejecutado: :count. El código es nuevo, el esquema no.',
        'pending' => 'no se ha ejecutado',
        'no_migrator' => 'Sin comprobar: la aplicación no tiene migrador.',
    ],

    'writable-directories' => [
        'label' => 'Directorios con escritura',
        'pass' => 'Todos los directorios en los que escribe Laravel admiten escritura.',
        'fail' => 'Rutas en las que Laravel no puede escribir en :server: :count. La primera acción de usuario que escribe falla.',
        'missing' => 'el directorio falta',
        'read_only' => 'el directorio no admite escritura para el usuario que ejecuta esta comprobación',
        'log_read_only' => 'el archivo de registro más reciente no admite escritura para el usuario que ejecuta esta comprobación, y una línea que registre se pierde',
    ],

    'storage-link' => [
        'label' => 'Enlace de storage',
        'pass' => 'Cada enlace de storage configurado apunta a donde debe.',
        'fail' => 'Los archivos del disco público responden 404.',
        'warn' => 'El próximo archivo guardado en el disco público responderá 404.',
        'elsewhere' => 'el enlace apunta a otro sitio',
        'directory' => 'donde debería estar el enlace hay un directorio real, y un archivo guardado después nunca aparece en él',
        'missing_with_files' => 'el enlace falta, y su destino ya contiene archivos. Ejecuta php artisan storage:link.',
        'missing' => 'el enlace falta. Ejecuta php artisan storage:link.',
    ],

    'env-exposure' => [
        'label' => 'Secretos en el sistema de archivos',
        'pass' => '.env no está abierto a todos los usuarios del servidor, y la raíz web no contiene ni .env ni .git.',
        'fail' => 'Archivos que entregan secretos: :count.',
        'world_readable' => 'cualquier usuario del servidor puede leerlo',
        'world_writable' => 'cualquier usuario del servidor puede escribirlo',
        'web_root' => 'está en la raíz web, donde el servidor web puede servirlo',
    ],

    'env-over-http' => [
        'label' => 'Secretos por HTTP',
        'pass' => 'El sitio no sirve ni .env ni .git/config.',
        'fail' => 'El sitio sirve un archivo secreto a cualquiera que lo pida.',
        'served' => 'el sitio responde 200 con el contenido del archivo',
        'off' => 'Sin comprobar: saber si el sitio sirve .env requiere una petición al sitio, y la sonda está desactivada. Actívala con PRODUCTION_PREFLIGHT_HTTP_PROBE=true.',
        'no_url' => 'Sin comprobar: app.url no es una dirección http o https a la que se pueda preguntar.',
        'unanswered' => 'Sin comprobar: el sitio no respondió.',
    ],

    'dev-dependencies' => [
        'label' => 'Dependencias de desarrollo',
        'pass' => 'No hay ningún paquete de desarrollo instalado.',
        'fail' => 'Paquetes de desarrollo instalados: :count.',
        'finding' => 'hay instalados ejecutores de tests, depuradores y generadores de código. Instala con composer install --no-dev.',
        'unreadable' => 'Sin comprobar: no se puede leer :path.',
    ],

    'optimized-autoloader' => [
        'label' => 'Autoloader optimizado',
        'pass' => 'El autoloader está optimizado.',
        'warn' => 'El autoloader busca cada clase en el disco.',
        'finding' => 'instala con composer install --optimize-autoloader',
        'unreadable' => 'Sin comprobar: no se puede leer composer.json o el mapa de clases del autoloader.',
    ],

    'php-version-parity' => [
        'label' => 'Versión de PHP del servidor web',
        'pass' => 'La línea de comandos y el servidor web usan PHP :cli.',
        'fail' => 'La línea de comandos usa PHP :cli y el servidor web PHP :web.',
        'finding' => 'las migraciones y los comandos programados se ejecutan con PHP :cli, la aplicación con PHP :web',
        'unnamed' => 'Sin comprobar: el PHP del servidor web es un binario que esta comprobación ejecuta, y no se ha indicado ninguno. Define PRODUCTION_PREFLIGHT_PHP_FPM, por ejemplo como /usr/sbin/php-fpm8.4.',
        'no_processes' => 'Sin comprobar: este servidor no permite iniciar un proceso.',
        'unanswered' => 'Sin comprobar: el binario indicado no informó de ninguna versión de PHP.',
    ],

    'opcache' => [
        'label' => 'OPcache',
        'pass' => 'OPcache está activado y no comprueba si los archivos cambian.',
        'fail' => 'OPcache no funciona en el servidor web.',
        'warn' => 'OPcache comprueba si cada archivo ha cambiado.',
        'not_loaded' => 'la extensión OPcache no está cargada',
        'off' => 'OPcache está desactivado',
        'timestamps' => 'cada petición comprueba si los archivos han cambiado. Desactívalo y recarga PHP en cada despliegue.',
        'command_line' => 'Sin comprobar: la línea de comandos tiene su propia configuración de OPcache. La comprobación responde en una petición web, como el endpoint de laravel-health con resultados recientes.',
    ],

];
