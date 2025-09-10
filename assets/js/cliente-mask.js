document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('tipoDocumento');
    const campoCPF = document.getElementById('campoCPF');
    const campoCNPJ = document.getElementById('campoCNPJ');
    const inputCPF = campoCPF.querySelector('input');
    const inputCNPJ = campoCNPJ.querySelector('input');

    function alternarCampos() {
        if (select.value === 'CPF') {
            campoCPF.style.display = '';
            campoCNPJ.style.display = 'none';
            inputCPF.required = true;
            inputCNPJ.required = false;
            inputCNPJ.value = '';
        } else {
            campoCPF.style.display = 'none';
            campoCNPJ.style.display = '';
            inputCPF.required = false;
            inputCNPJ.required = true;
            inputCPF.value = '';
        }
    }

    select.addEventListener('change', alternarCampos);
    alternarCampos();

    $(document).ready(function() {
    // Máscara para CPF
    $('input[name="cpf_cnpj"]').mask('000.000.000-00', {reverse: true});
    
    // Máscara para CNPJ
    $('input[name="cpf_cnpj"]').mask('00.000.000/0000-00', {reverse: true});

    // Atualizar a máscara conforme o tipo de documento
    $('#tipoDocumento').change(function() {
        if ($(this).val() == 'CNPJ') {
            $('#campoCNPJ').show();
            $('#campoCPF').hide();
        } else {
            $('#campoCNPJ').hide();
            $('#campoCPF').show();
        }
    });
});

});