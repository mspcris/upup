<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $nascimento = $_POST['nascimento'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];

    $to = "cristiano@camim.com.br"; 
    $subject = "Nova inscrição no curso";
    $message = "Nome: $nome\n".
               "Data de Nascimento: $nascimento\n".
               "Endereço: $endereco\n".
               "Telefone: $telefone\n";

    $headers = "From: cristiano@camim.com.br";

    if (mail($to, $subject, $message, $headers)) {
        echo "Inscrição enviada com sucesso!";
    } else {
        echo "Erro ao enviar. Tente novamente.";
    }
}
?>
