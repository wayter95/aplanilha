import { defineRule, configure } from 'vee-validate'
import { required, email, min, max, confirmed, numeric, alpha_spaces } from '@vee-validate/rules'
import { localize, setLocale } from '@vee-validate/i18n'

// 📋 Regras básicas — refletindo validações Laravel
defineRule('required', required)
defineRule('email', email)
defineRule('min', min)
defineRule('max', max)
defineRule('confirmed', confirmed)
defineRule('numeric', numeric)
defineRule('alpha_spaces', alpha_spaces)

// 🔐 Regra customizada de senha forte — opcional (ex: se usar no back tb)
defineRule('password', (value) => {
    if (!value || !value.length) {
        return true
    }
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/
    if (!regex.test(value)) {
        return false, 'A senha deve ter ao menos 8 caracteres, incluindo maiúscula, minúscula e número'
    }
    return true
})

// 🌐 Localização pt-BR
const ptBR = {
    code: 'pt-BR',
    messages: {
        required: '{field} é obrigatório',
        email: '{field} deve ser um email válido',
        min: '{field} deve ter ao menos {length} caracteres',
        max: '{field} deve ter no máximo {length} caracteres',
        confirmed: 'A confirmação de {field} não confere',
        numeric: '{field} deve ser um número',
        alpha_spaces: '{field} deve conter apenas letras e espaços',
        password: 'A senha deve ter ao menos 8 caracteres, incluindo maiúscula, minúscula e número'
    },
    names: {
        email: 'Email',
        password: 'Senha',
        password_confirmation: 'Confirmação de senha',
        name: 'Nome',
        first_name: 'Primeiro nome',
        last_name: 'Sobrenome',
        subdomain: 'Subdomínio',
        company_name: 'Nome da empresa',
        phone: 'Telefone'
    }
}

// 🔧 Configuração global
configure({
    generateMessage: (ctx) => {
        const messages = ptBR.messages;
        const names = ptBR.names;
        let msg = messages[ctx.rule.name] || '{field} é inválido';
        // Interpolação do nome do campo
        msg = msg.replace('{field}', names[ctx.field] || ctx.field);
        // Interpolação do length (min/max)
        if (msg.includes('{length}')) {
            // ctx.rule.params[0] normalmente é o valor do min/max
            const length = Array.isArray(ctx.rule.params) ? ctx.rule.params[0] : '';
            msg = msg.replace('{length}', length);
        }
        return msg;
    },
    validateOnBlur: true,
    validateOnChange: true,
    validateOnInput: false,
    validateOnModelUpdate: true
});

setLocale('pt-BR');
export { ptBR };
