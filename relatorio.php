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
    $linhasHeader = [
        'Empresa: ' . ($headerRelatorio['titulo_empresa'] ?? ''),
        'Período: ' . ($headerRelatorio['periodo_relatorio'] ?? ''),
        'id: ' . ($headerRelatorio['id'] ?? ''),
        $headerRelatorio['titulo_relatorio'] ?? '',
    ];

    $temHeader = false;

    foreach ($linhasHeader as $linhaHeader) {
        if ($linhaHeader !== '' && $linhaHeader !== null) {
            $temHeader = true;
            break;
        }
    }

    if ($temHeader) {
        foreach ($linhasHeader as $linhaHeader) {
            if ($linhaHeader === '' || $linhaHeader === null) {
                continue;
            }

            fputcsv($output, [$linhaHeader], ';');
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