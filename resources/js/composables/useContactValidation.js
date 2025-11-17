import { useForm, defineField } from 'vee-validate'
import * as yup from 'yup'
import { ptBR } from '@/plugins/validation'

export function useContactValidation() {
    // Schema de validação para contatos
    const contactSchema = yup.object({
        type: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Tipo'))
            .oneOf(['customer', 'supplier', 'location'], 'Tipo deve ser cliente, fornecedor ou localização'),
        
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Nome da empresa'))
            .min(2, ptBR.messages.min.replace('{field}', 'Nome da empresa').replace('{length}', '2'))
            .max(255, ptBR.messages.max.replace('{field}', 'Nome da empresa').replace('{length}', '255'))
            .matches(/^[A-Za-zÀ-ÿ0-9\s\.\-\_\&\(\)]+$/, 'Nome da empresa deve conter apenas letras, números, espaços e caracteres especiais válidos'),
        
        email: yup.string()
            .nullable()
            .email(ptBR.messages.email.replace('{field}', 'E-mail'))
            .max(255, ptBR.messages.max.replace('{field}', 'E-mail').replace('{length}', '255')),
        
        phone: yup.string()
            .nullable()
            .matches(/^(\(\d{2}\)\s?|\d{2}\s?)?\d{4,5}-?\d{4}$/, 'Telefone deve ter formato válido (ex: (11) 99999-9999)')
            .max(50, ptBR.messages.max.replace('{field}', 'Telefone').replace('{length}', '50')),
        
        name_line: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Linha de nome').replace('{length}', '255')),
        
        website: yup.string()
            .nullable()
            .url('Website deve ser uma URL válida (ex: https://exemplo.com)')
            .max(255, ptBR.messages.max.replace('{field}', 'Website').replace('{length}', '255')),
        
        // Endereço de visita
        street_visiting: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Rua (visita)').replace('{length}', '255')),
        
        house_number_visiting: yup.string()
            .nullable()
            .max(50, ptBR.messages.max.replace('{field}', 'Número (visita)').replace('{length}', '50')),
        
        postal_code_visiting: yup.string()
            .nullable()
            .matches(/^\d{5}-?\d{3}$/, 'CEP deve ter formato válido (ex: 12345-678)')
            .max(20, ptBR.messages.max.replace('{field}', 'CEP (visita)').replace('{length}', '20')),
        
        city_visiting: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s\.\-]+$/, 'Cidade deve conter apenas letras, espaços e caracteres válidos')
            .max(100, ptBR.messages.max.replace('{field}', 'Cidade (visita)').replace('{length}', '100')),
        
        state_visiting: yup.string()
            .nullable()
            .matches(/^[A-Za-z]{2}$/, 'Estado deve ter 2 letras (ex: SP)')
            .max(100, ptBR.messages.max.replace('{field}', 'Estado (visita)').replace('{length}', '100')),
        
        country_visiting: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, 'País deve conter apenas letras e espaços')
            .max(100, ptBR.messages.max.replace('{field}', 'País (visita)').replace('{length}', '100')),
        
        lat_visiting: yup.number()
            .nullable()
            .min(-90, 'Latitude deve estar entre -90 e 90')
            .max(90, 'Latitude deve estar entre -90 e 90'),
        
        lng_visiting: yup.number()
            .nullable()
            .min(-180, 'Longitude deve estar entre -180 e 180')
            .max(180, 'Longitude deve estar entre -180 e 180'),
        
        // Endereço de correspondência
        street_mailing: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Rua (correspondência)').replace('{length}', '255')),
        
        house_number_mailing: yup.string()
            .nullable()
            .max(50, ptBR.messages.max.replace('{field}', 'Número (correspondência)').replace('{length}', '50')),
        
        postal_code_mailing: yup.string()
            .nullable()
            .matches(/^\d{5}-?\d{3}$/, 'CEP deve ter formato válido (ex: 12345-678)')
            .max(20, ptBR.messages.max.replace('{field}', 'CEP (correspondência)').replace('{length}', '20')),
        
        city_mailing: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s\.\-]+$/, 'Cidade deve conter apenas letras, espaços e caracteres válidos')
            .max(100, ptBR.messages.max.replace('{field}', 'Cidade (correspondência)').replace('{length}', '100')),
        
        state_mailing: yup.string()
            .nullable()
            .matches(/^[A-Za-z]{2}$/, 'Estado deve ter 2 letras (ex: SP)')
            .max(100, ptBR.messages.max.replace('{field}', 'Estado (correspondência)').replace('{length}', '100')),
        
        country_mailing: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, 'País deve conter apenas letras e espaços')
            .max(100, ptBR.messages.max.replace('{field}', 'País (correspondência)').replace('{length}', '100')),
        
        lat_mailing: yup.number()
            .nullable()
            .min(-90, 'Latitude deve estar entre -90 e 90')
            .max(90, 'Latitude deve estar entre -90 e 90'),
        
        lng_mailing: yup.number()
            .nullable()
            .min(-180, 'Longitude deve estar entre -180 e 180')
            .max(180, 'Longitude deve estar entre -180 e 180'),
        
        // Endereço de cobrança
        street_billing: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Rua (cobrança)').replace('{length}', '255')),
        
        house_number_billing: yup.string()
            .nullable()
            .max(50, ptBR.messages.max.replace('{field}', 'Número (cobrança)').replace('{length}', '50')),
        
        postal_code_billing: yup.string()
            .nullable()
            .matches(/^\d{5}-?\d{3}$/, 'CEP deve ter formato válido (ex: 12345-678)')
            .max(20, ptBR.messages.max.replace('{field}', 'CEP (cobrança)').replace('{length}', '20')),
        
        city_billing: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s\.\-]+$/, 'Cidade deve conter apenas letras, espaços e caracteres válidos')
            .max(100, ptBR.messages.max.replace('{field}', 'Cidade (cobrança)').replace('{length}', '100')),
        
        state_billing: yup.string()
            .nullable()
            .matches(/^[A-Za-z]{2}$/, 'Estado deve ter 2 letras (ex: SP)')
            .max(100, ptBR.messages.max.replace('{field}', 'Estado (cobrança)').replace('{length}', '100')),
        
        country_billing: yup.string()
            .nullable()
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, 'País deve conter apenas letras e espaços')
            .max(100, ptBR.messages.max.replace('{field}', 'País (cobrança)').replace('{length}', '100')),
        
        lat_billing: yup.number()
            .nullable()
            .min(-90, 'Latitude deve estar entre -90 e 90')
            .max(90, 'Latitude deve estar entre -90 e 90'),
        
        lng_billing: yup.number()
            .nullable()
            .min(-180, 'Longitude deve estar entre -180 e 180')
            .max(180, 'Longitude deve estar entre -180 e 180'),
        
        general_notes: yup.string()
            .nullable()
    })

    // Schema de validação para pessoas de contato
    const contactPersonSchema = yup.object({
        first_name: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Primeiro nome'))
            .min(2, ptBR.messages.min.replace('{field}', 'Primeiro nome').replace('{length}', '2'))
            .max(255, ptBR.messages.max.replace('{field}', 'Primeiro nome').replace('{length}', '255'))
            .matches(/^[A-Za-zÀ-ÿ\s]+$/, ptBR.messages.alpha_spaces.replace('{field}', 'Primeiro nome')),
        
        last_name: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Sobrenome').replace('{length}', '255'))
            .matches(/^[A-Za-zÀ-ÿ\s]*$/, ptBR.messages.alpha_spaces.replace('{field}', 'Sobrenome')),
        
        mobile: yup.string()
            .nullable()
            .matches(/^(\(\d{2}\)\s?|\d{2}\s?)?\d{4,5}-?\d{4}$/, 'Celular deve ter formato válido (ex: (11) 99999-9999)')
            .max(50, ptBR.messages.max.replace('{field}', 'Celular').replace('{length}', '50')),
        
        role: yup.string()
            .nullable()
            .max(255, ptBR.messages.max.replace('{field}', 'Cargo').replace('{length}', '255')),
        
        emails: yup.array()
            .of(
                yup.string()
                    .email(ptBR.messages.email.replace('{field}', 'E-mail'))
                    .max(255, ptBR.messages.max.replace('{field}', 'E-mail').replace('{length}', '255'))
            )
    })

    // Schema de validação para notas de pessoa de contato
    const contactPersonNoteSchema = yup.object({
        name: yup.string()
            .required(ptBR.messages.required.replace('{field}', 'Nome da nota'))
            .min(3, ptBR.messages.min.replace('{field}', 'Nome da nota').replace('{length}', '3'))
            .max(255, ptBR.messages.max.replace('{field}', 'Nome da nota').replace('{length}', '255')),
        
        content: yup.string()
            .nullable(),
        
        note_date: yup.date()
            .nullable()
            .max(new Date(), 'A data da nota não pode ser futura')
    })

    function createContactForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: contactSchema,
            initialValues: {
                type: 'customer',
                name: '',
                email: '',
                phone: '',
                name_line: '',
                website: '',
                street_visiting: '',
                house_number_visiting: '',
                postal_code_visiting: '',
                city_visiting: '',
                state_visiting: '',
                country_visiting: '',
                lat_visiting: null,
                lng_visiting: null,
                street_mailing: '',
                house_number_mailing: '',
                postal_code_mailing: '',
                city_mailing: '',
                state_mailing: '',
                country_mailing: '',
                lat_mailing: null,
                lng_mailing: null,
                street_billing: '',
                house_number_billing: '',
                postal_code_billing: '',
                city_billing: '',
                state_billing: '',
                country_billing: '',
                lat_billing: null,
                lng_billing: null,
                general_notes: '',
                ...initialValues
            }
        })

        const [type, typeAttrs] = defineField('type')
        const [name, nameAttrs] = defineField('name')
        const [email, emailAttrs] = defineField('email')
        const [phone, phoneAttrs] = defineField('phone')
        const [nameLine, nameLineAttrs] = defineField('name_line')
        const [website, websiteAttrs] = defineField('website')
        
        // Endereço de visita
        const [streetVisiting, streetVisitingAttrs] = defineField('street_visiting')
        const [houseNumberVisiting, houseNumberVisitingAttrs] = defineField('house_number_visiting')
        const [postalCodeVisiting, postalCodeVisitingAttrs] = defineField('postal_code_visiting')
        const [cityVisiting, cityVisitingAttrs] = defineField('city_visiting')
        const [stateVisiting, stateVisitingAttrs] = defineField('state_visiting')
        const [countryVisiting, countryVisitingAttrs] = defineField('country_visiting')
        
        // Endereço de correspondência
        const [streetMailing, streetMailingAttrs] = defineField('street_mailing')
        const [houseNumberMailing, houseNumberMailingAttrs] = defineField('house_number_mailing')
        const [postalCodeMailing, postalCodeMailingAttrs] = defineField('postal_code_mailing')
        const [cityMailing, cityMailingAttrs] = defineField('city_mailing')
        const [stateMailing, stateMailingAttrs] = defineField('state_mailing')
        const [countryMailing, countryMailingAttrs] = defineField('country_mailing')
        
        // Endereço de cobrança
        const [streetBilling, streetBillingAttrs] = defineField('street_billing')
        const [houseNumberBilling, houseNumberBillingAttrs] = defineField('house_number_billing')
        const [postalCodeBilling, postalCodeBillingAttrs] = defineField('postal_code_billing')
        const [cityBilling, cityBillingAttrs] = defineField('city_billing')
        const [stateBilling, stateBillingAttrs] = defineField('state_billing')
        const [countryBilling, countryBillingAttrs] = defineField('country_billing')
        
        const [generalNotes, generalNotesAttrs] = defineField('general_notes')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: {
                type, name, email, phone, nameLine, website,
                streetVisiting, houseNumberVisiting, postalCodeVisiting, cityVisiting, stateVisiting, countryVisiting,
                streetMailing, houseNumberMailing, postalCodeMailing, cityMailing, stateMailing, countryMailing,
                streetBilling, houseNumberBilling, postalCodeBilling, cityBilling, stateBilling, countryBilling,
                generalNotes
            },
            attrs: {
                type: typeAttrs, name: nameAttrs, email: emailAttrs, phone: phoneAttrs, 
                nameLine: nameLineAttrs, website: websiteAttrs,
                streetVisiting: streetVisitingAttrs, houseNumberVisiting: houseNumberVisitingAttrs,
                postalCodeVisiting: postalCodeVisitingAttrs, cityVisiting: cityVisitingAttrs,
                stateVisiting: stateVisitingAttrs, countryVisiting: countryVisitingAttrs,
                streetMailing: streetMailingAttrs, houseNumberMailing: houseNumberMailingAttrs,
                postalCodeMailing: postalCodeMailingAttrs, cityMailing: cityMailingAttrs,
                stateMailing: stateMailingAttrs, countryMailing: countryMailingAttrs,
                streetBilling: streetBillingAttrs, houseNumberBilling: houseNumberBillingAttrs,
                postalCodeBilling: postalCodeBillingAttrs, cityBilling: cityBillingAttrs,
                stateBilling: stateBillingAttrs, countryBilling: countryBillingAttrs,
                generalNotes: generalNotesAttrs
            }
        }
    }

    function createContactPersonForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: contactPersonSchema,
            initialValues: {
                first_name: '',
                last_name: '',
                mobile: '',
                role: '',
                emails: [''],
                ...initialValues
            }
        })

        const [firstName, firstNameAttrs] = defineField('first_name')
        const [lastName, lastNameAttrs] = defineField('last_name')
        const [mobile, mobileAttrs] = defineField('mobile')
        const [role, roleAttrs] = defineField('role')
        const [emails, emailsAttrs] = defineField('emails')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { firstName, lastName, mobile, role, emails },
            attrs: {
                firstName: firstNameAttrs,
                lastName: lastNameAttrs,
                mobile: mobileAttrs,
                role: roleAttrs,
                emails: emailsAttrs
            }
        }
    }

    function createContactPersonNoteForm(initialValues = {}) {
        const { errors, handleSubmit, isSubmitting } = useForm({
            validationSchema: contactPersonNoteSchema,
            initialValues: {
                name: '',
                content: '',
                note_date: new Date().toISOString().split('T')[0],
                ...initialValues
            }
        })

        const [name, nameAttrs] = defineField('name')
        const [content, contentAttrs] = defineField('content')
        const [noteDate, noteDateAttrs] = defineField('note_date')

        return {
            errors,
            isSubmitting,
            handleSubmit,
            fields: { name, content, noteDate },
            attrs: {
                name: nameAttrs,
                content: contentAttrs,
                noteDate: noteDateAttrs
            }
        }
    }

    return {
        contactSchema,
        contactPersonSchema,
        contactPersonNoteSchema,
        createContactForm,
        createContactPersonForm,
        createContactPersonNoteForm
    }
}