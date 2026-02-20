<?php
// proxy_webhook.php — Proxy da Meta para seu CRM na Hostinger

$token = '#@DAF345wer3254'; // mesmo token usado no painel da Meta

// 1️⃣ Validação inicial (GET) — usada apenas pela Meta para verificar o webhook
if (isset($_GET['hub_challenge']) && isset($_GET['hub_verify_token'])) {
    $hub_challenge = $_GET['hub_challenge'];
    $token_verificacao = $_GET['hub_verify_token'];

    if ($token === $token_verificacao) {
        echo $hub_challenge;
        exit;
    } else {
        http_response_code(403);
        echo "Token inválido.";
        exit;
    }
}

// 2️⃣ Responde imediatamente pra Meta (evita timeout)
ignore_user_abort(true);
header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
header("Content-Type: application/json");
echo json_encode(["status" => "ok"]);
flush();

// 3️⃣ Lê o JSON recebido
$input = file_get_contents('php://input');

// Se estiver vazio (ex.: ping do cron), não repassa pro CRM
if (empty(trim($input))) {
    exit;
}

// 4️⃣ Log local (para debug e auditoria)
file_put_contents(
    'log_proxy.txt',
    "[" . date('Y-m-d H:i:s') . "] " . $input . "\n",
    FILE_APPEND
);

// 5️⃣ Repassa o JSON para o webhook original (CRM na Hostinger)
$url_crm = 'https://growthsis.com.br/admin/whatsapp_webhook'; // coloque sua URL real

$ch = curl_init($url_crm);
curl_setopt_array($ch, [
    CURLOPT_POST            => true,
    CURLOPT_POSTFIELDS      => $input,
    CURLOPT_HTTPHEADER      => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_CONNECTTIMEOUT  => 5,   // tenta conectar por até 5s
    CURLOPT_TIMEOUT         => 10,  // espera no máximo 10s pela resposta
    CURLOPT_SSL_VERIFYPEER  => true // garante conexão HTTPS segura
]);
$result = curl_exec($ch);

// Se quiser registrar erros de conexão:
if ($result === false) {
    file_put_contents(
        'log_proxy.txt',
        "[" . date('Y-m-d H:i:s') . "] CURL ERROR: " . curl_error($ch) . "\n",
        FILE_APPEND
    );
}

curl_close($ch);
?>