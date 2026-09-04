<?php
/**
 * dadosDashboard.php
 * Retorna em JSON os 5 serviços e os 5 clientes mais recentes,
 * usados no painel administrativo (principalFUN.html).
 *
 * IMPORTANTE:
 * Ajuste o caminho do require abaixo para apontar para o seu
 * arquivo de conexão com o banco (o que já é usado nos outros
 * arquivos da pasta /PHP, ex.: GerenciarServico.php,
 * ListarCliente.php). Se o seu projeto já usa PDO ou mysqli com
 * outro nome de variável, adapte as linhas marcadas abaixo.
 */

header('Content-Type: application/json; charset=utf-8');

// ------------------------------------------------------------------
// Conexão com o banco — ajuste conforme o padrão já usado no projeto
// ------------------------------------------------------------------
require_once __DIR__ . '../../telas/ADM/principalFUN.html'; // deve fornecer $pdo (PDO)

try {
    // 5 serviços mais recentes, com o nome do cliente
    $stmtServicos = $pdo->prepare("
        SELECT
            c.clinome,
            s.tipo_servico,
            s.servalor,
            s.serstatus_pagamento,
            s.serdata_servico
        FROM servicos s
        INNER JOIN clientes c ON c.cliid = s.cliid
        ORDER BY s.serdata_servico DESC
        LIMIT 5
    ");
    $stmtServicos->execute();
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);

    // 5 clientes mais recentes (apenas quem é cliente, não funcionário)
    $stmtClientes = $pdo->prepare("
        SELECT
            clinome,
            clitel,
            cliendereco
        FROM clientes
        WHERE tipocliente = 'cliente'
        ORDER BY cliid DESC
        LIMIT 5
    ");
    $stmtClientes->execute();
    $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'servicos' => $servicos,
        'clientes' => $clientes,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'erro' => 'Erro ao consultar o banco de dados.',
        // Remova a linha abaixo em produção — exposta aqui apenas para depuração
        'detalhe' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}