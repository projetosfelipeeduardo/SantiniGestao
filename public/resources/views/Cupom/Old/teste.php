<html>
<div>
    document.addEventListener('DOMContentLoaded', function() {
    var dataInicioInput = document.getElementById('data_inicio');
    var dataFimInput = document.getElementById('data_fim');
    var btnSave = document.getElementById('btn-save');

    function validarDataFinal() {
    var dataInicio = new Date(dataInicioInput.value);
    var dataFim = new Date(dataFimInput.value);

    if (dataFim < dataInicio) { dataFimInput.classList.add('is-invalid'); btnSave.setAttribute('disabled', 'disabled' ); } else { dataFimInput.classList.remove('is-invalid'); btnSave.removeAttribute('disabled'); } } btnSave.addEventListener('click', function() { var dataInicio=new Date(dataInicioInput.value); var dataFim=new Date(dataFimInput.value); if (dataFim < dataInicio) { alert('A data final não pode ser menor que a data inicial.'); return; } // Se a validação passar, continue com o envio do formulário document.getElementById('form-novo').submit(); }); dataFimInput.addEventListener('input', validarDataFinal); }); </div>
        </dataInicio>

</html>