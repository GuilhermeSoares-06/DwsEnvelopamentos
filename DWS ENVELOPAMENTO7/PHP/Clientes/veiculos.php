<?php
// =============================================
// veiculos.php - Retorna TODOS os veículos
// =============================================
require_once __DIR__ . '/../bootstrap.php';

enviarHeadersSeguranca(true);

$tipo = trim($_GET['tipo'] ?? '');

// =============================================
// MAPA: tipo → {tabela, id, modelo, marca}
// =============================================
$mapa = [
    'carro' => [
        'tabela' => 'carros',
        'id'     => 'carid',
        'modelo' => 'carmodelo',
        'marca'  => 'carmarca'
    ],
    'moto' => [
        'tabela' => 'motos',
        'id'     => 'motid',
        'modelo' => 'motmodelo',
        'marca'  => 'motmarca'
    ],
    'caminhao' => [
        'tabela' => 'caminhoes',
        'id'     => 'camid',
        'modelo' => 'camelo',      // ⚠️ atenção: no banco é 'camelo'
        'marca'  => 'cammarca'
    ],
    'aquatico' => [
        'tabela' => 'nauticos',
        'id'     => 'nauid',
        'modelo' => 'naumodelo',
        'marca'  => 'naumarca'
    ],
    'mobilia' => [
        'tabela' => 'mobilia',
        'id'     => 'mobid',
        'modelo' => 'mobmedida',   // ⚠️ atenção: no banco é 'mobmedida'
        'marca'  => null           // mobilia não tem marca
    ]
];

// =============================================
// FUNÇÃO: busca de uma tabela específica
// =============================================
function buscarVeiculos(PDO $pdo, string $tipo, array $cfg): array {
    try {
        $sql = "SELECT {$cfg['id']} AS id, " .
               ($cfg['marca'] ? "{$cfg['marca']} AS marca, " : "NULL AS marca, ") .
               "{$cfg['modelo']} AS modelo " .
               "FROM {$cfg['tabela']} " .
               "ORDER BY " . ($cfg['marca'] ? "{$cfg['marca']} ASC, " : "") . "{$cfg['modelo']} ASC";

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($rows as $row) {
            $resultado[] = [
                'id'     => (int)$row['id'],
                'tipo'   => $tipo,
                'modelo' => $row['modelo'] ?? '',
                'marca'  => $row['marca'] ?? ''
            ];
        }
        return $resultado;

    } catch (PDOException $e) {
        error_log("Erro ao buscar {$cfg['tabela']}: " . $e->getMessage());
        return [];
    }
}

// =============================================
// PROCESSA
// =============================================
try {
    $veiculos = [];

    if ($tipo && isset($mapa[$tipo])) {
        // Filtro por tipo específico
        $veiculos = buscarVeiculos($pdo, $tipo, $mapa[$tipo]);
    } else {
        // Busca TODOS os tipos
        foreach ($mapa as $t => $cfg) {
            $veiculos = array_merge($veiculos, buscarVeiculos($pdo, $t, $cfg));
        }
    }

    responderSucesso('Veículos carregados', [
        'veiculos' => $veiculos,
        'total'    => count($veiculos),
        'filtro'   => $tipo ?: 'todos'
    ]);

} catch (Exception $e) {
    error_log("Erro veiculos.php: " . $e->getMessage());
    responderErro('Erro ao buscar veículos.', 500);
}