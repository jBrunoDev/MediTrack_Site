<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "dbmeditrack";

$conn = new mysqli($servidor, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
} else {
    echo "Conexão estabelecida com sucesso!<br>";
}

// Pegando dados do formulário
$token = $_POST['token'];
$senha = $_POST['senha'];
$confirmar = $_POST['confirmar'];

if (empty($token) || empty($senha) || empty($confirmar)) {
    echo "Todos os campos são obrigatórios!";
    exit;
}

if ($senha !== $confirmar) {
    echo "As senhas não coincidem!";
    exit;
}

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Verifica se o token é válido
$sql = "SELECT id_funcionario FROM funcionario_ WHERE token = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Token inválido!";
    exit;
}

// Atualiza a senha
$update = $conn->prepare("UPDATE funcionario_ SET senha = ?, token = NULL WHERE token = ?");
$update->bind_param("ss", $senhaHash, $token);

if ($update->execute()) {
    echo "Senha criada com sucesso! Agora você pode acessar o sistema.";
} else {
    echo "Erro ao atualizar a senha: " . $update->error;
}

$stmt->close();
$update->close();
$conn->close();
?>
