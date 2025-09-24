<?php
session_start();
session_unset();
session_destroy();
echo "<script>alert('Sessão encerrada com sucesso!'); window.location.href='login.html';</script>";
exit();
