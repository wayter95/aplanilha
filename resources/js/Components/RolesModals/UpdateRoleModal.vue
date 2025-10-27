<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

            <!-- Modal -->
            <div
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"
            >
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Editar Função
                    </h3>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- ✅ BaseForm -->
                <BaseForm @submit="updateRole">
                    <div class="space-y-4">
                        <Input
                            id="update-role-name"
                            name="name"
                            v-model="form.name"
                            type="text"
                            label="Nome (identificador único)"
                            placeholder="Digite o nome da função"
                            required
                            :rules="nameRules"
                        />

                        <Input
                            id="update-role-display-name"
                            name="display_name"
                            v-model="form.display_name"
                            type="text"
                            label="Nome de Exibição"
                            placeholder="Digite o nome de exibição"
                            required
                            :rules="displayNameRules"
                        />

                        <div>
                            <label
                                for="edit-description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Descrição
                            </label>
                            <textarea
                                id="edit-description"
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
                                        :id="`edit-permission-${permission.id}`"
                                        :value="permission.id"
                                        v-model="form.permissions"
                                        class="form-check-input me-2"
                                    >
                                    <label
                                        :for="`edit-permission-${permission.id}`"
                                        class="text-sm text-gray-700 dark:text-gray-300"
                                    >
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
                                    Salvando...
                                </span>
                                <span v-else>
                                    Atualizar
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
import { ref, watch } from 'vue'

const page = usePage()
const { success, error } = useToast()
const loading = ref(false)

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    role: {
        type: Object,
        default: null
    },
    availablePermissions: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['close', 'role-updated'])

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

// 🧭 Preencher formulário ao abrir modal
watch(
    () => props.role,
    (newRole) => {
        if (newRole) {
            form.value = {
                name: newRole.name || '',
                display_name: newRole.display_name || '',
                description: newRole.description || '',
                status: newRole.is_active ? 'Ativo' : 'Inativo',
                permissions: newRole.permissions
                    ? newRole.permissions.map((p) => p.id)
                    : []
            }
        }
    },
    { immediate: true }
)

const updateRole = async (values, { setErrors }) => {
    if (!props.role || !props.role.id) {
        console.error('No role ID provided')
        return
    }

    loading.value = true

    try {
        const dataToSend = {
      ...form.value,
      is_active: form.value.status === 'Ativo'
    }

        const response = await fetch(`/api/roles/${props.role.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': page.props.csrf_token
            },
            body: JSON.stringify(dataToSend)
        })

        const result = await response.json()

        if (result.success) {
            success('Função atualizada com sucesso!')
            emit('role-updated', result.role)
            closeModal()
        } else {
            if (result.errors) {
                // 👇 Exibe mensagens vindas do backend (ex. "Nome de Exibição obrigatório")
                setErrors(result.errors)
            } else {
                error(result.message || 'Erro ao atualizar função.')
            }
        }
    } catch (err) {
        console.error('Erro ao atualizar função:', err)
        error('Erro ao atualizar função')
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
