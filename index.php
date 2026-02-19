<?php
// proxy_webhook.php

$token = '#@DAF345wer3254'; // mesmo token do seu webhook na Hostinger

// 1️⃣ Validação inicial da Meta (GET)
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

// 2️⃣ Responde rápido pro POST da Meta
http_response_code(200);
flush();

// 3️⃣ Lê o JSON do POST
$input = file_get_contents('php://input');

// 4️⃣ Log opcional (pra debug)
file_put_contents('log_proxy.txt', date('Y-m-d H:i:s') . " => " . $input . "\n", FILE_APPEND);

// 5️⃣ Repassa o JSON pro seu webhook original
$url_crm = 'https://growthsis.com.br/admin/whatsapp_webhook'; // coloque aqui sua URL real

$ch = curl_init($url_crm);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
?>