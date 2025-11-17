<template>
    <AppLayout :title="'Modelos de Documentos'" :description="'Gerencie templates por tipo'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Modelos de Documentos</h2>
            <div class="flex gap-2">
                <button @click="openCreate()" class="ti-btn btn-wave ti-btn-primary-full !py-2 !px-3">Novo Modelo</button>
                <button :disabled="!selectedIds.length" @click="bulkDelete" class="ti-btn btn-wave ti-btn-danger-full disabled:opacity-50 !py-2 !px-3">Remover Selecionados</button>
            </div>
        </div>

        <div v-for="group in grouped" :key="group.type" class="mb-3">
            <Accordion :title="labelOf(group.type)" :count="group.items.length" :open="isOpen(group.type)" @toggle="toggle(group.type)">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm table-auto">
                        <thead>
                            <tr class="text-left text-gray-600 border-b">
                                <th class="w-8"><input type="checkbox" :checked="allSelected(group.items)" @change="toggleAll(group.items, $event.target.checked)" /></th>
                                <th>Nome</th>
                                <th>Idioma</th>
                                <th>País</th>
                                <th>Status</th>
                                <th>Sistema</th>
                                <th>Padrão</th>
                                <th class="text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in group.items" :key="item.id" class="border-b">
                                <td class="w-8"><input type="checkbox" :value="item.id" v-model="selectedIds" /></td>
                                <td>{{ item.name }}</td>
                                <td>{{ item.language || '-' }}</td>
                                <td>{{ item.country || '-' }}</td>
                                <td>
                                    <span :class="item.status === 'active' ? 'text-green-600' : 'text-gray-500'">{{ item.status }}</span>
                                </td>
                                <td>{{ item.is_system ? 'Sim' : 'Não' }}</td>
                                <td>
                                    <span v-if="item.is_default" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Padrão</span>
                                </td>
                                <td class="text-right space-x-2">
                                    <button class="ti-btn ti-btn-outline-primary !py-1 !px-2 !text-[0.75rem]" @click="edit(item)">Editar</button>
                                    <button class="ti-btn ti-btn-outline-secondary !py-1 !px-2 !text-[0.75rem]" @click="duplicate(item)">Duplicar</button>
                                    <button class="ti-btn ti-btn-outline-warning !py-1 !px-2 !text-[0.75rem]" @click="setDefault(item)">Padrão</button>
                                    <button class="ti-btn ti-btn-outline-danger !py-1 !px-2 !text-[0.75rem]" @click="remove(item)">Remover</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Accordion>
        </div>
      </div>
    </div>
    </div>

        
        <ToastContainer />
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import Accordion from '@/Components/Accordion.vue'
import ToastContainer from '@/Components/ToastContainer.vue'
import { useToast } from '@/composables/useToast'
import { useDocumentTemplates } from '@/composables/useDocumentTemplates'

export default {
    components: { AppLayout, ToastContainer, Accordion },
    setup() {
        const toast = useToast()
        const {
            loading,
            types,
            itemsByType,
            grouped,
            fetchAll,
            setDefault: apiSetDefault,
            remove: apiRemove,
            duplicate: apiDuplicate,
            bulkDelete: apiBulkDelete
        } = useDocumentTemplates()

        return {
            toast,
            loading,
            types,
            itemsByType,
            grouped,
            fetchAll,
            apiSetDefault,
            apiRemove,
            apiDuplicate,
            apiBulkDelete
        }
    },
    data() {
        return {
            openTypes: {},
            selectedIds: []
        }
    },
    created() {
        this.loadData()
    },
    methods: {
        labelOf(t) {
            if (t === 'contract') return 'Contratos'
            if (t === 'invoice') return 'Faturas'
            if (t === 'quote') return 'Orçamentos'
            return t
        },
        async loadData() {
            try {
                await this.fetchAll()
                // Abrir todos os accordions após carregar
                this.types.forEach(t => this.$set(this.openTypes, t, true))
            } catch (e) {
                this.toast.error('Erro ao carregar templates')
                console.error(e)
            }
        },
        isOpen(type) {
            return !!this.openTypes[type]
        },
        toggle(type) {
            this.$set(this.openTypes, type, !this.openTypes[type])
        },
        allSelected(items) {
            if (!items.length) return false
            return items.every(i => this.selectedIds.includes(i.id))
        },
        toggleAll(items, checked) {
            const ids = items.map(i => i.id)
            if (checked) {
                this.selectedIds = Array.from(new Set([...this.selectedIds, ...ids]))
            } else {
                this.selectedIds = this.selectedIds.filter(id => !ids.includes(id))
            }
        },
        openCreate() {
            this.$inertia.visit('/document-templates/new')
        },
        edit(item) {
            this.$inertia.visit(`/document-templates/${item.id}/edit`)
        },
        
        async setDefault(item) {
            try {
                await this.apiSetDefault(item.id, item.type)
                this.toast.success('Modelo definido como padrão')
            } catch (e) {
                this.toast.error('Erro ao definir modelo como padrão')
                console.error(e)
            }
        },
        async remove(item) {
            try {
                await this.apiRemove(item.id, item.type)
                this.selectedIds = this.selectedIds.filter(id => id !== item.id)
                this.toast.success('Modelo removido')
            } catch (e) {
                this.toast.error('Erro ao remover modelo')
                console.error(e)
            }
        },
        async duplicate(item) {
            try {
                await this.apiDuplicate(item)
                this.toast.success('Modelo duplicado')
            } catch (e) {
                this.toast.error('Erro ao duplicar modelo')
                console.error(e)
            }
        },
        async bulkDelete() {
            if (!this.selectedIds.length) return
            
            try {
                await this.apiBulkDelete(this.selectedIds)
                this.selectedIds = []
                this.toast.success('Modelos removidos')
            } catch (e) {
                this.toast.error('Erro ao remover modelos')
                console.error(e)
            }
        }
    }
}
</script>

<style scoped>
.disabled\:opacity-50:disabled { opacity: 0.5; }
</style>


