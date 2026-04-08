<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

/**
 * AuditRawQueries
 *
 * Scans the codebase for raw DB::select() / DB::statement() / DB::affectingStatement()
 * calls that appear to be missing a `saas_client_id` guard in the SQL string.
 *
 * This is a best-effort heuristic — it cannot guarantee correctness but catches
 * obvious omissions. Always review flagged results manually.
 *
 * Usage:
 *   php artisan citus:audit-raw-queries
 *   php artisan citus:audit-raw-queries --path=app/Filament
 *   php artisan citus:audit-raw-queries --json
 *
 * Phase 12 checklist item: "Audit all DB::select() raw queries"
 */
class AuditRawQueries extends Command
{
    protected $signature = 'citus:audit-raw-queries
                            {--path= : Relative path to scan (default: app)}
                            {--json  : Output results as JSON}
                            {--strict : Exit with failure code if any violations found}';

    protected $description = 'Scan for raw DB queries that may be missing saas_client_id guard (best-effort heuristic)';

    /**
     * Raw query patterns to look for.
     */
    private array $rawQueryPatterns = [
        'DB::select(',
        'DB::statement(',
        'DB::affectingStatement(',
        'DB::unprepared(',
        '->selectRaw(',
        '->whereRaw(',
        '->havingRaw(',
        '->orderByRaw(',
        '->fromRaw(',
        '->joinRaw(',
        '->groupByRaw(',
    ];

    /**
     * Patterns that indicate the query is likely already guarded.
     */
    private array $guardPatterns = [
        'saas_client_id',
        'current_tenant_id',
        'bypass_rls',
        'withoutTenantScope',
        'forTenant(',
        '-- superadmin',
        '// superadmin',
        'app.bypass_rls',
        'app.saas_client_id',
    ];

    /**
     * Directories to exclude from scanning.
     */
    private array $excludeDirs = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        '.git',
    ];

    public function handle(): int
    {
        $scanPath = base_path($this->option('path') ?? 'app');

        if (!is_dir($scanPath)) {
            $this->error("Directory does not exist: {$scanPath}");
            return self::FAILURE;
        }

        $this->info("==> Auditing raw queries in: {$scanPath}");
        $this->newLine();

        $violations = [];
        $warnings   = [];
        $scanned    = 0;

        $finder = new Finder();
        $finder->files()
            ->in($scanPath)
            ->name('*.php')
            ->notPath($this->excludeDirs);

        foreach ($finder as $file) {
            $scanned++;
            $content = $file->getContents();
            $lines   = explode("\n", $content);
            $relPath = ltrim(str_replace(base_path(), '', $file->getRealPath()), '/');

            foreach ($lines as $lineNum => $line) {
                $trimmed = trim($line);

                // Skip comments
                if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '#')) {
                    continue;
                }

                // Check if line contains a raw query pattern
                foreach ($this->rawQueryPatterns as $pattern) {
                    if (!str_contains($line, $pattern)) {
                        continue;
                    }

                    // Look at a window of lines around this one for the guard pattern
                    $windowStart = max(0, $lineNum - 5);
                    $windowEnd   = min(count($lines) - 1, $lineNum + 10);
                    $window      = implode("\n", array_slice($lines, $windowStart, $windowEnd - $windowStart + 1));

                    $hasGuard = false;
                    foreach ($this->guardPatterns as $guard) {
                        if (str_contains($window, $guard)) {
                            $hasGuard = true;
                            break;
                        }
                    }

                    $entry = [
                        'file'    => $relPath,
                        'line'    => $lineNum + 1,
                        'pattern' => $pattern,
                        'code'    => trim($line),
                    ];

                    if (!$hasGuard) {
                        $violations[] = $entry;
                    } else {
                        $warnings[] = $entry;
                    }
                }
            }
        }

        // ─── Output violations ─────────────────────────────────────────────────
        if (!empty($violations)) {
            $this->error('--- VIOLATIONS (raw queries missing saas_client_id guard) ---');
            foreach ($violations as $v) {
                $this->line("  <error>✗</error> <comment>{$v['file']}:{$v['line']}</comment>");
                $this->line("       Pattern : <info>{$v['pattern']}</info>");
                $this->line("       Code    : {$v['code']}");
                $this->newLine();
            }
        }

        // ─── Output guarded (informational) ───────────────────────────────────
        if (!empty($warnings)) {
            $this->info('--- GUARDED (raw queries with saas_client_id guard — review recommended) ---');
            foreach ($warnings as $w) {
                $this->line("  <info>✓</info> <comment>{$w['file']}:{$w['line']}</comment> [{$w['pattern']}]");
            }
            $this->newLine();
        }

        // ─── Summary ──────────────────────────────────────────────────────────
        $this->newLine();
        $this->info("==> Scan complete");
        $this->line("    Files scanned   : {$scanned}");
        $this->line("    Violations      : " . count($violations));
        $this->line("    Guarded (OK)    : " . count($warnings));
        $this->newLine();
        $this->warn("NOTE: This is a best-effort heuristic scan. False positives and false negatives are possible.");
        $this->warn("      Review all flagged files manually, especially dashboard aggregations and reporting queries.");

        if ($this->option('json')) {
            $this->newLine();
            $this->line(json_encode([
                'scanned'    => $scanned,
                'violations' => $violations,
                'guarded'    => $warnings,
            ], JSON_PRETTY_PRINT));
        }

        if ($this->option('strict') && !empty($violations)) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
