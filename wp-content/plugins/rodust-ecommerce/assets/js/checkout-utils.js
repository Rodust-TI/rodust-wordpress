/**
 * Checkout Utilities
 * Helper functions for formatting and validation
 */

// Formatar CEP
function formatCEP(cep) {
    cep = cep.replace(/\D/g, '');
    if (cep.length !== 8) return cep;
    return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
}

// Formatar CPF
function formatCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return cpf;
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

// Formatar CNPJ
function formatCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    if (cnpj.length !== 14) return cnpj;
    return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
}

// Função para exibir toast/alert
function showToast(message, type = 'info') {
    // Tipo pode ser: 'success', 'error', 'info', 'warning'
    alert(message); // Temporário - depois substituir por toast mais elegante
}
