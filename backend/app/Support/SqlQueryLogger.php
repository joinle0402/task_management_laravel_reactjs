<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Events\QueryExecuted;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;

class SqlQueryLogger
{
    private static bool $muted = false;

    private ConsoleOutput $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput(decorated: true);
    }

    public function handle(QueryExecuted $query): void
    {
        if (self::$muted) {
            return;
        }

        $sql = $this->normalizeSql($query->toRawSql());
        $type = $this->getQueryType($sql);
        $color = $this->getQueryColor($type);
        $lines = $this->formatSqlLines($sql);
        $lastIndex = array_key_last($lines);
        $meta = $this->formatMeta($query, $type);

        $this->output->writeln('');
        $this->output->writeln(sprintf(
            '<fg=cyan;options=bold>SQL</> <fg=%s;options=bold>%s</>',
            $color,
            $type,
        ));

        foreach ($lines as $index => $line) {
            $suffix = $index === $lastIndex ? '  '.$meta : '';

            $this->output->writeln('  '.$this->highlightSql($line, $color).$suffix);
        }
    }

    private function formatMeta(QueryExecuted $query, string $type): string
    {
        $parts = [];

        if (in_array($type, ['UPDATE', 'DELETE'], true)) {
            $affectedRows = $this->getAffectedRows($query->connection);

            if ($affectedRows !== null) {
                $parts[] = sprintf('<fg=white;options=bold>affected: %d</>', $affectedRows);
            }
        }

        $parts[] = sprintf(
            '<fg=%s;options=bold>%.2f ms</>',
            $this->getTimeColor($query->time),
            $query->time,
        );

        return '<fg=gray>|</> '.implode(' <fg=gray>|</> ', $parts);
    }

    private function getAffectedRows(ConnectionInterface $connection): ?int
    {
        $driver = $connection->getDriverName();
        $sql = match ($driver) {
            'sqlite' => 'SELECT changes() AS affected',
            'mysql', 'mariadb' => 'SELECT ROW_COUNT() AS affected',
            'sqlsrv' => 'SELECT @@ROWCOUNT AS affected',
            default => null,
        };

        if ($sql === null) {
            return null;
        }

        self::$muted = true;

        try {
            $row = $connection->selectOne($sql);

            return isset($row->affected) ? (int) $row->affected : null;
        } finally {
            self::$muted = false;
        }
    }

    private function formatSqlLines(string $sql): array
    {
        if ($this->isSimpleQuery($sql)) {
            return [$sql];
        }

        $sql = preg_replace(
            '/\b(FROM|WHERE|INNER JOIN|LEFT JOIN|RIGHT JOIN|JOIN|VALUES|SET|GROUP BY|ORDER BY|HAVING|LIMIT|OFFSET|RETURNING)\b/',
            "\n$1",
            $sql,
        ) ?: $sql;

        $sql = preg_replace('/\b(AND|OR)\b/', "\n  $1", $sql) ?: $sql;

        return array_values(array_filter(explode("\n", trim($sql)), fn (string $line): bool => trim($line) !== ''));
    }

    private function isSimpleQuery(string $sql): bool
    {
        return ! preg_match('/\bJOIN\b|\(\s*SELECT\b/i', $sql);
    }

    private function normalizeSql(string $sql): string
    {
        $sql = preg_replace('/\s+/', ' ', trim($sql)) ?: trim($sql);
        $parts = $this->splitSqlByStrings($sql);

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

    private function highlightSql(string $sql, string $keywordColor): string
    {
        $parts = $this->splitSqlByStrings($sql);

        foreach ($parts as $index => $part) {
            $part = OutputFormatter::escape($part);

            if ($index % 2 === 1) {
                $parts[$index] = '<fg=green>'.$part.'</>';

                continue;
            }

            $part = preg_replace(
                '/\b(SELECT|FROM|WHERE|INSERT|INTO|VALUES|UPDATE|SET|DELETE|JOIN|INNER|LEFT|RIGHT|ON|AND|OR|GROUP BY|ORDER BY|HAVING|LIMIT|OFFSET|COUNT|AS|NULL|RETURNING)\b/',
                '<fg='.$keywordColor.';options=bold>$1</>',
                $part,
            ) ?: $part;

            $part = preg_replace(
                '/(?<![\w.])(\d+(?:\.\d+)?)(?![\w.])/',
                '<fg=magenta>$1</>',
                $part,
            ) ?: $part;

            $parts[$index] = $part;
        }

        return implode('', $parts);
    }

    private function splitSqlByStrings(string $sql): array
    {
        return preg_split("/('(?:''|[^'])*')/", $sql, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [$sql];
    }

    private function getQueryType(string $sql): string
    {
        return strtoupper(strtok(ltrim($sql), " \n\t") ?: 'QUERY');
    }

    private function getQueryColor(string $type): string
    {
        return match ($type) {
            'SELECT' => 'green',
            'INSERT' => 'blue',
            'UPDATE' => 'magenta',
            'DELETE' => 'red',
            default => 'white',
        };
    }

    private function getTimeColor(float $time): string
    {
        return match (true) {
            $time >= 500 => 'red',
            $time >= 100 => 'yellow',
            default => 'cyan',
        };
    }
}
