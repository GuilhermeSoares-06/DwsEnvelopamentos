<?php
// =============================================
// agendar.php - Processa agendamento de serviços
// =============================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../Banco/conexao.php';
// Token opcional no agendamento (cliente pode estar logado ou não)
require_once __DIR__ . '/../Auth/token.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'erro','mensagem'=>'Método não permitido.']);
    exit;
}

// ---- Recebe dados ----
$nome        = trim($_POST['nome']               ?? '');
$telefone    = trim($_POST['telefone']           ?? '');
$email       = trim($_POST['email']             ?? '');
$veiculo     = trim($_POST['veiculo']            ?? '');
$descricao   = trim($_POST['descricao']          ?? '');
$tipo        = trim($_POST['tipo_servico']        ?? '');
$acabamento  = trim($_POST['acabamento']         ?? '1.0');
$data_ag     = trim($_POST['data_agendamento']   ?? '');
$horario_ag  = trim($_POST['horario_agendamento']?? '');
$valor_total = trim($_POST['valor_total']        ?? '0');
$valor_base  = trim($_POST['valor_base']         ?? '0');

// ---- Validações ----
$erros = [];
if (!$nome)      $erros[] = 'Nome é obrigatório.';
if (!$telefone)  $erros[] = 'Telefone é obrigatório.';
if (!$veiculo)   $erros[] = 'Veículo/Mobília é obrigatório.';
if (!$descricao) $erros[] = 'Descrição do serviço é obrigatória.';
if (!$tipo)      $erros[] = 'Tipo de serviço é obrigatório.';
if (!$data_ag)   $erros[] = 'Data do agendamento é obrigatória.';
if (!$horario_ag)$erros[] = 'Horário é obrigatório.';

if ($erros) {
    http_response_code(400);
    echo json_encode(['status'=>'erro','mensagem'=>implode(' ', $erros)]);
    exit;
}

// ---- Verifica se horário já está ocupado ----
try {
    $chk = $pdo->prepare(
        "SELECT serid FROM servicos WHERE DATE(serdata_servico) = :data
         AND TIME_FORMAT(TIME(serdata_servico),'%H:%i') = :hora LIMIT 1"
    );
    $chk->execute([':data' => $data_ag, ':hora' => $horario_ag]);
    if ($chk->fetch()) {
        http_response_code(409);
        echo json_encode(['status'=>'erro','mensagem'=>'Este horário já está ocupado. Escolha outro.']);
        exit;
    }

    // ---- Busca ou cria cliente ----
    $cliid = null;

    // Se tem token, usa o id do token
    $tk = Token::doHeader() ?? Token::doCookie();
    if ($tk) {
        $payload = Token::validar($tk);
        if ($payload) $cliid = (int)$payload['id'];
    }

    if (!$cliid) {
        // Busca por telefone
        $s = $pdo->prepare("SELECT cliid FROM clientes WHERE clitel = :t LIMIT 1");
        $s->execute([':t' => $telefone]);
        $cl = $s->fetch();
        if ($cl) {
            $cliid = (int)$cl['cliid'];
        } else {
            $ins = $pdo->prepare(
                "INSERT INTO clientes (clinome, clitel, cliendereco, tipocliente)
                 VALUES (:n, :t, 'Não informado', 'cliente')"
            );
            $ins->execute([':n' => $nome, ':t' => $telefone]);
            $cliid = (int)$pdo->lastInsertId();
        }
    }

    // ---- Descrição completa ----
    $acab_map = ['1.0'=>'Fosco','1.15'=>'Brilhante','1.30'=>'Perolizado','1.40'=>'Texturizado'];
    $desc_completa = "Veículo: $veiculo | $descricao";
    if ($acabamento !== '1.0')
        $desc_completa .= ' | Acabamento: ' . ($acab_map[$acabamento] ?? $acabamento);

    // ---- Insere serviço ----
    $ins = $pdo->prepare(
        "INSERT INTO servicos (cliid, tipo_servico, serdescricao, servalor, serdata_servico)
         VALUES (:cliid, :tipo, :desc, :valor, :data_hora)"
    );
    $ins->execute([
        ':cliid'     => $cliid,
        ':tipo'      => $tipo,
        ':desc'      => $desc_completa,
        ':valor'     => $valor_total,
        ':data_hora' => "$data_ag $horario_ag:00",
    ]);
    $serid = (int)$pdo->lastInsertId();

    // ---- Link WhatsApp ----
    $msg = urlencode(
        "🆕 NOVO AGENDAMENTO DWS!\n\n" .
        "👤 Cliente: $nome\n📞 $telefone\n📧 $email\n\n" .
        "📅 Data: $data_ag  🕐 Horário: $horario_ag\n" .
        "🚗 Veículo: $veiculo\n🔧 Serviço: $tipo\n💰 Valor: R$ $valor_total\n\n" .
        "📝 $descricao\n\n✅ Agendamento #$serid"
    );
    $whatsapp_url = "https://wa.me/5514996175617?text=$msg";

    echo json_encode([
        'status'       => 'sucesso',
        'mensagem'     => "Agendamento #$serid realizado com sucesso!",
        'servico_id'   => $serid,
        'whatsapp_url' => $whatsapp_url,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'erro','mensagem'=>'Erro interno ao processar o agendamento.']);
}
?>
