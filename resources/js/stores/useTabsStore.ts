import { defineStore } from 'pinia'
import { UI_CONFIG } from '@/config/ui'
import { useTabFormDataStore } from './useTabFormDataStore'

export type TemplateTab = {
    key: string
    title: string
    mode: 'create' | 'edit'
    componentName: string
    path: string
    props: Record<string, any>
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

export const useTabsStore = defineStore('tabs', {
    state: () => ({
        tabs: loaded.tabs as TemplateTab[],
        activeTab: null as TemplateTab | null,
    }),
    actions: {
        addTab(tab: TemplateTab) {
            if (this.tabs.length >= UI_CONFIG.MAX_TABS) return false
            this.tabs.push(tab)
            this.activeTab = tab
            saveToStorage(this.tabs, tab.key)
            return true
        },
        setActive(tab: TemplateTab) {
            this.activeTab = tab
            saveToStorage(this.tabs, tab.key)
        },
        clearActive() {
            this.activeTab = null
            saveToStorage(this.tabs, null)
        },
        closeTab(tab: TemplateTab) {
            const index = this.tabs.indexOf(tab)
            if (index === -1) return
            
            const formDataStore = useTabFormDataStore()
            formDataStore.clearFormData(tab.key)
            
            this.tabs.splice(index, 1)
            if (this.activeTab === tab) {
                this.activeTab = this.tabs[index - 1] || this.tabs[0] || null
            }
            saveToStorage(this.tabs, this.activeTab?.key || null)
        },
        convertToEdit(tempKey: string, newId: string, newTitle: string) {
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
            tab.path = `/document-templates/${newId}/edit`
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


