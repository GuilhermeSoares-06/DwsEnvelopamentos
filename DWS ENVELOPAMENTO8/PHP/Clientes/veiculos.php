<?php
// =============================================
// veiculos.php - Retorna os veículos ativos (tabela única `veiculos`)
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

$tipo = trim($_GET['tipo'] ?? '');
$tiposValidos = ['carro', 'moto', 'caminhao', 'aquatico', 'mobilia'];

try {
    $sql = "SELECT vecid, vectipo, vecmarca, vecmodelo FROM veiculos WHERE vecativo = 1";
    $params = [];

    if (in_array($tipo, $tiposValidos, true)) {
        $sql .= " AND vectipo = :tipo";
        $params[':tipo'] = $tipo;
    }

    $sql .= " ORDER BY FIELD(vectipo, 'carro', 'moto', 'caminhao', 'aquatico', 'mobilia'), vecmarca, vecmodelo";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $veiculos = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $veiculos[] = [
            'id'     => (int)$row['vecid'],
            'tipo'   => $row['vectipo'],
            'modelo' => $row['vecmodelo'],
            'marca'  => $row['vecmarca'] ?? ''
        ];
    }

    responderSucesso('Veículos carregados', [
        'veiculos' => $veiculos,
        'total'    => count($veiculos),
        'filtro'   => in_array($tipo, $tiposValidos, true) ? $tipo : 'todos'
    ]);

} catch (PDOException $e) {
    error_log("Erro veiculos.php: " . $e->getMessage());
    responderErro('Erro ao buscar veículos.', 500);
}
