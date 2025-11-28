/**
 * Helper Functions - JavaScript
 * 
 * Funções utilitárias para uso no frontend
 * 
 * @package RodustEcommerce
 */

(function(window) {
    'use strict';

    /**
     * Namespace para helpers
     */
    window.RodustHelpers = {
        
        /**
         * Formatar preço: 1234.56 -> R$ 1.234,56
         * 
         * @param {number|string} value Valor numérico
         * @param {boolean} showCurrency Mostrar símbolo R$ (padrão: true)
         * @returns {string} Preço formatado
         */
        formatPrice: function(value, showCurrency) {
            if (showCurrency === undefined) {
                showCurrency = true;
            }
            
            if (value === null || value === undefined || value === '') {
                return showCurrency ? 'R$ 0,00' : '0,00';
            }
            
            const numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                return showCurrency ? 'R$ 0,00' : '0,00';
            }
            
            // Formatar: 1234.56 -> 1.234,56
            const formatted = numValue.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            
            return showCurrency ? 'R$ ' + formatted : formatted;
        },
        
        /**
         * Sanitizar CPF/CNPJ (remover caracteres não numéricos)
         * 
         * @param {string} document Documento com ou sem formatação
         * @returns {string} Documento apenas com números
         */
        sanitizeDocument: function(document) {
            if (!document) {
                return '';
            }
            
            return document.replace(/[^0-9]/g, '');
        },
        
        /**
         * Formatar CPF: 12345678901 -> 123.456.789-01
         * 
         * @param {string} cpf CPF sem formatação
         * @returns {string} CPF formatado
         */
        formatCPF: function(cpf) {
            if (!cpf) {
                return '';
            }
            
            cpf = this.sanitizeDocument(cpf);
            
            if (cpf.length !== 11) {
                return cpf;
            }
            
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        },
        
        /**
         * Formatar CNPJ: 12345678000190 -> 12.345.678/0001-90
         * 
         * @param {string} cnpj CNPJ sem formatação
         * @returns {string} CNPJ formatado
         */
        formatCNPJ: function(cnpj) {
            if (!cnpj) {
                return '';
            }
            
            cnpj = this.sanitizeDocument(cnpj);
            
            if (cnpj.length !== 14) {
                return cnpj;
            }
            
            return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        },
        
        /**
         * Formatar CPF ou CNPJ automaticamente
         * 
         * @param {string} document Documento sem formatação
         * @returns {string} Documento formatado
         */
        formatDocument: function(document) {
            if (!document) {
                return '';
            }
            
            document = this.sanitizeDocument(document);
            
            if (document.length === 11) {
                return this.formatCPF(document);
            } else if (document.length === 14) {
                return this.formatCNPJ(document);
            }
            
            return document;
        },
        
        /**
         * Sanitizar telefone (remover caracteres não numéricos)
         * 
         * @param {string} phone Telefone com ou sem formatação
         * @returns {string} Telefone apenas com números
         */
        sanitizePhone: function(phone) {
            if (!phone) {
                return '';
            }
            
            return phone.replace(/[^0-9]/g, '');
        },
        
        /**
         * Formatar telefone: 11987654321 -> (11) 98765-4321
         * 
         * @param {string} phone Telefone sem formatação
         * @returns {string} Telefone formatado
         */
        formatPhone: function(phone) {
            if (!phone) {
                return '';
            }
            
            phone = this.sanitizePhone(phone);
            
            if (phone.length === 11) {
                // Celular com 9 dígitos: (11) 98765-4321
                return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (phone.length === 10) {
                // Fixo ou celular antigo: (11) 8765-4321
                return phone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            }
            
            return phone;
        },
        
        /**
         * Sanitizar CEP (remover caracteres não numéricos)
         * 
         * @param {string} postalCode CEP com ou sem formatação
         * @returns {string} CEP apenas com números
         */
        sanitizePostalCode: function(postalCode) {
            if (!postalCode) {
                return '';
            }
            
            return postalCode.replace(/[^0-9]/g, '');
        },
        
        /**
         * Formatar CEP: 13400710 -> 13400-710
         * 
         * @param {string} postalCode CEP sem formatação
         * @returns {string} CEP formatado
         */
        formatPostalCode: function(postalCode) {
            if (!postalCode) {
                return '';
            }
            
            postalCode = this.sanitizePostalCode(postalCode);
            
            if (postalCode.length !== 8) {
                return postalCode;
            }
            
            return postalCode.replace(/(\d{5})(\d{3})/, '$1-$2');
        },
        
        /**
         * Escapar HTML para segurança
         * 
         * @param {string} text Texto para escapar
         * @returns {string} Texto escapado
         */
        escapeHtml: function(text) {
            if (!text) {
                return '';
            }
            
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        /**
         * Debounce function (limitar execução)
         * 
         * @param {Function} func Função para executar
         * @param {number} wait Tempo de espera em ms
         * @returns {Function} Função com debounce
         */
        debounce: function(func, wait) {
            let timeout;
            
            return function executedFunction() {
                const context = this;
                const args = arguments;
                
                const later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
})(window);
