import { defineStore } from 'pinia'

/**
 * Tipo genérico para dados de formulário
 * Permite qualquer estrutura de dados
 */
export type FormData = Record<string, any>

/**
 * Store de Dados de Formulário 100% EM MEMÓRIA
 * 
 * COMPORTAMENTO:
 * - Não persiste dados em localStorage/sessionStorage
 * - Mantém dados dos formulários apenas em memória durante navegação entre tabs
 * - Todo estado é perdido após F5
 * - Quando o usuário troca de tab, os dados do formulário são preservados
 * - Ao fechar a tab, os dados são limpos automaticamente
 * 
 * CASO DE USO:
 * 1. Usuário abre "Novo Tipo de Projeto" (tab A)
 * 2. Preenche campos: título = "Web", cor = "#ff0000"
 * 3. Abre outra tab (tab B)
 * 4. Volta para tab A → dados devem estar preservados
 * 5. Fecha tab A → dados são limpos
 * 6. F5 na página → TUDO É PERDIDO
 */
export const useTabFormMemoryStore = defineStore('tabFormMemory', {
    state: () => ({
        /**
         * Armazena dados de formulário por chave de tab
         * Estrutura: { [tabKey]: { field1: value1, field2: value2, ... } }
         */
        forms: {} as Record<string, FormData>,
    }),
    
    getters: {
        /**
         * Obtém dados de um formulário
         * 
         * @param tabKey - Chave única da tab
         * @returns Dados do formulário ou null
         */
        getFormData: (state) => (tabKey: string): FormData | null => {
            return state.forms[tabKey] || null
        },
    },
    
    actions: {
        /**
         * Define dados do formulário para uma tab
         * 
         * @param tabKey - Chave única da tab
         * @param data - Dados parciais do formulário (faz merge com existentes)
         */
        setFormData(tabKey: string, data: Partial<FormData>) {
            // Se não existe, inicializa com objeto vazio
            if (!this.forms[tabKey]) {
                this.forms[tabKey] = {}
            }
            
            // Merge com dados existentes
            this.forms[tabKey] = { 
                ...this.forms[tabKey], 
                ...data 
            }
        },
        
        /**
         * Limpa dados do formulário de uma tab
         * Chamado automaticamente ao fechar uma tab
         * 
         * @param tabKey - Chave única da tab
         */
        clearFormData(tabKey: string) {
            delete this.forms[tabKey]
        },
        
        /**
         * Remove dados do formulário (alias para clearFormData)
         * 
         * @param tabKey - Chave única da tab
         */
        removeFormData(tabKey: string) {
            this.clearFormData(tabKey)
        },
        
        /**
         * Inicializa dados do formulário apenas se não existirem
         * Útil para definir valores padrão sem sobrescrever dados existentes
         * 
         * @param tabKey - Chave única da tab
         * @param initialData - Dados iniciais (opcional)
         */
        initializeFormData(tabKey: string, initialData?: Partial<FormData>) {
            // Só inicializa se não existir
            if (!this.forms[tabKey]) {
                this.forms[tabKey] = initialData ? { ...initialData } : {}
            }
        },
        
        /**
         * Verifica se existem dados para uma tab
         * 
         * @param tabKey - Chave única da tab
         * @returns true se existem dados
         */
        hasFormData(tabKey: string): boolean {
            return !!this.forms[tabKey] && Object.keys(this.forms[tabKey]).length > 0
        },
        
        /**
         * Limpa todos os dados de formulário
         * Útil para reset completo (não é necessário na prática, pois F5 já limpa tudo)
         */
        clearAll() {
            this.forms = {}
        },
    },
})
