import { ptBR } from "@/plugins/validation";
import { defineRule, useForm } from "vee-validate";
import * as yup from "yup";

export function useSettingsValidation() {
    const personalDataSchema = yup.object({
        name: yup
            .string()
            .required(
                ptBR.messages.required.replace("{field}", ptBR.names.name)
            ),
        username: yup.string().nullable(),
        email: yup
            .string()
            .required(
                ptBR.messages.required.replace("{field}", ptBR.names.email)
            )
            .email(ptBR.messages.email.replace("{field}", ptBR.names.email)),
        phone: yup.string().nullable(),
    });

    const passwordSchema = yup.object({
        current_password: yup
            .string()
            .required(
                ptBR.messages.required.replace(
                    "{field}",
                    ptBR.names.current_password
                )
            ),
        password: yup
            .string()
            .required(
                ptBR.messages.required.replace("{field}", ptBR.names.password)
            )
            .min(
                8,
                ptBR.messages.min
                    .replace("{field}", ptBR.names.password)
                    .replace("{length}", "8")
            ),
        password_confirmation: yup
            .string()
            .required(
                ptBR.messages.required.replace(
                    "{field}",
                    ptBR.names.password_confirmation
                )
            )
            .oneOf(
                [yup.ref("password")],
                ptBR.messages.confirmed.replace("{field}", ptBR.names.password)
            ),
    });

    function createPersonalDataForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: personalDataSchema,
            initialValues: {
                name: "",
                username: "",
                email: "",
                phone: "",
                ...initialValues,
            },
        });

        const [name, nameAttrs] = defineRule("name");
        const [username, usernameAttrs] = defineRule("username");
        const [email, emailAttrs] = defineRule("email");
        const [phone, phoneAttrs] = defineRule("phone");

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, username, email, phone },
            attrs: {
                name: nameAttrs,
                username: usernameAttrs,
                email: emailAttrs,
                phone: phoneAttrs,
            },
        };
    }

    function createPasswordForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: passwordSchema,
            initialValues: {
                current_password: "",
                password: "",
                password_confirmation: "",
                ...initialValues,
            },
        });

        const [currentPassword, currentPasswordAttrs] =
            defineRule("current_password");
        const [password, passwordAttrs] = defineRule("password");
        const [passwordConfirmation, passwordConfirmationAttrs] = defineRule(
            "password_confirmation"
        );

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { currentPassword, password, passwordConfirmation },
            attrs: {
                currentPassword: currentPasswordAttrs,
                password: passwordAttrs,
                passwordConfirmation: passwordConfirmationAttrs,
            },
        };
    }

    return {
        personalDataSchema,
        passwordSchema,
        createPersonalDataForm,
        createPasswordForm,
    };
}
