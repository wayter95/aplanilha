import { useForm, defineField } from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useUserValidation() {
    const createUserSchema = yup.object({
        first_name: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.first_name)).min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.first_name)),
        last_name: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.last_name)).min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.last_name)),
        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email))
            .max(100, ptBR.messages.max.replace('{field}', ptBR.names.email).replace('{length}', '100'))
            .test('unique_email_tenant', ptBR.messages.unique_email_tenant, async function (value) {
                const tenantId = this.parent.client_id || this.parent.tenant_id;
                if (!value || !tenantId) return true;
                try {
                    const response = await fetch('/api/check-email', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: value, tenant_id: tenantId })
                    });
                    const data = await response.json();
                    return data.unique === true;
                } catch (e) {
                    return this.createError({ message: 'Erro ao validar email.' });
                }
            }),
        password: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.password))
            .min(8, ptBR.messages.min.replace('{field}', ptBR.names.password).replace('{length}', '8'))
            .max(50, ptBR.messages.max.replace('{field}', ptBR.names.password).replace('{length}', '50'))
            .matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/, ptBR.messages.password),
        password_confirmation: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.password_confirmation))
            .oneOf([yup.ref('password')], ptBR.messages.confirmed.replace('{field}', ptBR.names.password)),
        role_id: yup.string().required(ptBR.messages.required.replace('{field}', 'Perfil')).uuid(),
        is_active: yup.boolean().default(true),
        phone: yup.string().nullable().matches(/^(\d{2})\s\d{4,5}-\d{4}$/, ptBR.messages.numeric.replace('{field}', ptBR.names.phone)),
        birth_date: yup.date().nullable().max(new Date(), 'A data de nascimento não pode ser futura.')
    })

    const updateUserSchema = yup.object({
        first_name: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.first_name)).min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.first_name)),
        last_name: yup.string().required(ptBR.messages.required.replace('{field}', ptBR.names.last_name)).min(2).max(50)
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', ptBR.names.last_name)),
        email: yup.string()
            .required(ptBR.messages.required.replace('{field}', ptBR.names.email))
            .email(ptBR.messages.email.replace('{field}', ptBR.names.email))
            .max(100, ptBR.messages.max.replace('{field}', ptBR.names.email).replace('{length}', '100'))
            .test('unique_email_tenant', ptBR.messages.unique_email_tenant, async function (value) {
                const tenantId = this.parent.client_id || this.parent.tenant_id;
                if (!value || !tenantId) return true;
                try {
                    const response = await fetch('/api/check-email', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: value, tenant_id: tenantId })
                    });
                    const data = await response.json();
                    return data.unique === true;
                } catch (e) {
                    return this.createError({ message: 'Erro ao validar email.' });
                }
            }),
        role_id: yup.string().required(ptBR.messages.required.replace('{field}', 'Perfil')).uuid(),
        is_active: yup.boolean().default(true),
        phone: yup.string().nullable().matches(/^(\d{2})\s\d{4,5}-\d{4}$/, ptBR.messages.numeric.replace('{field}', ptBR.names.phone)),
        birth_date: yup.date().nullable().max(new Date(), 'A data de nascimento não pode ser futura.')
    })

    function createUserForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: createUserSchema,
            initialValues: {
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role_id: '',
                is_active: true,
                phone: '',
                birth_date: '',
                ...initialValues
            }
        })

        const [firstName, firstNameAttrs] = defineField('first_name')
        const [lastName, lastNameAttrs] = defineField('last_name')
        const [email, emailAttrs] = defineField('email')
        const [password, passwordAttrs] = defineField('password')
        const [passwordConfirmation, passwordConfirmationAttrs] = defineField('password_confirmation')
        const [roleId, roleIdAttrs] = defineField('role_id')
        const [isActive, isActiveAttrs] = defineField('is_active')
        const [phone, phoneAttrs] = defineField('phone')
        const [birthDate, birthDateAttrs] = defineField('birth_date')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: {
                firstName, lastName, email, password, passwordConfirmation,
                roleId, isActive, phone, birthDate
            },
            attrs: {
                firstName: firstNameAttrs,
                lastName: lastNameAttrs,
                email: emailAttrs,
                password: passwordAttrs,
                passwordConfirmation: passwordConfirmationAttrs,
                roleId: roleIdAttrs,
                isActive: isActiveAttrs,
                phone: phoneAttrs,
                birthDate: birthDateAttrs
            }
        }
    }

    function updateUserForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: updateUserSchema,
            initialValues: {
                first_name: '',
                last_name: '',
                email: '',
                role_id: '',
                is_active: true,
                phone: '',
                birth_date: '',
                ...initialValues
            }
        })
        const [firstName, firstNameAttrs] = defineField('first_name')
        const [lastName, lastNameAttrs] = defineField('last_name')
        const [email, emailAttrs] = defineField('email')
        const [roleId, roleIdAttrs] = defineField('role_id')
        const [isActive, isActiveAttrs] = defineField('is_active')
        const [phone, phoneAttrs] = defineField('phone')
        const [birthDate, birthDateAttrs] = defineField('birth_date')
        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: {
                firstName, lastName, email, roleId, isActive, phone, birthDate
            },
            attrs: {
                firstName: firstNameAttrs,
                lastName: lastNameAttrs,
                email: emailAttrs,
                roleId: roleIdAttrs,
                isActive: isActiveAttrs,
                phone: phoneAttrs,
                birthDate: birthDateAttrs
            }
        }
    }

    return {
        createUserSchema,
        updateUserSchema,
        createUserForm,
        updateUserForm
    }
}
