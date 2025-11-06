<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Criar Nova Função
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- ✅ BaseForm -->
                <BaseForm @submit="createRole">
                    <div class="space-y-4">
                        <Input
                            id="create-role-name"
                            name="name"
                            v-model="form.name"
                            type="text"
                            label="Nome (identificador único)"
                            placeholder="Digite o nome da função"
                            required
                            :rules="nameRules"
                        />

                        <!-- ✅ Importante: garantir name="display_name" -->
                        <Input
                            id="create-role-display-name"
                            name="display_name"
                            v-model="form.display_name"
                            type="text"
                            label="Nome de Exibição"
                            placeholder="Digite o nome de exibição"
                            required
                            :rules="displayNameRules"
                        />

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descrição
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                v-model="form.description"
                                rows="3"
                                class="ti-form-input w-full"
                                placeholder="Digite uma descrição para a função"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select
                                v-model="form.status"
                                name="status"
                                required
                                class="ti-form-select w-full"
                                :rules="statusRules"
                            >
                                <option value="">Selecione um status</option>
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Permissões
                            </label>
                            <div class="border rounded-lg p-3 max-h-48 overflow-y-auto">
                                <div
                                    v-for="permission in availablePermissions"
                                    :key="permission.id"
                                    class="flex items-center mb-2"
                                >
                                    <input
                                        type="checkbox"
                                        :id="`permission-${permission.id}`"
                                        :value="permission.id"
                                        v-model="form.permissions"
                                        class="form-check-input me-2"
                                    >
                                    <label :for="`permission-${permission.id}`" class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ `${permission.module} - ${permission.action}` }}
                                    </label>
                                </div>
                                <p v-if="availablePermissions.length === 0" class="text-gray-500 text-sm">
                                    Nenhuma permissão disponível
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 mt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="ti-btn ti-btn-light"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="ti-btn ti-btn-primary"
                            >
                                <span v-if="loading" class="flex items-center">
                                    <i class="ri-loader-4-line animate-spin mr-2"></i>
                                    Criando...
                                </span>
                                <span v-else>
                                    Criar
                                </span>
                            </button>
                        </div>
                    </div>
                </BaseForm>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseForm from '@/Components/Form/BaseForm.vue'
import Input from '@/Components/Input.vue'
import { useToast } from '@/composables/useToast'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()
const { success, error } = useToast()
const loading = ref(false)

const props = defineProps({
    show: { type: Boolean, default: false },
    availablePermissions: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'role-created'])

const nameRules = 'required'
const displayNameRules = 'required'
const statusRules = 'required'

const form = ref({
    name: '',
    display_name: '',
    description: '',
    status: '',
    permissions: []
})

const createRole = async (values, { setErrors }) => {
    loading.value = true

    try {
        const { status, ...rest } = values

        const dataToSend = {
            ...rest,
            is_active: status === 'Ativo'
        }

        const response = await fetch('/api/roles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token
            },
            body: JSON.stringify(dataToSend)
        })

        const result = await response.json()

        if (result.success) {
            success('Função criada com sucesso!')
            emit('role-created', result.role)
            closeModal()
        } else {
            if (result.errors) {
                // 👇 Exibe mensagem de erro vinda do backend (incluindo display_name)
                setErrors(result.errors)
            } else {
                error(result.message || 'Erro ao criar função.')
            }
        }
    } catch (err) {
        console.error('Erro ao criar função:', err)
        error('Erro ao criar função')
    } finally {
        loading.value = false
    }
}

const closeModal = () => {
    form.value = {
        name: '',
        display_name: '',
        description: '',
        status: '',
        permissions: []
    }
    emit('close')
}
</script>
