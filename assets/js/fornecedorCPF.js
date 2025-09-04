document.getElementById('tipoDocumento').addEventListener('change', function() {
    if (this.value === 'CPF') {
        document.getElementById('campoCPF').style.display = '';
        document.getElementById('campoCNPJ').style.display = 'none';
        document.querySelector('#campoCPF input').required = true;
        document.querySelector('#campoCNPJ input').required = false;
    } else {
        document.getElementById('campoCPF').style.display = 'none';
        document.getElementById('campoCNPJ').style.display = '';
        document.querySelector('#campoCPF input').required = false;
        document.querySelector('#campoCNPJ input').required = true;
    }
});