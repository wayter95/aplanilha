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

defineRule("unique_remote", async (value, [url, field]) => {
    if (!value || !value.length || !url) {
        return true;
    }
    try {
        const response = await fetch(
            url + `?${field}=` + encodeURIComponent(value)
        );
        const data = await response.json();
        if (data.exists) {
            return `O valor já está em uso.`;
        }
        return true;
    } catch (e) {
        return "Erro ao validar unicidade.";
    }
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
