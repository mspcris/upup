<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $nascimento = $_POST['nascimento'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    // e-mail de destino (pode ser um e-mail @upup.com.br configurado na KingHost)
    $to = "contato@upup.com.br";

    $subject = "Nova inscrição no curso";
    $message = "Nome: $nome\n".
               "Data de Nascimento: $nascimento\n".
               "Endereço: $endereco\n".
               "Telefone: $telefone\n";

    // remetente deve ser do mesmo domínio (boa prática no KingHost)
    $headers = "From: contato@upup.com.br\r\n";
    $headers .= "Reply-To: $to\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "Inscrição enviada com sucesso!";
    } else {
        echo "Erro ao enviar. Tente novamente.";
    }
}
?>
