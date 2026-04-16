<?php
header('Content-Type: application/json; charset=UTF-8');

// Apenas aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido']);
    exit;
}

// Validação do token de sessão
$token = $_POST['_token'] ?? '';
$tokenFile = __DIR__ . '/_tokens/' . md5($token) . '.tok';
if (empty($token) || !file_exists($tokenFile)) {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'erro' => 'token_invalido']);
    exit;
}
// Token de uso único — apaga após validar
unlink($tokenFile);

// Honeypot: bots preenchem este campo, humanos não
$honeypot = $_POST['_honeypot'] ?? '';
if ($honeypot !== '') {
    // Responde com sucesso falso para não alertar o bot
    echo json_encode(['sucesso' => true]);
    exit;
}

// Rate limiting: máx. 3 submissões por IP por hora
$ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$cacheDir  = __DIR__ . '/_rl/';
$cacheFile = $cacheDir . md5($ip) . '.json';
$limite    = 3;
$janela    = 3600; // segundos (1 hora)

if (!is_dir($cacheDir)) mkdir($cacheDir, 0700, true);

$dados = file_exists($cacheFile)
    ? json_decode(file_get_contents($cacheFile), true)
    : ['count' => 0, 'since' => time()];

// Reset se a janela expirou
if (time() - $dados['since'] > $janela) {
    $dados = ['count' => 0, 'since' => time()];
}

if ($dados['count'] >= $limite) {
    http_response_code(429);
    echo json_encode(['sucesso' => false, 'erro' => 'too_many_requests']);
    exit;
}

$dados['count']++;
file_put_contents($cacheFile, json_encode($dados), LOCK_EX);

// Sanitização
$nome      = trim(htmlspecialchars($_POST['nome']      ?? '', ENT_QUOTES, 'UTF-8'));
$telefone  = trim(htmlspecialchars($_POST['telefone']  ?? '', ENT_QUOTES, 'UTF-8'));
$email     = trim(htmlspecialchars($_POST['email']     ?? '', ENT_QUOTES, 'UTF-8'));
$modalidade = trim(htmlspecialchars($_POST['modalidade'] ?? '', ENT_QUOTES, 'UTF-8'));
$mensagem  = trim(htmlspecialchars($_POST['mensagem']  ?? '', ENT_QUOTES, 'UTF-8'));

// Validação mínima server-side
if (empty($nome) || empty($telefone) || empty($modalidade)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'erro' => 'Campos obrigatórios em falta']);
    exit;
}

// Destino
$para = 'geral@pacehybridbox.pt';

// Assunto
$assunto = '=?UTF-8?B?' . base64_encode("Nova marcação — $modalidade | $nome") . '?=';

// Corpo do email
$corpo  = "NOVO PEDIDO DE AULA — PACE Hybrid Box\n";
$corpo .= str_repeat('─', 40) . "\n\n";
$corpo .= "Nome:        $nome\n";
$corpo .= "Telefone:    $telefone\n";
$corpo .= "Email:       " . ($email ?: '—') . "\n";
$corpo .= "Modalidade:  $modalidade\n\n";
if (!empty($mensagem)) {
    $corpo .= "Mensagem:\n$mensagem\n\n";
}
$corpo .= str_repeat('─', 40) . "\n";
$corpo .= "Enviado em: " . date('d/m/Y H:i') . " (hora do servidor)\n";

// Headers
$headers  = "From: PACE Hybrid Box <noreply@pacehybridbox.pt>\r\n";
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $headers .= "Reply-To: $email\r\n";
}
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Envio
$enviado = mail($para, $assunto, $corpo, $headers);

if ($enviado) {
    echo json_encode(['sucesso' => true]);
} else {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Falha no envio do email']);
}
