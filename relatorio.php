<?php

header('Content-Type: text/csv; charset=utf-8');

$input = file_get_contents('php://input');

if (!$input) {
    http_response_code(400);
    echo "Body vazio.";
    exit;
}

$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo "JSON inválido.";
    exit;
}

if (!isset($data['titulo']) || !isset($data['dados'])) {
    http_response_code(400);
    echo "JSON deve conter 'titulo' e 'dados'.";
    exit;
}

$titulo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['titulo']);
$dados = $data['dados'];
$headerRelatorio = $data['header'] ?? $data['cabecalho'] ?? null;

if (empty($dados) || !is_array($dados)) {
    http_response_code(400);
    echo "Nenhum dado encontrado.";
    exit;
}

$nomeArquivo = $titulo . '.csv';

header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header opcional do relatorio
if (is_array($headerRelatorio)) {
    $camposHeader = [
        'titulo_empresa',
        'periodo_relatorio',
        'id',
        'titulo_relatorio',
    ];

    $temHeader = false;

    foreach ($camposHeader as $campo) {
        if (array_key_exists($campo, $headerRelatorio) && $headerRelatorio[$campo] !== '' && $headerRelatorio[$campo] !== null) {
            $temHeader = true;
            break;
        }
    }

    if ($temHeader) {
        foreach ($camposHeader as $campo) {
            if (array_key_exists($campo, $headerRelatorio) && $headerRelatorio[$campo] !== '' && $headerRelatorio[$campo] !== null) {
                fputcsv($output, [str_replace('_', ' ', $campo), $headerRelatorio[$campo]], ';');
            }
        }

        fputcsv($output, [], ';');
    }
}

// Cabeçalho
$cabecalho = array_keys($dados[0]);
fputcsv($output, $cabecalho, ';');

// Linhas
foreach ($dados as $linha) {
    fputcsv($output, $linha, ';');
}

fclose($output);
exit;