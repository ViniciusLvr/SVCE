// $(document).ready(function(){
//     // CPF
//     $('.cpf').mask('000.000.000-00');

//     // CNPJ
//     $('.cnpj').mask('00.000.000/0000-00');

//     // Telefone
//     $('.telefone').mask('(00) 00000-0000');

//     // CEP
//     $('.cep').mask('00000-000');

//     // Data
//     $('.data').mask('00/00/0000');

//     // Dinheiro
//     $('.money').mask('000.000.000,00', {reverse: true});
// });

$(document).ready(function(){
    $('.cpf').mask('000.000.000-00');
    $('.cnpj').mask('00.000.000/0001-00');

    // Alterna campos CPF/CNPJ
    $('#tipoDocumento').on('change', function() {
        if ($(this).val() === 'CPF') {
            $('#campoCPF').show();
            $('#campoCNPJ').hide();
            $('#campoCNPJ input').val('');
        } else {
            $('#campoCPF').hide();
            $('#campoCNPJ').show();
            $('#campoCPF input').val('');
        }
    }).trigger('change');
});


// adiconar <script src="../assets/js/masks.js"></script>
// nos arquivos que usarem as mascaras