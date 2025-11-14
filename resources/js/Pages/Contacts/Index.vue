<template>
  <AppLayout :title="'Contatos'" :description="'Gerenciar contatos do sistema'" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box custom-card">
          <div class="box-header justify-between items-center py-4 px-6">
            <div class="box-title text-lg font-semibold text-gray-900 dark:text-white">
              Gerenciamento de Contatos
            </div>
            <div class="flex gap-3">
              <button 
                @click="handleAddContact"
                class="ti-btn ti-btn-primary-full !py-2.5 !px-4 !text-sm flex items-center gap-2 rounded-lg font-medium"
              >
                <i class="ri-add-line text-sm"></i>
                Novo Contato
              </button>
            </div>
          </div>
          
          <div class="box-body p-6">
            <!-- Barra de Pesquisa e Filtros -->
            <div class="mb-6">
              <!-- Linha única com todos os controles -->
              <div class="flex items-center gap-3 flex-wrap">
                <!-- Dropdown de Tipos -->
                <div class="relative">
                  <button
                    @click="toggleFoldersDropdown"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                  >
                    <i class="ri-folder-line text-sm"></i>
                    <span class="font-medium hidden sm:inline">All folders</span>
                    <i class="ri-arrow-down-s-line"></i>
                  </button>
                  <div
                    v-if="showFoldersDropdown"
                    class="absolute left-0 mt-1 w-56 bg-bodybg dark:bg-dark-800 border border-defaultborder dark:border-white/10 rounded-lg shadow-lg z-20"
                  >
                    <button
                      v-for="type in contactTypes"
                      :key="type.value"
                      @click="selectType(type.value)"
                      class="w-full text-left px-4 py-3 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors first:rounded-t-lg last:rounded-b-lg text-defaulttextcolor dark:text-white"
                    >
                      {{ type.label }}
                    </button>
                  </div>
                </div>

                <!-- Botão de Busca -->
                <div class="relative search-container">
                  <button
                    v-if="!showSearchBar"
                    @click="showSearchBar = true"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                  >
                    <i class="ri-search-line text-sm"></i>
                  </button>
                  <div
                    v-else
                    class="flex items-center gap-2"
                  >
                    <div class="relative">
                      <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                      <input
                        ref="searchInput"
                        v-model="searchQuery"
                        @input="handleSearchChange"
                        @blur="handleSearchBlur"
                        type="text"
                        placeholder="Buscar contato..."
                        class="form-control !py-2.5 !pl-10 !pr-4 w-64 rounded-lg"
                      />
                    </div>
                    <button
                      @click="closeSearch"
                      class="ti-btn ti-btn-soft-secondary !py-2.5 !px-2.5 rounded-lg"
                    >
                      <i class="ri-close-line text-sm"></i>
                    </button>
                  </div>
                </div>

                <!-- Botões de ação -->
                <button class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg">
                  <i class="ri-price-tag-3-line text-sm"></i>
                  <span class="hidden sm:inline">Tags</span>
                </button>
                <button class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg">
                  <i class="ri-filter-3-line text-sm"></i>
                  <span class="hidden sm:inline">Filters</span>
                </button>
                <button class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg">
                  <i class="ri-eye-line text-sm"></i>
                  <span class="hidden sm:inline">Views</span>
                </button>

                <!-- Botão de Importar -->
                <div class="ml-auto">
                  <button class="ti-btn ti-btn-soft-info !py-2.5 !px-4 flex items-center gap-2 rounded-lg">
                    <i class="ri-upload-2-line text-sm"></i>
                    <span class="hidden sm:inline">Import</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Mensagem quando não há contatos -->
            <div v-if="!contacts.data || contacts.data.length === 0" class="text-center py-20">
              <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                  <i class="ri-user-line text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Nenhum contato encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                  Adicione novos contatos ou ajuste os filtros de busca para visualizar os resultados.
                </p>
                <button 
                  @click="handleAddContact"
                  class="ti-btn ti-btn-primary-full !py-2.5 !px-6 flex items-center gap-2"
                >
                  <i class="ri-add-line"></i>
                  Adicionar Primeiro Contato
                </button>
              </div>
            </div>

            <!-- Tabela de Contatos -->
            <div v-else class="overflow-hidden rounded-lg border border-defaultborder dark:border-white/10 bg-bodybg dark:bg-dark-800">
              <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap">
                  <thead class="bg-light-100 dark:bg-dark-700">
                    <tr class="border-b border-defaultborder dark:border-white/10">
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 min-w-[200px] uppercase tracking-wide">
                        <button @click="sortBy('name')" class="flex items-center gap-2 hover:text-primary dark:hover:text-primary-400 transition-colors">
                          Nome
                          <i v-if="currentSort === 'name'" :class="sortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'" class="text-sm"></i>
                        </button>
                      </th>
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">
                        <button @click="sortBy('type')" class="flex items-center gap-2 hover:text-primary dark:hover:text-primary-400 transition-colors">
                          Tipo
                          <i v-if="currentSort === 'type'" :class="sortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'" class="text-sm"></i>
                        </button>
                      </th>
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">E-mail</th>
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">
                        <button @click="sortBy('phone')" class="flex items-center gap-2 hover:text-primary dark:hover:text-primary-400 transition-colors">
                          Telefone
                          <i v-if="currentSort === 'phone'" :class="sortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'" class="text-sm"></i>
                        </button>
                      </th>
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">
                        <button @click="sortBy('city_visiting')" class="flex items-center gap-2 hover:text-primary dark:hover:text-primary-400 transition-colors">
                          Cidade
                          <i v-if="currentSort === 'city_visiting'" :class="sortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'" class="text-sm"></i>
                        </button>
                      </th>
                      <th scope="col" class="!text-start !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">
                        <button @click="sortBy('country_visiting')" class="flex items-center gap-2 hover:text-primary dark:hover:text-primary-400 transition-colors">
                          País
                          <i v-if="currentSort === 'country_visiting'" :class="sortDirection === 'asc' ? 'ri-arrow-up-line' : 'ri-arrow-down-line'" class="text-sm"></i>
                        </button>
                      </th>
                      <th scope="col" class="!text-center !text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 !py-4 !px-6 uppercase tracking-wide">Ações</th>
                    </tr>
                  </thead>
                  <tbody class="bg-bodybg dark:bg-dark-800 divide-y divide-defaultborder dark:divide-white/10">
                    <tr
                      v-for="contact in contacts.data"
                      :key="contact.id"
                      class="hover:bg-light-200 dark:hover:bg-dark-700/50 transition-colors"
                    >
                      <td class="!text-start !py-4 !px-6">
                        <div class="flex items-center gap-3">
                          <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <span class="text-primary font-semibold text-sm">
                              {{ contact.name.charAt(0).toUpperCase() }}
                            </span>
                          </div>
                          <div class="leading-tight">
                            <div class="font-semibold text-defaulttextcolor dark:text-white">{{ contact.name }}</div>
                            <div v-if="contact.name_line" class="text-sm text-defaulttextcolor/60 dark:text-white/60 mt-0.5">
                              {{ contact.name_line }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="!text-start !py-4 !px-6">
                        <span
                          :class="getTypeBadgeClass(contact.type)"
                          class="badge !py-1.5 !px-3 !text-xs font-medium rounded-full"
                        >
                          {{ getTypeLabel(contact.type) }}
                        </span>
                      </td>
                      <td class="!text-start !py-4 !px-6">
                        <div v-if="contact.email" class="flex items-center gap-2">
                          <i class="ri-mail-line text-defaulttextcolor/50 dark:text-white/50 text-sm"></i>
                          <span class="text-defaulttextcolor/80 dark:text-white/80">{{ contact.email }}</span>
                        </div>
                        <span v-else class="text-defaulttextcolor/40 dark:text-white/40 text-sm">—</span>
                      </td>
                      <td class="!text-start !py-4 !px-6">
                        <div v-if="contact.phone" class="flex items-center gap-2">
                          <i class="ri-phone-line text-defaulttextcolor/50 dark:text-white/50 text-sm"></i>
                          <span class="text-defaulttextcolor/80 dark:text-white/80">{{ contact.phone }}</span>
                        </div>
                        <span v-else class="text-defaulttextcolor/40 dark:text-white/40 text-sm">—</span>
                      </td>
                      <td class="!text-start !py-4 !px-6">
                        <button
                          v-if="contact.city_visiting"
                          @click="filterByCity(contact.city_visiting)"
                          class="text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors"
                        >
                          {{ contact.city_visiting }}
                        </button>
                        <span v-else class="text-defaulttextcolor/40 dark:text-white/40 text-sm">—</span>
                      </td>
                      <td class="!text-start !py-4 !px-6">
                        <button
                          v-if="contact.country_visiting"
                          @click="filterByCountry(contact.country_visiting)"
                          class="text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors"
                        >
                          {{ contact.country_visiting }}
                        </button>
                        <span v-else class="text-defaulttextcolor/40 dark:text-white/40 text-sm">—</span>
                      </td>
                      <td class="!text-center !py-4 !px-6">
                        <div class="flex gap-2 justify-center">
                          <button
                            @click="handleEdit(contact)"
                            class="ti-btn ti-btn-sm ti-btn-soft-primary !py-2 !px-2.5 rounded-lg group"
                            title="Editar contato"
                          >
                            <i class="ri-edit-line text-sm group-hover:scale-110 transition-transform"></i>
                          </button>
                          <button
                            @click="handleDelete(contact)"
                            class="ti-btn ti-btn-sm ti-btn-soft-danger !py-2 !px-2.5 rounded-lg group"
                            title="Excluir contato"
                          >
                            <i class="ri-delete-bin-line text-sm group-hover:scale-110 transition-transform"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Paginação -->
            <div v-if="contacts.data && contacts.data.length > 0" class="mt-6 border-t border-defaultborder dark:border-white/10 pt-6">
              <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="text-defaulttextcolor/70 dark:text-white/70 text-sm font-medium">
                  Mostrando {{ contacts.from }} até {{ contacts.to }} de {{ contacts.total }} registros
                </div>
                <nav aria-label="Navegação de páginas">
                  <ul class="ti-pagination mb-0 flex gap-1">
                    <li v-for="link in contacts.links" :key="link.label" :class="{ active: link.active }">
                      <button
                        @click="changePage(link.url)"
                        :disabled="!link.url"
                        class="page-link !py-2 !px-3 rounded-lg border border-defaultborder dark:border-white/10 transition-colors bg-bodybg dark:bg-dark-800 text-defaulttextcolor dark:text-white"
                        :class="{
                          '!bg-primary !text-white !border-primary': link.active,
                          '!text-defaulttextcolor/40 dark:!text-white/40 !cursor-not-allowed !bg-light-200 dark:!bg-dark-700': !link.url,
                          'hover:!bg-light-100 dark:hover:!bg-dark-700': link.url && !link.active,
                        }"
                        v-html="link.label"
                      ></button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'

