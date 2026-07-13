<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function (QueryExecuted $query): void {
            $output = new ConsoleOutput(decorated: true);

            $sql = $this->formatSql($query->toRawSql());
            $type = strtoupper(strtok(ltrim($sql), " \n\t"));
            $color = $this->getSqlColor($type);
            $timeColor = $query->time >= 500 ? 'red' : ($query->time >= 100 ? 'yellow' : 'cyan');

            $output->writeln(
                sprintf(
                    '<fg=cyan;options=bold>SQL</> <fg=%s;options=bold>%-7s</> <fg=%s;options=bold>(%.2f ms)</>',
                    $color,
                    $type,
                    $timeColor,
                    $query->time,
                ),
            );

            foreach (explode("\n", $this->highlightSql($sql)) as $line) {
                $output->writeln('  '.$line);
            }
        });

        Scramble::configure()->withDocumentTransformers(function (OpenApi $openApi): void {
            $openApi->secure(SecurityScheme::http('bearer'));
        });
    }

    private function getSqlColor(string $type): string
    {
        return match ($type) {
            'SELECT' => 'green',
            'INSERT' => 'blue',
            'UPDATE' => 'yellow',
            'DELETE' => 'red',
            default => 'white',
        };
    }

    private function formatSql(string $sql): string
    {
        $sql = preg_replace('/\s+/', ' ', trim($sql)) ?: trim($sql);
        $sql = $this->normalizeSql($sql);

        $sql = preg_replace(
            '/\b(FROM|WHERE|INNER JOIN|LEFT JOIN|RIGHT JOIN|JOIN|VALUES|SET|GROUP BY|ORDER BY|HAVING|LIMIT|OFFSET|RETURNING)\b/',
            "\n$1",
            $sql,
        ) ?: $sql;

        $sql = preg_replace('/\b(AND|OR)\b/', "\n  $1", $sql) ?: $sql;

        return trim($sql);
    }

    private function normalizeSql(string $sql): string
    {
        $parts = preg_split("/('(?:''|[^'])*')/", $sql, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return $sql;
        }

        foreach ($parts as $index => $part) {
            if ($index % 2 === 1) {
                continue;
            }

            $part = preg_replace('/["`]([a-zA-Z_][a-zA-Z0-9_]*)["`]/', '$1', $part) ?: $part;
            $part = preg_replace_callback(
                '/\b(group\s+by|order\s+by|inner\s+join|left\s+join|right\s+join|select|from|where|insert|into|values|update|set|delete|join|on|and|or|having|limit|offset|count|as|null|returning)\b/i',
                fn (array $matches): string => strtoupper(preg_replace('/\s+/', ' ', $matches[1]) ?: $matches[1]),
                $part,
            ) ?: $part;

            $parts[$index] = $part;
        }

        return implode('', $parts);
    }

    private function highlightSql(string $sql): string
    {
        $sql = OutputFormatter::escape($sql);

        $sql = preg_replace(
            "/'([^']|'')*'/",
            '<fg=green>$0</>',
            $sql,
        ) ?: $sql;

        $sql = preg_replace(
            '/\b(select|from|where|insert|into|values|update|set|delete|join|inner|left|right|on|and|or|group by|order by|having|limit|offset|count|as|null|returning)\b/i',
            '<fg=yellow;options=bold>$0</>',
            $sql,
        ) ?: $sql;

        return preg_replace(
            '/(?<![\w.])(\d+(?:\.\d+)?)(?![\w.])/',
            '<fg=magenta>$1</>',
            $sql,
        ) ?: $sql;
    }
}
