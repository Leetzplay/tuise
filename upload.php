
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $usuario = $_POST['usuario'];

  if (isset($_FILES['holerite']) && $_FILES['holerite']['error'] === 0) {
    $nomeOriginal = $_FILES['holerite']['name'];
    $ext = pathinfo($nomeOriginal, PATHINFO_EXTENSION);

    if ($ext !== 'pdf') {
      echo "Somente arquivos PDF são permitidos.";
      exit;
    }

    if (!is_dir("arquivos")) {
      mkdir("arquivos");
    }

    $novoNome = "arquivos/{$usuario}-" . date('Y-m-d') . ".pdf";
    move_uploaded_file($_FILES['holerite']['tmp_name'], $novoNome);

    echo "<script>alert('Holerite enviado com sucesso!'); window.location.href='admin.php';</script>";
  } else {
    echo "Erro no upload.";
  }
}
?>
