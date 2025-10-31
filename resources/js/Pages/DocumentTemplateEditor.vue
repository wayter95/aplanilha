<template>
    <AppLayout :title="'Editor de Modelo'" :description="'Edite layout, fontes e plano de fundo'" :user="user">
    <div class="grid grid-cols-12 gap-6 p-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Editor de Modelo</h2>
            <div class="flex gap-2">
                <button class="px-3 py-2 border rounded" @click="goBack">Voltar</button>
                <button class="px-3 py-2 border rounded" @click="exportPdf">Exportar PDF</button>
                <button class="px-3 py-2 bg-blue-600 text-white rounded" @click="save">Salvar</button>
            </div>
        </div>
        <div class="mb-4 border-b">
            <nav class="flex gap-4">
                <button :class="tabClass('dados')" @click="activeTab='dados'">Dados</button>
                <button :class="tabClass('layout')" @click="activeTab='layout'">Layout</button>
                <button :class="tabClass('html')" @click="activeTab='html'">Conteúdo</button>
                <button :class="tabClass('preview')" @click="activeTab='preview'">Pré-visualização</button>
            </nav>
        </div>

        <div v-if="activeTab==='dados'" class="grid grid-cols-2 gap-6 bg-white dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10 p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <BaseSelect name="type" label="Tipo" :options="typeOptions" v-model="form.type" />
                    <BaseInput name="name" label="Nome" rules="required" v-model="form.name" />
                    <BaseInput name="language" label="Idioma" v-model="form.language" />
                    <BaseInput name="country" label="País" v-model="form.country" />
                    <BaseSelect name="status" label="Status" :options="statusOptions" v-model="form.status" />
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.is_default" /> Definir como padrão</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Plano de fundo (URL)</label>
                    <input v-model="form.background_image_path" type="text" class="w-full border rounded px-2 py-1" placeholder="https://..." />
                </div>
            </div>

            <div v-if="activeTab==='preview'" class="bg-white dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10 p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600">Pré-visualização</div>
                    <div class="flex items-center gap-3">
                        <button class="px-2 py-1 text-sm border rounded" @click="fetchPreviewHtml">Atualizar</button>
                        <div class="text-xs text-gray-500">Placeholders: ${name}, ${current_date}</div>
                    </div>
                </div>
                <div class="border rounded overflow-hidden">
                    <iframe v-if="serverHtml" :srcdoc="serverHtml" class="w-full" style="height: 1200px;"></iframe>
                    <div v-else class="p-6 text-sm text-gray-500">Salve o modelo para visualizar ou clique em Atualizar</div>
                </div>
            </div>
        </div>

        <div v-if="activeTab==='layout'" class="grid grid-cols-2 gap-6 bg-white dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10 p-6">
            <div>
                <label class="block text-sm mb-1">Fonts JSON</label>
                <textarea v-model="fontsJsonText" rows="6" class="w-full border rounded px-2 py-1 font-mono" placeholder='{"family":"Inter","size":12,"color":"#111"}'></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Layout JSON</label>
                <textarea v-model="layoutJsonText" rows="6" class="w-full border rounded px-2 py-1 font-mono" placeholder='{"contentAlign":"left"}'></textarea>
            </div>
        </div>

        <div v-if="activeTab==='html'" class="grid grid-cols-1 gap-4 bg-white dark:bg-bgdark rounded-md border border-default-200 dark:border-white/10 p-6">
            <div>
                <label class="block text-sm mb-1">Cabeçalho (HTML)</label>
                <textarea v-model="form.header_html" rows="3" class="w-full border rounded px-2 py-1" placeholder="<h1>Título</h1>"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Conteúdo (HTML)</label>
                <textarea v-model="form.content_html" rows="8" class="w-full border rounded px-2 py-1" placeholder="<p>Olá ${name}, ${current_date}</p>"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1">Rodapé (HTML)</label>
                <textarea v-model="form.footer_html" rows="3" class="w-full border rounded px-2 py-1"></textarea>
            </div>
        </div>
      </div>
    </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import BaseInput from '@/Components/Form/BaseInput.vue'
import BaseSelect from '@/Components/Form/BaseSelect.vue'
export default {
    components: { AppLayout, BaseInput, BaseSelect },
    props: {
        templateId: { type: String, default: null }
    },
    data() {
        return {
            user: this.$page.props.user || null,
            types: [],
            typeOptions: [],
            statusOptions: [
                { value: 'active', label: 'Ativo' },
                { value: 'inactive', label: 'Inativo' },
            ],
            form: {
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
                fonts_json: null,
                layout_json: null,
            },
            fontsJsonText: '',
            layoutJsonText: '',
            serverHtml: '',
            activeTab: 'dados',
        }
    },
    computed: {},
    created() {
        this.fetchTypes()
    },
    methods: {
        labelOf(t) {
            if (t === 'contract') return 'Contratos'
            if (t === 'invoice') return 'Faturas'
            if (t === 'quote') return 'Orçamentos'
            return t
        },
        tabClass(key) {
            return [
                'px-3 py-2 border-b-2',
                this.activeTab === key ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600 hover:text-gray-800'
            ]
        },
        safeParse(t) {
            try { return t ? JSON.parse(t) : null } catch { return null }
        },
        async fetchTypes() {
            const { data } = await window.axios.get('/api/document-templates/types')
            this.types = data
            this.typeOptions = data.map(t => ({ value: t, label: this.labelOf(t) }))
            if (this.templateId) {
                await this.loadTemplate()
            }
        },
        async loadTemplate() {
            const { data } = await window.axios.get(`/api/document-templates/${this.templateId}`)
            this.form = {
                type: data.type,
                name: data.name,
                language: data.language,
                country: data.country,
                status: data.status,
                is_default: !!data.is_default,
                header_html: data.header_html || '',
                content_html: data.content_html || '',
                footer_html: data.footer_html || '',
                background_image_path: data.background_image_path || '',
                fonts_json: data.fonts_json || null,
                layout_json: data.layout_json || null,
            }
            this.fontsJsonText = this.form.fonts_json ? JSON.stringify(this.form.fonts_json) : ''
            this.layoutJsonText = this.form.layout_json ? JSON.stringify(this.form.layout_json) : ''
        },
        async save() {
            const payload = { ...this.form }
            payload.fonts_json = this.safeParse(this.fontsJsonText)
            payload.layout_json = this.safeParse(this.layoutJsonText)
            if (this.templateId) {
                await window.axios.put(`/api/document-templates/${this.templateId}`, payload)
            } else {
                const { data } = await window.axios.post('/api/document-templates', payload)
                this.$inertia.visit(`/document-templates/${data.id}/edit`)
                return
            }
            if (payload.is_default) {
                await window.axios.post(`/api/document-templates/${this.templateId}/set-default`)
            }
        },
        async fetchPreviewHtml() {
            if (!this.templateId) return
            const { data } = await window.axios.post(`/api/document-generation/${this.templateId}/preview-html`, { name: 'Exemplo' })
            this.serverHtml = data.html
        },
        exportPdf() {
            if (!this.templateId) return
            const url = `/api/document-generation/${this.templateId}/export-pdf?name=Exemplo`
            window.open(url, '_blank')
        },
        goBack() {
            this.$inertia.visit('/document-templates')
        }
    }
}
</script>

<style scoped>
.preview-page { box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
</style>


