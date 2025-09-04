$(document).ready(function(){
    // CPF
    $('.cpf').mask('000.000.000-00');

    // CNPJ
    $('.cnpj').mask('00.000.000/0000-00');

    // Telefone
    $('.telefone').mask('(00) 00000-0000');

    // CEP
    $('.cep').mask('00000-000');

    // Data
    $('.data').mask('00/00/0000');

    // Dinheiro
    $('.money').mask('000.000.000,00', {reverse: true});
});

// adiconar <script src="../js/masks.js"></script>
// nos arquivos que usarem as mascaras