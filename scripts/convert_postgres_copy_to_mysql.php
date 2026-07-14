<?php

declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "Uso: php scripts/convert_postgres_copy_to_mysql.php <dump-postgres.sql> <dados-mysql.sql>\n");
    exit(1);
}

[$script, $inputPath, $outputPath] = $argv;

if (! is_file($inputPath) || ! is_readable($inputPath)) {
    fwrite(STDERR, "O dump PostgreSQL nao pode ser lido: {$inputPath}\n");
    exit(1);
}

$tableOrder = [
    'users',
    'stores',
    'categories',
    'products',
    'category_product',
    'orders',
    'order_items',
    'plan_subscriptions',
    'asaas_webhook_events',
    'system_settings',
    'dismissed_notifications',
];

$booleanColumns = [
    'stores' => ['is_active', 'pix_enabled'],
    'products' => ['is_active', 'is_featured', 'track_stock'],
];

$selectedTables = array_fill_keys($tableOrder, true);
$rowsByTable = [];
$columnsByTable = [];

$handle = fopen($inputPath, 'rb');

if ($handle === false) {
    fwrite(STDERR, "Nao foi possivel abrir o dump PostgreSQL.\n");
    exit(1);
}

$currentTable = null;
$currentColumns = [];

while (($line = fgets($handle)) !== false) {
    $line = rtrim($line, "\r\n");

    if ($currentTable === null) {
        if (preg_match('/^COPY public\.([a-z0-9_]+) \((.+)\) FROM stdin;$/', $line, $matches) !== 1) {
            continue;
        }

        $table = $matches[1];
        $columns = array_map('trim', explode(',', $matches[2]));

        $currentTable = isset($selectedTables[$table]) ? $table : '__skip__';
        $currentColumns = $columns;

        if ($currentTable !== '__skip__') {
            $columnsByTable[$currentTable] = $columns;
            $rowsByTable[$currentTable] ??= [];
        }

        continue;
    }

    if ($line === '\\.') {
        $currentTable = null;
        $currentColumns = [];
        continue;
    }

    if ($currentTable === '__skip__') {
        continue;
    }

    $rawValues = explode("\t", $line);

    if (count($rawValues) !== count($currentColumns)) {
        fclose($handle);
        fwrite(STDERR, "Quantidade de colunas invalida na tabela {$currentTable}.\n");
        exit(1);
    }

    $row = [];

    foreach ($rawValues as $index => $rawValue) {
        $column = $currentColumns[$index];
        $value = decodePostgresCopyValue($rawValue);

        if ($value !== null && in_array($column, $booleanColumns[$currentTable] ?? [], true)) {
            $value = match ($value) {
                't' => '1',
                'f' => '0',
                default => $value,
            };
        }

        $row[] = $value;
    }

    $rowsByTable[$currentTable][] = $row;
}

fclose($handle);

$output = fopen($outputPath, 'wb');

if ($output === false) {
    fwrite(STDERR, "Nao foi possivel criar o SQL MySQL: {$outputPath}\n");
    exit(1);
}

fwrite($output, "-- Dados do Shopla convertidos de PostgreSQL COPY para MySQL\n");
fwrite($output, "-- A estrutura deve ser criada antes com as migrations do Laravel.\n\n");
fwrite($output, "SET NAMES utf8mb4;\n");
fwrite($output, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($output, "START TRANSACTION;\n\n");

foreach (array_reverse($tableOrder) as $table) {
    fwrite($output, "DELETE FROM `{$table}`;\n");
}

fwrite($output, "\n");

foreach ($tableOrder as $table) {
    $columns = $columnsByTable[$table] ?? [];
    $rows = $rowsByTable[$table] ?? [];

    fwrite($output, sprintf("-- %s: %d registro(s)\n", $table, count($rows)));

    if ($columns === [] || $rows === []) {
        fwrite($output, "\n");
        continue;
    }

    $columnSql = implode(', ', array_map(
        static fn (string $column): string => '`'.str_replace('`', '``', $column).'`',
        $columns,
    ));

    foreach (array_chunk($rows, 100) as $chunk) {
        fwrite($output, "INSERT INTO `{$table}` ({$columnSql}) VALUES\n");

        $valueRows = array_map(
            static fn (array $row): string => '('.implode(', ', array_map('mysqlLiteral', $row)).')',
            $chunk,
        );

        fwrite($output, implode(",\n", $valueRows).";\n");
    }

    fwrite($output, "\n");
}

fwrite($output, "COMMIT;\n");
fwrite($output, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($output);

foreach ($tableOrder as $table) {
    printf("%-28s %d\n", $table, count($rowsByTable[$table] ?? []));
}

echo "SQL MySQL criado em: {$outputPath}\n";

function decodePostgresCopyValue(string $value): ?string
{
    if ($value === '\\N') {
        return null;
    }

    return (string) preg_replace_callback(
        '/\\\\(?:x[0-9A-Fa-f]{1,2}|[0-7]{1,3}|.)/s',
        static function (array $matches): string {
            $escape = substr($matches[0], 1);

            if ($escape !== '' && $escape[0] === 'x') {
                return chr((int) hexdec(substr($escape, 1)));
            }

            if (preg_match('/^[0-7]{1,3}$/', $escape) === 1) {
                return chr((int) octdec($escape));
            }

            return match ($escape) {
                'b' => "\x08",
                'f' => "\x0C",
                'n' => "\n",
                'r' => "\r",
                't' => "\t",
                'v' => "\x0B",
                '\\' => '\\',
                default => $escape,
            };
        },
        $value,
    );
}

function mysqlLiteral(?string $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    $escaped = strtr($value, [
        "\\" => "\\\\",
        "\0" => "\\0",
        "\n" => "\\n",
        "\r" => "\\r",
        "\x1a" => "\\Z",
        "'" => "\\'",
    ]);

    return "'{$escaped}'";
}
