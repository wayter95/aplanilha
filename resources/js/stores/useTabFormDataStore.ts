import { defineStore } from 'pinia'

export type FormData = {
    type: string
    name: string
    language: string
    country: string
    status: string
    is_default: boolean
    header_html: string
    content_html: string
    footer_html: string
    background_image_path: string
}

const STORAGE_KEY = 'tab-form-data-store'

const loadFromStorage = (): Record<string, FormData> => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY)
        if (stored) {
            return JSON.parse(stored)
        }
    } catch (error) {
        console.warn('Erro ao carregar dados do localStorage:', error)
    }
    return {}
}

const saveToStorage = (data: Record<string, FormData>) => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
    } catch (error) {
        console.warn('Erro ao salvar dados no localStorage:', error)
    }
}

export const useTabFormDataStore = defineStore('tabFormData', {
    state: () => ({
        forms: loadFromStorage(),
    }),
    getters: {
        getFormData: (state) => (tabKey: string): FormData | null => {
            return state.forms[tabKey] || null
        },
    },
    actions: {
        setFormData(tabKey: string, data: Partial<FormData>) {
            if (!this.forms[tabKey]) {
                this.forms[tabKey] = {
                    type: 'contract',
                    name: '',
                    language: '',
                    country: '',
                    status: 'active',
                    is_default: false,
                    header_html: '',
                    content_html: '',
                    footer_html: '',
                    background_image_path: '',
                }
            }
            this.forms[tabKey] = { ...this.forms[tabKey], ...data }
            console.log('useTabFormDataStore: Salvando dados no localStorage para tabKey:', tabKey, this.forms[tabKey])
            saveToStorage(this.forms)
            // Verifica se salvou corretamente
            const saved = loadFromStorage()
            console.log('useTabFormDataStore: Dados salvos verificados:', saved[tabKey])
        },
        clearFormData(tabKey: string) {
            delete this.forms[tabKey]
            saveToStorage(this.forms)
        },
        initializeFormData(tabKey: string, initialData?: Partial<FormData>) {
            // Só inicializa se realmente não existe dados salvos
            // Verifica tanto na store quanto no localStorage para evitar sobrescrever
            const existingInStore = this.forms[tabKey]
            const existingInStorage = loadFromStorage()[tabKey]
            
            if (!existingInStore && !existingInStorage) {
                this.forms[tabKey] = {
                    type: initialData?.type || 'contract',
                    name: initialData?.name || '',
                    language: initialData?.language || '',
                    country: initialData?.country || '',
                    status: initialData?.status || 'active',
                    is_default: initialData?.is_default || false,
                    header_html: initialData?.header_html || '',
                    content_html: initialData?.content_html || '',
                    footer_html: initialData?.footer_html || '',
                    background_image_path: initialData?.background_image_path || '',
                }
                saveToStorage(this.forms)
            }
        },
    },
})

