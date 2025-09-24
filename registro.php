<?php
$arquivo = 'usuarios.json';

$nome = $_POST['nome'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$matricula = $_POST['matricula'];
$role = $_POST['role'];
$contrato = $_POST['contrato'];
$plantao = $_POST['plantao']; // Novo campo "plantão"

$novoUsuario = [
    "nome" => $nome,
    "email" => $email,
    "usuario" => $usuario,
    "matricula" => $matricula,
    "role" => $role,
    "contrato" => $contrato,
    "plantao" => $plantao // Adicionado ao JSON
];

$usuarios = [];
if (file_exists($arquivo)) {
    $usuarios = json_decode(file_get_contents($arquivo), true);
}

foreach ($usuarios as $user) {
    if ($user['usuario'] === $usuario) {
        echo "Usuário já existe!";
        exit;
    }
}

$usuarios[] = $novoUsuario;
file_put_contents($arquivo, json_encode($usuarios, JSON_PRETTY_PRINT));

echo "<script>alert('Usuário registrado com sucesso!');window.location.href='login.html';</script>";
?>
