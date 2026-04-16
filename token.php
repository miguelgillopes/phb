<?php
header('Content-Type: application/json; charset=UTF-8');

// Gera um token criptograficamente seguro e guarda-o em ficheiro

// Guarda tokens numa pasta local do site por compatibilidade com hosts partilhados
$tokenDir = __DIR__ . '/_tokens/';
if (!is_dir($tokenDir)) mkdir($tokenDir, 0700, true);

// Limpa tokens expirados (> 1 hora) para não acumular ficheiros
foreach (glob($tokenDir . '*.tok') as $f) {
    if (time() - filemtime($f) > 3600) unlink($f);
}

$token = bin2hex(random_bytes(32)); // 64 caracteres hex, imprevisível
file_put_contents($tokenDir . md5($token) . '.tok', time(), LOCK_EX);

echo json_encode(['token' => $token]);
