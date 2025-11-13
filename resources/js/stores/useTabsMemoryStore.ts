import { defineStore } from 'pinia'
import { UI_CONFIG } from '@/config/ui'
import { useTabFormMemoryStore } from './useTabFormMemoryStore'

export type Tab = {
    key: string
    title: string
    mode?: 'create' | 'edit'
    componentName: string
    path: string
    props?: Record<string, any>
    context?: string
    metadata?: Record<string, any>
    isModified?: boolean  // Indica se a tab possui alterações não salvas
    color?: string        // Cor do template (para a bolinha)
}

export type TemplateTab = Tab

/**
 * Hooks para eventos de tab
 */
export type TabHook = {
    /** Chamado antes de fechar uma tab. Retorna false para cancelar. */
    beforeClose?: (tab: Tab) => boolean | Promise<boolean>
    
    /** Chamado após criar uma tab */
    afterCreate?: (tab: Tab) => void
    
    /** Chamado ao ativar uma tab */
    onActivate?: (tab: Tab) => void
    
    /** Chamado ao desativar uma tab */
    onDeactivate?: (tab: Tab) => void
}

// Hooks são armazenados fora do state para evitar problemas de serialização
const hooksRegistry = new Map<string, TabHook>()

/**
 * Store de Tabs 100% EM MEMÓRIA
 * 
 * COMPORTAMENTO:
 * - Não persiste dados em localStorage/sessionStorage
 * - Todo estado é perdido após F5
 * - Mantém estado apenas durante navegação entre abas (ciclo de vida do componente)
 * - Estado reinicia quando app é remontado
 */
export const useTabsMemoryStore = defineStore('tabsMemory', {
    state: () => ({
        tabs: [] as TemplateTab[],
        activeTab: null as TemplateTab | null,
    }),
    
    actions: {
        /**
         * Registra hooks para um componente
         * 
         * @param componentName - Nome do componente
         * @param hooks - Objeto com hooks
         */
        registerHooks(componentName: string, hooks: TabHook) {
            hooksRegistry.set(componentName, hooks)
        },
        
        /**
         * Remove hooks de um componente
         * 
         * @param componentName - Nome do componente
         */
        unregisterHooks(componentName: string) {
            hooksRegistry.delete(componentName)
        },
        
        /**
         * Obtém hooks de um componente
         * 
         * @param componentName - Nome do componente
         * @returns Hooks ou undefined
         */
        getHooks(componentName: string): TabHook | undefined {
            return hooksRegistry.get(componentName)
        },
        
        /**
         * Adiciona uma nova tab
         * Retorna false se atingiu o limite de tabs
         */
        addTab(tab: TemplateTab): boolean {
            // Verifica se já existe
            const existingTab = this.tabs.find(t => t.key === tab.key)
            if (existingTab) {
                this.setActive(existingTab)
                return true
            }
            
            // Verifica limite
            if (this.tabs.length >= UI_CONFIG.MAX_TABS) {
                console.warn(`[TabsMemory] Limite de ${UI_CONFIG.MAX_TABS} tabs atingido`)
                return false
            }
            
            // Adiciona tab
            this.tabs.push(tab)
            this.activeTab = tab
            
            // Chama hook afterCreate se existir
            const hook = hooksRegistry.get(tab.componentName)
            if (hook?.afterCreate) {
                hook.afterCreate(tab)
            }
            
            return true
        },
        
        /**
         * Marca uma tab como modificada
         * 
         * @param tabKey - Chave da tab
         */
        markAsModified(tabKey: string) {
            const tab = this.tabs.find(t => t.key === tabKey)
            if (tab) {
                tab.isModified = true
            }
        },
        
        /**
         * Marca uma tab como não modificada (limpa)
         * Usado após salvar com sucesso
         * 
         * @param tabKey - Chave da tab
         */
        markAsClean(tabKey: string) {
            const tab = this.tabs.find(t => t.key === tabKey)
            if (tab) {
                tab.isModified = false
            }
        },
        
        /**
         * Verifica se uma tab possui modificações
         * 
         * @param tabKey - Chave da tab
         * @returns true se modificada
         */
        isTabModified(tabKey: string): boolean {
            const tab = this.tabs.find(t => t.key === tabKey)
            return tab?.isModified || false
        },
        
        /**
         * Define uma tab como ativa
         */
        setActive(tab: TemplateTab) {
            // Chama hook onDeactivate na tab antiga
            if (this.activeTab) {
                const oldHook = hooksRegistry.get(this.activeTab.componentName)
                if (oldHook?.onDeactivate) {
                    oldHook.onDeactivate(this.activeTab)
                }
            }
            
            this.activeTab = tab
            
            // Chama hook onActivate na nova tab
            const hook = hooksRegistry.get(tab.componentName)
            if (hook?.onActivate) {
                hook.onActivate(tab)
            }
        },
        
        /**
         * Limpa tab ativa
         */
        clearActive() {
            this.activeTab = null
        },
        
        /**
         * Fecha uma tab
         * Retorna true se fechou com sucesso
         */
        async closeTab(tab: TemplateTab): Promise<boolean> {
            const index = this.tabs.indexOf(tab)
            if (index === -1) return false
            
            // Chama hook beforeClose se existir
            const hook = hooksRegistry.get(tab.componentName)
            if (hook?.beforeClose) {
                const canClose = await hook.beforeClose(tab)
                if (!canClose) {
                    return false // Cancela o fechamento
                }
            }
            
            const wasActive = this.activeTab?.key === tab.key
            
            // Limpa dados do formulário desta tab
            const formDataStore = useTabFormMemoryStore()
            formDataStore.clearFormData(tab.key)
            
            // Remove tab
            this.tabs.splice(index, 1)
            
            // Se a tab fechada era a ativa, tenta ativar outra
            if (wasActive) {
                const newActiveIndex = index > 0 ? index - 1 : 0
                const newActiveTab = this.tabs[newActiveIndex] || this.tabs[0] || null
                if (newActiveTab) {
                    this.setActive(newActiveTab)
                } else {
                    this.activeTab = null
                }
            }
            
            return true
        },
        
        /**
         * Converte uma tab temporária (create) para edit após salvar
         */
        convertToEdit(tempKey: string, newId: string, newTitle: string, context?: string) {
            const tab = this.tabs.find(t => t.key === tempKey)
            if (!tab) return
            
            // Transfere dados do form
            const formDataStore = useTabFormMemoryStore()
            const oldData = formDataStore.getFormData(tempKey)
            
            if (oldData) {
                formDataStore.setFormData(newId, oldData)
                formDataStore.clearFormData(tempKey)
            }
            
            // Atualiza tab
            tab.key = newId
            tab.title = newTitle
            tab.mode = 'edit'
            
            const basePath = context === 'document-types' 
                ? `/document-types/${newId}/edit`
                : context === 'document-templates'
                ? `/document-templates/${newId}/edit`
                : `/projects/types/${newId}/edit`
            
            tab.path = basePath
            tab.props = { ...(tab.props || {}), mode: 'edit', id: newId }
            this.activeTab = tab
        },
        
        /**
         * Limpa todas as tabs
         * Útil para reset completo (não é necessário na prática, pois F5 já limpa tudo)
         */
        clearAll() {
            this.tabs = []
            this.activeTab = null
            hooksRegistry.clear()
        },
    },
})
