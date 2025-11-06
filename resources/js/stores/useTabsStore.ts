import { defineStore } from 'pinia'
import { UI_CONFIG } from '@/config/ui'
import { useTabFormDataStore } from './useTabFormDataStore'

export type Tab = {
    key: string
    title: string
    mode?: 'create' | 'edit'
    componentName: string
    path: string
    props?: Record<string, any>
    context?: string
    metadata?: Record<string, any>
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

const STORAGE_KEY = 'tabs-store'

const loadFromStorage = () => {
    try {
        const stored = localStorage.getItem(STORAGE_KEY)
        if (stored) {
            const data = JSON.parse(stored)
            return {
                tabs: data.tabs || [],
                activeTabKey: data.activeTabKey || null
            }
        }
    } catch (error) {
        console.warn('Erro ao carregar tabs do localStorage:', error)
    }
    return { tabs: [], activeTabKey: null }
}

const saveToStorage = (tabs: TemplateTab[], activeTabKey: string | null) => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ tabs, activeTabKey }))
    } catch (error) {
        console.warn('Erro ao salvar tabs no localStorage:', error)
    }
}

const loaded = loadFromStorage()

// Hooks são armazenados fora do state para evitar problemas de serialização
const hooksRegistry = new Map<string, TabHook>()

export const useTabsStore = defineStore('tabs', {
    state: () => ({
        tabs: loaded.tabs as TemplateTab[],
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
        
        addTab(tab: TemplateTab) {
            if (this.tabs.length >= UI_CONFIG.MAX_TABS) return false
            this.tabs.push(tab)
            this.activeTab = tab
            saveToStorage(this.tabs, tab.key)
            
            // Chama hook afterCreate se existir
            const hook = hooksRegistry.get(tab.componentName)
            if (hook?.afterCreate) {
                hook.afterCreate(tab)
            }
            
            return true
        },
        
        setActive(tab: TemplateTab) {
            // Chama hook onDeactivate se existir
            if (this.activeTab) {
                const oldHook = hooksRegistry.get(this.activeTab.componentName)
                if (oldHook?.onDeactivate) {
                    oldHook.onDeactivate(this.activeTab)
                }
            }
            
            this.activeTab = tab
            saveToStorage(this.tabs, tab.key)
            
            // Chama hook onActivate se existir
            const hook = hooksRegistry.get(tab.componentName)
            if (hook?.onActivate) {
                hook.onActivate(tab)
            }
        },
        clearActive() {
            this.activeTab = null
            saveToStorage(this.tabs, null)
        },
        async closeTab(tab: TemplateTab) {
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
            const formDataStore = useTabFormDataStore()
            formDataStore.clearFormData(tab.key)
            
            this.tabs.splice(index, 1)
            
            // Se a tab fechada era a ativa, tenta ativar outra ou limpa
            if (wasActive) {
                // Tenta ativar a tab anterior ou a primeira disponível
                const newActiveIndex = index > 0 ? index - 1 : 0
                const newActiveTab = this.tabs[newActiveIndex] || this.tabs[0] || null
                if (newActiveTab) {
                    this.setActive(newActiveTab)
                } else {
                    this.activeTab = null
                }
            }
            
            saveToStorage(this.tabs, this.activeTab?.key || null)
            return true
        },
        convertToEdit(tempKey: string, newId: string, newTitle: string, context?: string) {
            const tab = this.tabs.find(t => t.key === tempKey)
            if (!tab) return
            
            const formDataStore = useTabFormDataStore()
            const oldData = formDataStore.getFormData(tempKey)
            
            if (oldData) {
                formDataStore.setFormData(newId, oldData)
                formDataStore.clearFormData(tempKey)
            }
            
            tab.key = newId
            tab.title = newTitle
            tab.mode = 'edit'
            
            const basePath = context === 'document-types' 
                ? `/document-types/${newId}/edit`
                : `/document-templates/${newId}/edit`
            
            tab.path = basePath
            tab.props = { ...(tab.props || {}), mode: 'edit', id: newId }
            this.activeTab = tab
            saveToStorage(this.tabs, newId)
        },
        syncFromStorage() {
            // Sincroniza activeTab após carregar do localStorage
            const stored = loadFromStorage()
            if (stored.activeTabKey) {
                const tab = this.tabs.find(t => t.key === stored.activeTabKey)
                if (tab) {
                    this.activeTab = tab
                } else {
                    this.activeTab = null
                    saveToStorage(this.tabs, null)
                }
            }
        },
    },
})


