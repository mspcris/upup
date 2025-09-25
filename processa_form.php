<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome   = $_POST['nome'] ?? '';
    $data   = $_POST['data_nascimento'] ?? '';
    $end    = $_POST['endereco'] ?? '';
    $fone   = $_POST['telefone'] ?? '';

    $to      = "contato@upup.ong.br"; // coloque aqui um e-mail criado no painel da KingHost
    $subject = "Nova inscrição pelo site UPUP";
    $message = "Nome: $nome\n".
               "Data de Nascimento: $data\n".
               "Endereço: $end\n".
               "Telefone: $fone\n";

    $headers = "From: contato@upup.ong.br\r\n".
               "Reply-To: $to\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "✅ Formulário enviado com sucesso!";
    } else {
        echo "❌ Erro ao enviar. Verifique o PHP Mail da hospedagem.";
    }
}
?>
