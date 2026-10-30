<?php

declare(strict_types=1);

namespace Pushery\ProductionPreflight\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Pushery\ProductionPreflight\Env\EnvFile;
use Pushery\ProductionPreflight\Preflight;
use Pushery\ProductionPreflight\Scope;

/**
 * Lint a `.env` file as text, with every check of the file scope.
 *
 * Strict where `preflight --deploy` is forgiving: a warning is a finding here too. Equal values on
 * two lines are harmless today and one edit away from a setting that does not apply, and a linter
 * that let the one case through would soon let the other.
 *
 * A file that cannot be read exits with 2, never 0. Whoever hangs this command into a deploy relies
 * on a green exit meaning something, and "nothing found" and "not looked" must not read alike.
 */
final class EnvLintCommand extends Command
{
    use RunsChecks;

    /**
     * @var string
     */
    protected $signature = 'env:lint
        {file? : The file to lint, by default the .env of this installation}
        {--quiet-when-clean : Print nothing when nothing was found, for a deploy or a schedule}
        {--show-values : Print the values behind the findings; never in a deploy or a scheduled run, whose output is kept}';

    /**
     * @var string
     */
    protected $description = 'Lint a .env file: keys set twice, keys of the example file, placeholders, a cut-off # and bytes that do not show (exit 1 on a finding, 2 when nothing could be checked)';

    public function handle(Repository $config): int
    {
        $file = $this->argument('file');

        if (is_string($file) && $file !== '') {
            $config->set('production-preflight.env.file', $file);
        }

        $configured = $config->get('production-preflight.env.file');
        $path = is_string($configured) && $configured !== '' ? $configured : $this->laravel->basePath('.env');

        if (! EnvFile::read($path) instanceof EnvFile) {
            $this->error($this->text('command.unreadable', ['path' => $path]));
            $this->line($this->text('command.unreadable_note'));
            $this->announce('env:lint', [], 2);

            return 2;
        }

        $done = $this->inspect(Preflight::checks(Scope::File));
        $exitCode = $this->exitCode($done, true);

        if ($exitCode !== 0 || $this->option('quiet-when-clean') !== true) {
            $this->line($this->text('command.file', ['path' => $path]));

            foreach ($done as $entry) {
                $this->report($entry['check'], $entry['inspection'], $this->option('show-values') === true);
            }
        }

        $this->announce('env:lint', $done, $exitCode);

        return $exitCode;
    }
}
