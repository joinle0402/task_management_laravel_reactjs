<?php

namespace App\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Events\QueryExecuted;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;

class SqlQueryLogger
{
    private static bool $muted = false;

    private static ?int $requestId = null;

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
        $meta = $this->formatMeta($query, $type);

        $this->writeFirstQuerySpacing();

        if (count($lines) === 1) {
            $this->output->writeln(sprintf(
                '<fg=cyan;options=bold>Query:</> %s <fg=gray>|</> %s',
                $this->highlightSql($lines[0], $color),
                $meta,
            ));
            $this->writeQuerySpacing(2);

            return;
        }

        $this->output->writeln('<fg=cyan;options=bold>Query:</>');

        $lastIndex = array_key_last($lines);

        foreach ($lines as $index => $line) {
            $suffix = $index === $lastIndex ? ' <fg=gray>|</> '.$meta : '';

            $this->output->writeln($this->highlightSqlLine($line, $color).$suffix);
        }

        $this->writeQuerySpacing(2);
    }

    private function writeFirstQuerySpacing(): void
    {
        if (! app()->bound('request')) {
            return;
        }

        $requestId = spl_object_id(app('request'));

        if (self::$requestId === $requestId) {
            return;
        }

        self::$requestId = $requestId;
        $this->writeQuerySpacing(2);
    }

    private function writeQuerySpacing(int $lines): void
    {
        for ($line = 0; $line < $lines; $line++) {
            $this->output->writeln('<fg=gray> </>');
        }
    }

    private function formatMeta(QueryExecuted $query, string $type): string
    {
        $parts = [
            sprintf(
                '<fg=%s;options=bold>%.2f ms</>',
                $this->getTimeColor($query->time),
                $query->time,
            ),
        ];

        if (in_array($type, ['INSERT', 'UPDATE', 'DELETE'], true)) {
            $affectedRows = $this->getAffectedRows($query->connection);

            if ($affectedRows !== null) {
                $parts[] = sprintf('<fg=white;options=bold>%d rows</>', $affectedRows);
            }
        }

        return implode(' <fg=gray>|</> ', $parts);
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
        if ($this->getQueryType($sql) === 'CREATE') {
            return $this->formatCreateSqlLines($sql);
        }

        if ($this->getQueryType($sql) === 'INSERT') {
            return $this->formatInsertSqlLines($sql);
        }

        if (preg_match('/^SELECT\s+EXISTS\s*\(/i', $sql)) {
            return $this->formatExistsSqlLines($sql);
        }

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

    private function formatCreateSqlLines(string $sql): array
    {
        if (! preg_match('/^CREATE\s+((?:TEMP|TEMPORARY)\s+)?TABLE\s+(.+?)\s*\((.*)\)$/i', $sql, $matches)) {
            return [$sql];
        }

        $definitions = $this->splitTopLevelComma($matches[3]);

        if ($definitions === []) {
            return [$sql];
        }

        $lines = [
            'CREATE '.($matches[1] ?? '').'TABLE '.$matches[2].' (',
        ];

        foreach ($definitions as $index => $definition) {
            $suffix = $index === array_key_last($definitions) ? '' : ',';
            $lines[] = '    '.$definition.$suffix;
        }

        $lines[] = ')';

        return $lines;
    }

    private function formatExistsSqlLines(string $sql): array
    {
        if (! preg_match('/^SELECT\s+EXISTS\s*\((.*)\)\s+AS\s+(.+)$/i', $sql, $matches)) {
            return [$sql];
        }

        $innerSql = trim($matches[1]);
        $innerSql = preg_replace(
            '/\b(FROM|WHERE|INNER JOIN|LEFT JOIN|RIGHT JOIN|JOIN|GROUP BY|ORDER BY|HAVING|LIMIT|OFFSET)\b/',
            "\n$1",
            $innerSql,
        ) ?: $innerSql;
        $innerSql = preg_replace('/\b(AND|OR)\b/', "\n  $1", $innerSql) ?: $innerSql;

        $lines = ['SELECT EXISTS ('];

        foreach (explode("\n", trim($innerSql)) as $line) {
            $lines[] = '    '.rtrim($line);
        }

        $alias = $matches[2] === 'EXISTS' ? 'exists' : $matches[2];
        $lines[] = ') AS '.$alias;

        return $lines;
    }

    private function formatInsertSqlLines(string $sql): array
    {
        if (! preg_match('/^(.*?)\s+VALUES\s+(.+)$/', $sql, $matches)) {
            return [$sql];
        }

        $records = $this->splitTopLevelComma($matches[2], true);

        if ($records === []) {
            return [$sql];
        }

        $lines = [
            trim($matches[1]),
            'VALUES '.$records[0],
        ];

        foreach (array_slice($records, 1) as $record) {
            $lines[] = '       '.$record;
        }

        return $lines;
    }

    private function splitTopLevelComma(string $value, bool $keepComma = false): array
    {
        $items = [];
        $item = '';
        $depth = 0;
        $inString = false;
        $length = strlen($value);

        for ($index = 0; $index < $length; $index++) {
            $char = $value[$index];
            $nextChar = $value[$index + 1] ?? null;

            if ($char === "'") {
                $item .= $char;

                if ($inString && $nextChar === "'") {
                    $item .= $nextChar;
                    $index++;

                    continue;
                }

                $inString = ! $inString;

                continue;
            }

            if (! $inString) {
                if ($char === '(') {
                    $depth++;
                } elseif ($char === ')') {
                    $depth--;
                } elseif ($char === ',' && $depth === 0) {
                    $items[] = trim($item).($keepComma ? ',' : '');
                    $item = '';

                    continue;
                }
            }

            $item .= $char;
        }

        $item = trim($item);

        if ($item !== '') {
            $items[] = $item;
        }

        return $items;
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
                '/\b(group\s+by|order\s+by|inner\s+join|left\s+join|right\s+join|primary\s+key|foreign\s+key|not\s+null|on\s+delete|create|temporary|temp|table|unique|index|select|from|where|insert|into|values|update|set|delete|join|references|cascade|exists|on|and|or|having|limit|offset|count|as|null|returning|integer|datetime|text|autoincrement)\b/i',
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
                '/\b(PRIMARY KEY|FOREIGN KEY|NOT NULL|ON DELETE|GROUP BY|ORDER BY|INNER JOIN|LEFT JOIN|RIGHT JOIN|CREATE|TEMPORARY|TEMP|TABLE|UNIQUE|INDEX|SELECT|FROM|WHERE|INSERT|INTO|VALUES|UPDATE|SET|DELETE|JOIN|INNER|LEFT|RIGHT|ON|AND|OR|HAVING|LIMIT|OFFSET|COUNT|AS|NULL|RETURNING|EXISTS|REFERENCES|CASCADE)\b/',
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

    private function highlightSqlLine(string $sql, string $keywordColor): string
    {
        preg_match('/^\s*/', $sql, $matches);

        $indent = $matches[0] ?? '';

        if ($indent === '') {
            return $this->highlightSql($sql, $keywordColor);
        }

        return "\033[0m".$indent.$this->highlightSql(ltrim($sql), $keywordColor);
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
