import { configure, defineRule } from 'vee-validate'
import { localize, setLocale } from '@vee-validate/i18n'
import ptBR from '@vee-validate/i18n/dist/locale/pt_BR.json'
import * as rules from '@vee-validate/rules'

// Registrar apenas regras compatíveis do VeeValidate
defineRule('required', rules.required)
defineRule('email', rules.email)
defineRule('min', rules.min)
defineRule('max', rules.max)
defineRule('confirmed', rules.confirmed)
defineRule('numeric', rules.numeric)
defineRule('alpha_spaces', rules.alpha_spaces)
// Adicione outras regras conforme necessário e compatíveis

// Configuração global pt-BR
configure({
    generateMessage: localize({ 'pt-BR': ptBR }),
    validateOnBlur: true,
    validateOnChange: true,
    validateOnInput: false,
    validateOnModelUpdate: true
})

setLocale('pt-BR')

// Exemplo de regra customizada assíncrona (opcional)
defineRule('verificacaoRemotaDisponibilidade', async (value: string, [endpoint]: [string]) => {
    if (!value || !endpoint) return true
    try {
        const response = await fetch(`${endpoint}?valor=${encodeURIComponent(value)}`)
        const data = await response.json()
        return data.disponivel ? true : 'Este valor já está em uso.'
    } catch {
        return 'Não foi possível validar este campo no momento.'
    }
})
