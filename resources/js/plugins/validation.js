import { setLocale } from "@vee-validate/i18n";
import {
    alpha_spaces,
    confirmed,
    email,
    max,
    min,
    numeric,
    required,
} from "@vee-validate/rules";
import { configure, defineRule } from "vee-validate";

// 📋 Regras básicas — refletindo validações Laravel
defineRule("required", required);
defineRule("email", email);
defineRule("min", min);
defineRule("max", max);
defineRule("confirmed", confirmed);
defineRule("numeric", numeric);
defineRule("alpha_spaces", alpha_spaces);

// � Regra assíncrona para validar unicidade de email por tenant
import axios from "axios";
defineRule("unique_email_tenant", async (value, [url, tenantId]) => {
    if (!value || !tenantId) return true;
    try {
        const response = await axios.post(url, {
            email: value,
            tenant_id: tenantId,
        });
        return response.data?.unique === true
            ? true
            : "Este email já está em uso neste tenant.";
    } catch (e) {
        return "Erro ao validar email.";
    }
});

// �🔐 Regra customizada de senha forte — opcional (ex: se usar no back tb)
defineRule("password", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
    if (!regex.test(value)) {
        return (
            false,
            "A senha deve ter ao menos 8 caracteres, incluindo maiúscula, minúscula e número."
        );
    }
    return true;
});
defineRule("password", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!regex.test(value)) {
        return "A senha deve ter ao menos 8 caracteres, incluindo maiúscula, minúscula, número e caractere especial";
    }
    return true;
});

defineRule("uuid_v7", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex =
        /^[0-9a-f]{8}-7[0-9a-f]{3}-[0-9a-f]{4}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i;
    if (!regex.test(value)) {
        return "O valor deve ser um UUID v7 válido.";
    }
    return true;
});

// 📞 Regra customizada para telefone brasileiro
defineRule("br_phone", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^(\(\d{2}\)\s?|\d{2}\s?)?\d{4,5}-?\d{4}$/;
    if (!regex.test(value)) {
        return "Telefone deve ter formato válido (ex: (11) 99999-9999)";
    }
    return true;
});

// 📮 Regra customizada para CEP brasileiro  
defineRule("br_postal_code", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^\d{5}-?\d{3}$/;
    if (!regex.test(value)) {
        return "CEP deve ter formato válido (ex: 12345-678)";
    }
    return true;
});

// 🏙️ Regra customizada para nomes de cidades
defineRule("city_name", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^[A-Za-zÀ-ÿ\s\.\-]+$/;
    if (!regex.test(value)) {
        return "Cidade deve conter apenas letras, espaços e caracteres válidos";
    }
    return true;
});

// 🌎 Regra customizada para estados brasileiros (sigla)
defineRule("br_state", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^[A-Za-z]{2}$/;
    if (!regex.test(value)) {
        return "Estado deve ter 2 letras (ex: SP)";
    }
    return true;
});

// 🌍 Regra customizada para nomes de países
defineRule("country_name", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^[A-Za-zÀ-ÿ\s]+$/;
    if (!regex.test(value)) {
        return "País deve conter apenas letras e espaços";
    }
    return true;
});

// 🏢 Regra customizada para nomes de empresas
defineRule("company_name", (value) => {
    if (!value || !value.length) {
        return true;
    }
    const regex = /^[A-Za-zÀ-ÿ0-9\s\.\-\_\&\(\)]+$/;
    if (!regex.test(value)) {
        return "Nome da empresa deve conter apenas letras, números, espaços e caracteres especiais válidos";
    }
    return true;
});

// 📍 Regras para latitude e longitude
defineRule("latitude", (value) => {
    if (!value && value !== 0) {
        return true;
    }
    const num = parseFloat(value);
    if (isNaN(num) || num < -90 || num > 90) {
        return "Latitude deve estar entre -90 e 90";
    }
    return true;
});

defineRule("longitude", (value) => {
    if (!value && value !== 0) {
        return true;
    }
    const num = parseFloat(value);
    if (isNaN(num) || num < -180 || num > 180) {
        return "Longitude deve estar entre -180 e 180";
    }
    return true;
});

const ptBR = {
    code: "pt-BR",
    messages: {
        required: "{field} obrigatório.",
        email: "{field} deve ser um email válido.",
        min: "{field} deve ter ao menos {length} caracteres.",
        max: "{field} deve ter no máximo {length} caracteres.",
        confirmed: "A confirmação de {field} não confere.",
        numeric: "{field} deve ser um número.",
        alpha_spaces: "{field} deve conter apenas letras e espaços.",
        password:
            "A senha deve ter ao menos 8 caracteres, incluindo maiúscula, minúscula e número.",
        unique_email_tenant: "Este email já está em uso neste tenant.",
        br_phone: "Telefone deve ter formato válido (ex: (11) 99999-9999).",
        br_postal_code: "CEP deve ter formato válido (ex: 12345-678).",
        city_name: "Cidade deve conter apenas letras, espaços e caracteres válidos.",
        br_state: "Estado deve ter 2 letras (ex: SP).",
        country_name: "País deve conter apenas letras e espaços.",
        company_name: "Nome da empresa deve conter apenas letras, números, espaços e caracteres especiais válidos.",
        latitude: "Latitude deve estar entre -90 e 90.",
        longitude: "Longitude deve estar entre -180 e 180.",
        url: "Website deve ser uma URL válida (ex: https://exemplo.com)."
    },
    names: {
        email: "Email",
        password: "Senha",
        password_confirmation: "Confirmação de senha",
        current_password: "Senha atual",
        name: "Nome",
        first_name: "Primeiro nome",
        last_name: "Sobrenome",
        subdomain: "Subdomínio",
        company_name: "Nome da empresa",
        phone: "Telefone",
        address: "Endereço",
        display_name: "Nome de exibição",
        type: "Tipo",
        website: "Website",
        name_line: "Linha de nome",
        street_visiting: "Rua (visita)",
        house_number_visiting: "Número (visita)",
        postal_code_visiting: "CEP (visita)",
        city_visiting: "Cidade (visita)",
        state_visiting: "Estado (visita)",
        country_visiting: "País (visita)",
        street_mailing: "Rua (correspondência)",
        house_number_mailing: "Número (correspondência)",
        postal_code_mailing: "CEP (correspondência)",
        city_mailing: "Cidade (correspondência)",
        state_mailing: "Estado (correspondência)",
        country_mailing: "País (correspondência)",
        street_billing: "Rua (cobrança)",
        house_number_billing: "Número (cobrança)",
        postal_code_billing: "CEP (cobrança)",
        city_billing: "Cidade (cobrança)",
        state_billing: "Estado (cobrança)",
        country_billing: "País (cobrança)",
        general_notes: "Notas gerais",
        mobile: "Celular",
        role: "Cargo"
    },
};

configure({
    generateMessage: (ctx) => {
        const messages = ptBR.messages;
        const names = ptBR.names;
        let msg = messages[ctx.rule.name] || "{field} é inválido.";
        // Interpolação do nome do campo
        msg = msg.replace("{field}", names[ctx.field] || ctx.field);
        // Interpolação do length (min/max)
        if (msg.includes("{length}")) {
            // ctx.rule.params[0] normalmente é o valor do min/max
            const length = Array.isArray(ctx.rule.params)
                ? ctx.rule.params[0]
                : "";
            msg = msg.replace("{length}", length);
        }
        return msg;
    },
    validateOnBlur: true,
    validateOnChange: true,
    validateOnInput: false,
    validateOnModelUpdate: true,
});

setLocale("pt-BR");
export { ptBR };