const props = defineProps({
  contacts: {
    type: Object,
    required: true
  },
  user: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const searchQuery = ref(props.filters.search || '')
const currentSort = ref(props.filters.sort_by || 'name')
const sortDirection = ref(props.filters.sort_direction || 'asc')
const showFoldersDropdown = ref(false)
const showSearchBar = ref(false)
const searchInput = ref(null)
let searchTimeout = null

const contactTypes = [
  { value: '', label: 'Todos os Tipos' },
  { value: 'customer', label: 'Cliente' },
  { value: 'supplier', label: 'Fornecedor' },
  { value: 'location', label: 'Localização' },
]

// Fechar dropdown ao clicar fora
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showFoldersDropdown.value = false
    if (!event.target.closest('.search-container')) {
      showSearchBar.value = false
    }
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Auto-focus quando a barra de busca é aberta
watch(showSearchBar, (newValue) => {
  if (newValue) {
    nextTick(() => {
      searchInput.value?.focus()
    })
  }
})

const toggleFoldersDropdown = () => {
  showFoldersDropdown.value = !showFoldersDropdown.value
}

const closeSearch = () => {
  showSearchBar.value = false
  searchQuery.value = ''
  // Atualizar a busca para limpar filtros
  router.get('/contacts', {
    ...props.filters,
    search: undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleSearchBlur = () => {
  // Fechar a barra de busca se o campo estiver vazio
  setTimeout(() => {
    if (!searchQuery.value) {
      showSearchBar.value = false
    }
  }, 100)
}

const selectType = (type) => {
  showFoldersDropdown.value = false
  router.get('/contacts', {
    ...props.filters,
    type: type || undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleSearchChange = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    router.get('/contacts', {
      ...props.filters,
      search: searchQuery.value || undefined,
    }, {
      preserveState: true,
      preserveScroll: true,
    })
  }, 300)
}

const sortBy = (field) => {
  const newDirection = currentSort.value === field && sortDirection.value === 'asc' ? 'desc' : 'asc'
  currentSort.value = field
  sortDirection.value = newDirection

  router.get('/contacts', {
    ...props.filters,
    sort_by: field,
    sort_direction: newDirection,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const filterByCity = (city) => {
  router.get('/contacts', {
    ...props.filters,
    city: city,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const filterByCountry = (country) => {
  router.get('/contacts', {
    ...props.filters,
    country: country,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const changePage = (url) => {
  if (!url) return
  router.visit(url, {
    preserveState: true,
    preserveScroll: true,
  })
}

const handleAddContact = () => {
  router.visit('/contacts/create')
}

const handleEdit = (contact) => {
  router.visit(`/contacts/${contact.id}/edit`)
}

const handleDelete = (contact) => {
  if (confirm(`Tem certeza que deseja excluir o contato "${contact.name}"?`)) {
    router.delete(`/api/contacts/${contact.id}`, {
      preserveState: true,
      onSuccess: () => {
        // Remove o router.reload() para permitir atualizações automáticas
        console.log('Contato excluído com sucesso')
      },
      onError: (errors) => {
        console.error('Erro ao excluir:', errors)
        alert('Erro ao excluir contato')
      }
    })
  }
}

const getTypeLabel = (type) => {
  const types = {
    customer: 'Cliente',
    supplier: 'Fornecedor',
    location: 'Localização'
  }
  return types[type] || type
}

const getTypeBadgeClass = (type) => {
  const classes = {
    customer: 'badge-primary-transparent',
    supplier: 'badge-success-transparent',
    location: 'badge-warning-transparent'
  }
  return classes[type] || 'badge-secondary-transparent'
}
</script>
