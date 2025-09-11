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

    // Aplique as máscaras separadamente
    if (window.jQuery) {
        $('.cpf').mask('000.000.000-00', {reverse: true});
        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    }
});