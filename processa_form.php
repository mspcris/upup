<?php
// Configurações do e-mail
$destinatario = "upupoficial@gmail.com";  // coloque o e-mail da ONG
$assunto = "Novo pedido de inscrição no curso - UPUP";

// Captura os dados do formulário
$nome       = $_POST['nome'] ?? '';
$nascimento = $_POST['nascimento'] ?? '';
$endereco   = $_POST['endereco'] ?? '';
$telefone   = $_POST['telefone'] ?? '';

// Monta a mensagem
$mensagem = "
<strong>Nova solicitação de inscrição recebida:</strong><br><br>
<b>Nome:</b> $nome <br>
<b>Data de Nascimento:</b> $nascimento <br>
<b>Endereço:</b> $endereco <br>
<b>Telefone:</b> $telefone <br>
";

// Cabeçalhos (importante para evitar SPAM)
$headers  = "MIME-Version: 1.1\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: contato@upup.ong.br\r\n";  // use um e-mail do seu domínio
$headers .= "Reply-To: $destinatario\r\n";

// Envia o e-mail
if (mail($destinatario, $assunto, $mensagem, $headers)) {
    echo "<h2>✅ Inscrição enviada com sucesso!</h2>";
    echo "<a href='cursos_artesanato.html'>Voltar</a>";
} else {
    echo "<h2>❌ Ocorreu um erro ao enviar sua inscrição.</h2>";
    echo "<a href='cursos_artesanato.html'>Tente novamente</a>";
}
?>
