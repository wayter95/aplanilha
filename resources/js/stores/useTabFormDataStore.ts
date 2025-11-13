import { defineStore } from 'pinia'

/**
 * Tipo genérico para dados de formulário
 * Permite qualquer estrutura de dados, não apenas DocumentTemplate
 */
export type FormData = Record<string, any>

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
        /**
         * Define dados do formulário para uma tab
         * Aceita qualquer estrutura de dados (genérico)
         * 
         * @param tabKey - Chave única da tab
         * @param data - Dados do formulário (qualquer estrutura)
         */
        setFormData(tabKey: string, data: Partial<FormData>) {
            // Se não existe, inicializa com objeto vazio
            if (!this.forms[tabKey]) {
                this.forms[tabKey] = {}
            }
            // Merge com dados existentes
            this.forms[tabKey] = { ...this.forms[tabKey], ...data }
            saveToStorage(this.forms)
        },
        
        /**
         * Limpa dados do formulário de uma tab
         * 
         * @param tabKey - Chave única da tab
         */
        clearFormData(tabKey: string) {
            delete this.forms[tabKey]
            saveToStorage(this.forms)
        },
        
        /**
         * Remove dados do formulário de uma tab (alias para clearFormData)
         * 
         * @param tabKey - Chave única da tab
         */
        removeFormData(tabKey: string) {
            this.clearFormData(tabKey)
        },
        
        /**
         * Inicializa dados do formulário apenas se não existirem
         * 
         * @param tabKey - Chave única da tab
         * @param initialData - Dados iniciais (opcional)
         */
        initializeFormData(tabKey: string, initialData?: Partial<FormData>) {
            const existingInStore = this.forms[tabKey]
            const existingInStorage = loadFromStorage()[tabKey]
            
            // Só inicializa se não existir dados
            if (!existingInStore && !existingInStorage) {
                this.forms[tabKey] = initialData ? { ...initialData } : {}
                saveToStorage(this.forms)
            }
        },
        
        /**
         * Limpa todos os dados de formulário
         */
        clearAllFormData() {
            this.forms = {}
            saveToStorage(this.forms)
        },
    },
})

