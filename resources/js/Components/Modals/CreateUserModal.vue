<template>
  <Modal
    :show="show"
    title="Criar Novo Usuário"
    description="Preencha os dados abaixo para criar um novo usuário"
    icon="bx bx-user-plus"
    type="default"
    size="md"
    @close="$emit('close')"
  >
    <form @submit.prevent="submit" class="space-y-4">
      <!-- Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Nome Completo *
        </label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          class="form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          :class="{ 'border-red-500': errors.name }"
          required
          placeholder="Digite o nome completo"
        >
        <div v-if="errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
          {{ errors.name }}
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Email *
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          class="form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          :class="{ 'border-red-500': errors.email }"
          required
          placeholder="Digite o email"
        >
        <div v-if="errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
          {{ errors.email }}
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Senha *
        </label>
        <input
          id="password"
          v-model="form.password"
          type="password"
          class="form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          :class="{ 'border-red-500': errors.password }"
          required
          placeholder="Digite a senha"
        >
        <div v-if="errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
          {{ errors.password }}
        </div>
      </div>

      <!-- Password Confirmation -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Confirmar Senha *
        </label>
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          required
          placeholder="Confirme a senha"
        >
      </div>

      <!-- Is Master -->
      <div>
        <div class="flex items-center">
          <input
            id="is_master"
            v-model="form.is_master"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded"
          >
          <label for="is_master" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
            Administrador
          </label>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
          Usuários administradores têm acesso total ao sistema
        </p>
      </div>
    </form>

    <template #footer>
      <button
        type="button"
        @click="$emit('close')"
        class="btn btn-outline-primary mr-3"
      >
        Cancelar
      </button>
      <button
        type="submit"
        @click="submit"
        :disabled="processing"
        class="btn btn-primary"
        :class="{ 'opacity-50 cursor-not-allowed': processing }"
      >
        <i v-if="processing" class="bx bx-loader-alt animate-spin mr-1"></i>
        <i v-else class="bx bx-save mr-1"></i>
        {{ processing ? 'Criando...' : 'Criar Usuário' }}
      </button>
    </template>
  </Modal>
</template>

<script setup>
import Modal from '@/Components/Modal.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'success'])

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  is_master: false
})

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      form.reset()
      emit('success')
      emit('close')
    }
  })
}
</script>

<style scoped>
.form-input {
  transition: all 0.2s ease-in-out;
}

.form-input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease-in-out;
  outline: none;
}

.btn:focus {
  ring: 2px;
  ring-offset: 2px;
}

.btn-primary {
  background-color: #2563eb;
  color: white;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

.btn-primary:focus {
  ring-color: #3b82f6;
}

.btn-outline-primary {
  border-color: #93c5fd;
  color: #1d4ed8;
  background-color: #eff6ff;
}

.btn-outline-primary:hover {
  background-color: #dbeafe;
}

.dark .btn-outline-primary {
  border-color: #2563eb;
  color: #60a5fa;
  background-color: rgba(30, 58, 138, 0.2);
}

.dark .btn-outline-primary:hover {
  background-color: rgba(30, 58, 138, 0.3);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
