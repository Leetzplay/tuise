<?php
session_start();

$arquivo = 'usuarios.json';

$usuario = $_POST['usuario'] ?? '';
$matricula = $_POST['matricula'] ?? '';

// Verificação do admin fixo com sessão
$adminUser = 'rhtuise';
$adminPass = 'rhtuiserj1234';

if ($usuario === $adminUser && $matricula === $adminPass) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['tipo'] = 'admin';
    header("Location: admin.php");
    exit;
}

// Verifica se o arquivo de usuários existe
if (!file_exists($arquivo)) {
    echo "<script>alert('Nenhum usuário cadastrado.');window.location.href='login.html';</script>";
    exit;
}

// Carrega os usuários do JSON
$usuarios = json_decode(file_get_contents($arquivo), true);

// Verifica usuários comuns
foreach ($usuarios as $user) {
    if ($user['usuario'] === $usuario && $user['matricula'] === $matricula) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['tipo'] = 'usuario';
        file_put_contents("usuario_logado.txt", $usuario);
        header("Location: usuario.php");
        exit;
    }
}

// Se chegou até aqui, o login falhou
echo "<script>alert('Usuário ou senha inválidos');window.location.href='login.html';</script>";
?>
