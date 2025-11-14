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
                <div class="relative folders-dropdown">
                  <button
                    @click="toggleFoldersDropdown"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                    :class="{ '!bg-primary/10 !text-primary !border-primary/20': props.filters.type }"
                  >
                    <i class="ri-folder-line text-sm"></i>
                    <span class="font-medium hidden sm:inline">
                      {{ getCurrentTypeLabel() }}
                    </span>
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
                      class="w-full text-left px-4 py-3 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors first:rounded-t-lg last:rounded-b-lg text-defaulttextcolor dark:text-white flex items-center justify-between"
                      :class="{ '!bg-primary/10 !text-primary': props.filters.type === type.value }"
                    >
                      {{ type.label }}
                      <i v-if="props.filters.type === type.value" class="ri-check-line text-primary"></i>
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
                <div class="relative tags-dropdown">
                  <button 
                    @click="toggleTagsDropdown"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                    :class="{ '!bg-primary/10 !text-primary !border-primary/20': props.filters.tag }"
                  >
                    <i class="ri-price-tag-3-line text-sm"></i>
                    <span class="hidden sm:inline">
                      {{ props.filters.tag ? `Tag: ${props.filters.tag}` : 'Tags' }}
                    </span>
                    <i class="ri-arrow-down-s-line text-sm"></i>
                  </button>
                  <div
                    v-if="showTagsDropdown"
                    class="absolute left-0 mt-1 w-48 bg-bodybg dark:bg-dark-800 border border-defaultborder dark:border-white/10 rounded-lg shadow-lg z-20"
                  >
                    <div class="p-2">
                      <div class="text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 px-2 py-1 uppercase tracking-wide">Filtrar por Tag</div>
                      <!-- Opção para limpar filtro -->
                      <button
                        v-if="props.filters.tag"
                        @click="clearTagFilter"
                        class="w-full text-left px-3 py-2 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors rounded-lg flex items-center gap-2 text-sm text-red-600 dark:text-red-400"
                      >
                        <i class="ri-close-line"></i>
                        Limpar Filtro
                      </button>
                      <!-- Separador se há filtro ativo -->
                      <div v-if="props.filters.tag" class="border-t border-defaultborder dark:border-white/10 my-1"></div>
                      <!-- Tags disponíveis -->
                      <button
                        v-for="tag in availableTags"
                        :key="tag.id"
                        @click="filterByTag(tag)"
                        class="w-full text-left px-3 py-2 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors rounded-lg flex items-center gap-2 text-sm"
                        :class="{ '!bg-primary/10 !text-primary': props.filters.tag === tag.name }"
                      >
                        <span class="w-3 h-3 rounded-full" :class="tag.color"></span>
                        {{ tag.name }}
                        <i v-if="props.filters.tag === tag.name" class="ri-check-line text-primary ml-auto"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="relative filters-dropdown">
                  <button 
                    @click="toggleFiltersDropdown"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                    :class="{ '!bg-primary/10 !text-primary !border-primary/20': hasActiveFilters() }"
                  >
                    <i class="ri-filter-3-line text-sm"></i>
                    <span class="hidden sm:inline">
                      {{ hasActiveFilters() ? `Filtros (${getActiveFiltersCount()})` : 'Filters' }}
                    </span>
                    <i class="ri-arrow-down-s-line text-sm"></i>
                  </button>
                  <div
                    v-if="showFiltersDropdown"
                    class="absolute left-0 mt-1 w-64 bg-bodybg dark:bg-dark-800 border border-defaultborder dark:border-white/10 rounded-lg shadow-lg z-20"
                  >
                    <div class="p-2">
                      <div class="text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 px-2 py-1 uppercase tracking-wide">Filtros Avançados</div>
                      <!-- Botão para limpar todos os filtros -->
                      <button
                        v-if="hasActiveFilters()"
                        @click="clearAllFilters"
                        class="w-full text-left px-3 py-2 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors rounded-lg text-sm text-red-600 dark:text-red-400 flex items-center gap-2 mb-1"
                      >
                        <i class="ri-close-circle-line"></i>
                        Limpar Todos os Filtros
                      </button>
                      <!-- Separador se há filtros ativos -->
                      <div v-if="hasActiveFilters()" class="border-t border-defaultborder dark:border-white/10 my-1"></div>
                      <!-- Opções de filtro -->
                      <button
                        v-for="filter in filterOptions"
                        :key="filter.key"
                        @click="toggleFilter(filter)"
                        class="w-full text-left px-3 py-2 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors rounded-lg text-sm text-defaulttextcolor dark:text-white flex items-center gap-2"
                        :class="{ '!bg-primary/10 !text-primary': props.filters[filter.key] }"
                      >
                        <i :class="filter.icon" class="text-sm"></i>
                        <span class="flex-1">{{ filter.label }}</span>
                        <i v-if="props.filters[filter.key]" class="ri-check-line text-primary"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="relative views-dropdown">
                  <button 
                    @click="toggleViewsDropdown"
                    class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                  >
                    <i class="ri-eye-line text-sm"></i>
                    <span class="hidden sm:inline">Views</span>
                    <i class="ri-arrow-down-s-line text-sm"></i>
                  </button>
                  <div
                    v-if="showViewsDropdown"
                    class="absolute left-0 mt-1 w-44 bg-bodybg dark:bg-dark-800 border border-defaultborder dark:border-white/10 rounded-lg shadow-lg z-20"
                  >
                    <div class="p-2">
                      <div class="text-xs font-semibold text-defaulttextcolor/70 dark:text-white/70 px-2 py-1 uppercase tracking-wide">Visualização</div>
                      <button
                        v-for="view in viewOptions"
                        :key="view.value"
                        @click="changeView(view)"
                        class="w-full text-left px-3 py-2 hover:bg-light-100 dark:hover:bg-dark-700/50 transition-colors rounded-lg flex items-center gap-2 text-sm"
                        :class="{ '!bg-primary/10 !text-primary': currentView === view.value }"
                      >
                        <i :class="view.icon" class="text-sm"></i>
                        {{ view.label }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Botão de Importar -->
                <div class="ml-auto">
                  <button 
                    @click="openImportModal"
                    class="ti-btn ti-btn-soft-info !py-2.5 !px-4 flex items-center gap-2 rounded-lg"
                  >
                    <i class="ri-upload-2-line text-sm"></i>
                    <span class="hidden sm:inline">Import</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Filtros Ativos -->
            <div v-if="hasActiveFiltersOrSearch()" class="mb-6">
              <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="text-sm font-medium text-defaulttextcolor/70 dark:text-white/70">Filtros ativos:</span>
                  
                  <!-- Filtro de busca -->
                  <span v-if="props.filters.search" class="inline-flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm">
                    Busca: "{{ props.filters.search }}"
                    <button @click="clearSearchFilter" class="hover:bg-primary/20 rounded-full p-0.5">
                      <i class="ri-close-line text-xs"></i>
                    </button>
                  </span>

                  <!-- Filtro de tipo -->
                  <span v-if="props.filters.type" class="inline-flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm">
                    {{ getCurrentTypeLabel() }}
                    <button @click="clearTypeFilter" class="hover:bg-primary/20 rounded-full p-0.5">
                      <i class="ri-close-line text-xs"></i>
                    </button>
                  </span>

                  <!-- Filtro de tag -->
                  <span v-if="props.filters.tag" class="inline-flex items-center gap-1 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm">
                    Tag: {{ props.filters.tag }}
                    <button @click="clearTagFilter" class="hover:bg-primary/20 rounded-full p-0.5">
                      <i class="ri-close-line text-xs"></i>
                    </button>
                  </span>

                  <!-- Filtros avançados ativos -->
                  <span 
                    v-for="filter in getActiveAdvancedFilters()" 
                    :key="filter.key"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm"
                  >
                    <i :class="filter.icon" class="text-xs"></i>
                    {{ filter.label }}
                    <button @click="clearSingleFilter(filter.key)" class="hover:bg-primary/20 rounded-full p-0.5 ml-1">
                      <i class="ri-close-line text-xs"></i>
                    </button>
                  </span>

                  <!-- Botão para limpar todos -->
                  <button 
                    @click="clearAllActiveFilters"
                    class="inline-flex items-center gap-1 px-3 py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-full text-sm font-medium transition-colors"
                  >
                    <i class="ri-close-circle-line"></i>
                    Limpar Todos
                  </button>
                </div>
                
                <!-- Contador de resultados filtrados -->
                <div v-if="contacts.data && contacts.data.length > 0" class="text-sm text-defaulttextcolor/60 dark:text-white/60">
                  <i class="ri-filter-line mr-1"></i>
                  {{ contacts.total }} {{ contacts.total === 1 ? 'resultado encontrado' : 'resultados encontrados' }}
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

            <!-- Tabela de Contatos - Vista Lista -->
            <div v-else-if="currentView === 'list'" class="overflow-hidden rounded-lg border border-defaultborder dark:border-white/10 bg-bodybg dark:bg-dark-800">
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

            <!-- Vista Grid -->
            <div v-else-if="currentView === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <div
                v-for="contact in contacts.data"
                :key="contact.id"
                class="bg-bodybg dark:bg-dark-800 rounded-lg border border-defaultborder dark:border-white/10 p-6 hover:shadow-lg transition-all duration-200 hover:border-primary/30"
              >
                <div class="flex items-start justify-between mb-4">
                  <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                    <span class="text-primary font-semibold">
                      {{ contact.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <span
                    :class="getTypeBadgeClass(contact.type)"
                    class="badge !py-1 !px-2 !text-xs font-medium rounded-full"
                  >
                    {{ getTypeLabel(contact.type) }}
                  </span>
                </div>
                
                <h3 class="font-semibold text-defaulttextcolor dark:text-white text-lg mb-1">{{ contact.name }}</h3>
                <p v-if="contact.name_line" class="text-defaulttextcolor/60 dark:text-white/60 text-sm mb-3">{{ contact.name_line }}</p>
                
                <div class="space-y-2 mb-4">
                  <div v-if="contact.email" class="flex items-center gap-2 text-sm">
                    <i class="ri-mail-line text-defaulttextcolor/50 dark:text-white/50"></i>
                    <span class="text-defaulttextcolor/80 dark:text-white/80 truncate">{{ contact.email }}</span>
                  </div>
                  <div v-if="contact.phone" class="flex items-center gap-2 text-sm">
                    <i class="ri-phone-line text-defaulttextcolor/50 dark:text-white/50"></i>
                    <span class="text-defaulttextcolor/80 dark:text-white/80">{{ contact.phone }}</span>
                  </div>
                  <div v-if="contact.city_visiting || contact.country_visiting" class="flex items-center gap-2 text-sm">
                    <i class="ri-map-pin-line text-defaulttextcolor/50 dark:text-white/50"></i>
                    <span class="text-defaulttextcolor/80 dark:text-white/80 truncate">
                      {{ [contact.city_visiting, contact.country_visiting].filter(Boolean).join(', ') }}
                    </span>
                  </div>
                </div>
                
                <div class="flex gap-2">
                  <button
                    @click="handleEdit(contact)"
                    class="ti-btn ti-btn-sm ti-btn-soft-primary !py-2 !px-3 rounded-lg flex-1"
                  >
                    <i class="ri-edit-line text-sm"></i>
                    Editar
                  </button>
                  <button
                    @click="handleDelete(contact)"
                    class="ti-btn ti-btn-sm ti-btn-soft-danger !py-2 !px-2.5 rounded-lg"
                  >
                    <i class="ri-delete-bin-line text-sm"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Vista Cards -->
            <div v-else-if="currentView === 'card'" class="space-y-4">
              <div
                v-for="contact in contacts.data"
                :key="contact.id"
                class="bg-bodybg dark:bg-dark-800 rounded-lg border border-defaultborder dark:border-white/10 p-6 hover:shadow-lg transition-all duration-200 hover:border-primary/30"
              >
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-primary font-semibold text-xl">
                      {{ contact.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-2">
                      <div>
                        <h3 class="font-semibold text-defaulttextcolor dark:text-white text-lg">{{ contact.name }}</h3>
                        <p v-if="contact.name_line" class="text-defaulttextcolor/60 dark:text-white/60 text-sm">{{ contact.name_line }}</p>
                      </div>
                      <span
                        :class="getTypeBadgeClass(contact.type)"
                        class="badge !py-1.5 !px-3 !text-xs font-medium rounded-full"
                      >
                        {{ getTypeLabel(contact.type) }}
                      </span>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                      <div v-if="contact.email" class="flex items-center gap-2">
                        <i class="ri-mail-line text-defaulttextcolor/50 dark:text-white/50"></i>
                        <span class="text-defaulttextcolor/80 dark:text-white/80 text-sm truncate">{{ contact.email }}</span>
                      </div>
                      <div v-if="contact.phone" class="flex items-center gap-2">
                        <i class="ri-phone-line text-defaulttextcolor/50 dark:text-white/50"></i>
                        <span class="text-defaulttextcolor/80 dark:text-white/80 text-sm">{{ contact.phone }}</span>
                      </div>
                      <div v-if="contact.city_visiting" class="flex items-center gap-2">
                        <i class="ri-map-pin-line text-defaulttextcolor/50 dark:text-white/50"></i>
                        <button
                          @click="filterByCity(contact.city_visiting)"
                          class="text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium transition-colors"
                        >
                          {{ contact.city_visiting }}
                        </button>
                      </div>
                      <div v-if="contact.country_visiting" class="flex items-center gap-2">
                        <i class="ri-global-line text-defaulttextcolor/50 dark:text-white/50"></i>
                        <button
                          @click="filterByCountry(contact.country_visiting)"
                          class="text-primary hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium transition-colors"
                        >
                          {{ contact.country_visiting }}
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex gap-2 flex-shrink-0">
                    <button
                      @click="handleEdit(contact)"
                      class="ti-btn ti-btn-sm ti-btn-soft-primary !py-2 !px-2.5 rounded-lg"
                      title="Editar contato"
                    >
                      <i class="ri-edit-line text-sm"></i>
                    </button>
                    <button
                      @click="handleDelete(contact)"
                      class="ti-btn ti-btn-sm ti-btn-soft-danger !py-2 !px-2.5 rounded-lg"
                      title="Excluir contato"
                    >
                      <i class="ri-delete-bin-line text-sm"></i>
                    </button>
                  </div>
                </div>
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

    <!-- Modal de Importação -->
    <div
      v-if="showImportModal"
      class="hs-overlay fixed inset-0 z-[80] bg-black/50 flex items-center justify-center p-4"
    >
      <div class="bg-bodybg dark:bg-dark-800 rounded-xl shadow-lg max-w-md w-full mx-auto">
        <div class="flex items-center justify-between p-6 border-b border-defaultborder dark:border-white/10">
          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">
            Importar Contatos
          </h3>
          <button
            @click="closeImportModal"
            class="text-defaulttextcolor/60 hover:text-defaulttextcolor dark:text-white/60 dark:hover:text-white transition-colors"
          >
            <i class="ri-close-line text-xl"></i>
          </button>
        </div>
        
        <div class="p-6">
          <div class="mb-6">
            <p class="text-defaulttextcolor/70 dark:text-white/70 text-sm mb-4">
              Selecione um arquivo CSV ou Excel para importar seus contatos.
            </p>
            <div class="space-y-2 text-xs text-defaulttextcolor/60 dark:text-white/60">
              <p><strong>Formato suportado:</strong> CSV, Excel (.xlsx)</p>
              <p><strong>Colunas esperadas:</strong> nome, email, telefone, tipo</p>
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-medium text-defaulttextcolor dark:text-white mb-2">
              Arquivo
            </label>
            <input
              type="file"
              accept=".csv,.xlsx,.xls"
              @change="processImport"
              class="form-control w-full"
            />
          </div>

          <div class="flex gap-3 justify-end">
            <button
              @click="closeImportModal"
              class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 rounded-lg"
            >
              Cancelar
            </button>
            <button
              @click="downloadTemplate"
              class="ti-btn ti-btn-soft-info !py-2.5 !px-4 rounded-lg flex items-center gap-2"
            >
              <i class="ri-download-2-line text-sm"></i>
              Download Template
            </button>
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
const showTagsDropdown = ref(false)
const showFiltersDropdown = ref(false)
const showViewsDropdown = ref(false)
const showImportModal = ref(false)
const currentView = ref(props.filters.view || 'list')
let searchTimeout = null

const contactTypes = [
  { value: '', label: 'Todos os Tipos' },
  { value: 'customer', label: 'Cliente' },
  { value: 'supplier', label: 'Fornecedor' },
  { value: 'location', label: 'Localização' },
]

const availableTags = ref([
  { id: 1, name: 'VIP', color: 'bg-yellow-500' },
  { id: 2, name: 'Ativo', color: 'bg-green-500' },
  { id: 3, name: 'Inativo', color: 'bg-red-500' },
  { id: 4, name: 'Potencial', color: 'bg-blue-500' },
  { id: 5, name: 'Internacional', color: 'bg-purple-500' },
])

const viewOptions = [
  { value: 'list', label: 'Lista', icon: 'ri-list-unordered' },
  { value: 'grid', label: 'Grid', icon: 'ri-grid-line' },
  { value: 'card', label: 'Cards', icon: 'ri-layout-grid-line' },
]

const filterOptions = ref([
  { key: 'has_email', label: 'Contatos com E-mail', type: 'boolean', icon: 'ri-mail-line' },
  { key: 'has_phone', label: 'Contatos com Telefone', type: 'boolean', icon: 'ri-phone-line' },
  { key: 'created_recently', label: 'Criados nos últimos 7 dias', type: 'boolean', icon: 'ri-time-line' },
  { key: 'no_location', label: 'Sem localização definida', type: 'boolean', icon: 'ri-map-pin-line' },
])

// Fechar dropdown ao clicar fora
const handleClickOutside = (event) => {
  const clickedElement = event.target
  
  // Verifica se o clique foi fora de qualquer dropdown
  if (!clickedElement.closest('.folders-dropdown')) {
    showFoldersDropdown.value = false
  }
  if (!clickedElement.closest('.tags-dropdown')) {
    showTagsDropdown.value = false
  }
  if (!clickedElement.closest('.filters-dropdown')) {
    showFiltersDropdown.value = false
  }
  if (!clickedElement.closest('.views-dropdown')) {
    showViewsDropdown.value = false
  }
  if (!clickedElement.closest('.search-container')) {
    showSearchBar.value = false
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

// Função para obter o label do tipo atual
const getCurrentTypeLabel = () => {
  if (!props.filters.type) return 'Todos os Tipos'
  const currentType = contactTypes.find(type => type.value === props.filters.type)
  return currentType ? currentType.label : 'Todos os Tipos'
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

// Função para alternar dropdown de tags
const toggleTagsDropdown = () => {
  showTagsDropdown.value = !showTagsDropdown.value
  showFiltersDropdown.value = false
  showViewsDropdown.value = false
}

// Função para aplicar filtro por tag
const filterByTag = (tag) => {
  showTagsDropdown.value = false
  router.get('/contacts', {
    ...props.filters,
    tag: tag.name,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Função para limpar filtro de tag
const clearTagFilter = () => {
  showTagsDropdown.value = false
  router.get('/contacts', {
    ...props.filters,
    tag: undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Função para alternar dropdown de filtros
const toggleFiltersDropdown = () => {
  showFiltersDropdown.value = !showFiltersDropdown.value
  showTagsDropdown.value = false
  showViewsDropdown.value = false
}

// Função para aplicar filtro avançado
const applyFilter = (filter) => {
  showFiltersDropdown.value = false
  router.get('/contacts', {
    ...props.filters,
    [filter.key]: true,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Função para alternar filtro (ativar/desativar)
const toggleFilter = (filter) => {
  const currentValue = props.filters[filter.key]
  
  // Cria uma cópia dos filtros atuais
  const newFilters = { ...props.filters }
  
  // Toggle do filtro: se está ativo, remove; se não está, ativa
  if (currentValue) {
    delete newFilters[filter.key]
  } else {
    newFilters[filter.key] = true
  }
  
  // Remove a página para voltar ao início quando aplicar filtro
  delete newFilters.page
  
  router.get('/contacts', newFilters, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      console.log(`Filtro ${filter.label} ${currentValue ? 'removido' : 'aplicado'}`)
    },
    onError: (errors) => {
      console.error('Erro ao aplicar filtro:', errors)
    }
  })
}

// Função para verificar se há filtros ativos
const hasActiveFilters = () => {
  return filterOptions.value.some(filter => props.filters[filter.key])
}

// Função para contar filtros ativos
const getActiveFiltersCount = () => {
  return filterOptions.value.filter(filter => props.filters[filter.key]).length
}

// Função para limpar todos os filtros
const clearAllFilters = () => {
  showFiltersDropdown.value = false
  
  // Cria uma cópia dos filtros mantendo apenas os filtros de controle (não de busca)
  const cleanFilters = {}
  
  // Preserva configurações que não são filtros de conteúdo
  if (props.filters.sort_by) cleanFilters.sort_by = props.filters.sort_by
  if (props.filters.sort_direction) cleanFilters.sort_direction = props.filters.sort_direction
  if (props.filters.view) cleanFilters.view = props.filters.view
  
  router.get('/contacts', cleanFilters, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      console.log('Todos os filtros avançados foram removidos')
    }
  })
}

// Função para alternar dropdown de visualizações
const toggleViewsDropdown = () => {
  showViewsDropdown.value = !showViewsDropdown.value
  showTagsDropdown.value = false
  showFiltersDropdown.value = false
}

// Função para alterar visualização
const changeView = (view) => {
  currentView.value = view.value
  showViewsDropdown.value = false
  router.get('/contacts', {
    ...props.filters,
    view: view.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

// Função para abrir modal de importação
const openImportModal = () => {
  showImportModal.value = true
}

// Função para fechar modal de importação
const closeImportModal = () => {
  showImportModal.value = false
}

// Função para processar importação de arquivo
const processImport = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Verificar tipo de arquivo
  const allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
  if (!allowedTypes.includes(file.type)) {
    alert('Tipo de arquivo não suportado. Use apenas CSV ou Excel (.xlsx)')
    event.target.value = '' // Limpar input
    return
  }

  // Verificar tamanho do arquivo (máximo 5MB)
  if (file.size > 5 * 1024 * 1024) {
    alert('Arquivo muito grande. O tamanho máximo é 5MB.')
    event.target.value = ''
    return
  }

  const formData = new FormData()
  formData.append('file', file)

  router.post('/contacts/import', formData, {
    preserveState: true,
    onSuccess: (response) => {
      closeImportModal()
      event.target.value = '' // Limpar input
      alert('Contatos importados com sucesso!')
    },
    onError: (errors) => {
      console.error('Erro na importação:', errors)
      event.target.value = '' // Limpar input
      
      // Mostrar erro específico se disponível
      const errorMessage = errors.file 
        ? `Erro: ${errors.file[0]}` 
        : 'Erro ao importar contatos. Verifique o arquivo e tente novamente.'
      alert(errorMessage)
    }
  })
}

// Função para download do template
const downloadTemplate = () => {
  // Criar dados do template
  const headers = ['nome', 'email', 'telefone', 'tipo', 'cidade_visitando', 'pais_visitando', 'linha_nome']
  const exampleData = [
    ['João Silva', 'joao@email.com', '+55 11 99999-9999', 'customer', 'São Paulo', 'Brasil', 'Empresa XYZ'],
    ['Maria Santos', 'maria@email.com', '+55 21 88888-8888', 'supplier', 'Rio de Janeiro', 'Brasil', ''],
    ['Local ABC', '', '', 'location', 'Brasília', 'Brasil', 'Escritório Central']
  ]

  // Criar CSV
  let csvContent = headers.join(',') + '\n'
  exampleData.forEach(row => {
    csvContent += row.map(field => `"${field}"`).join(',') + '\n'
  })

  // Criar e fazer download do arquivo
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'template_contatos.csv'
  link.style.display = 'none'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(link.href)
}

// Funções auxiliares para filtros ativos
const hasActiveFiltersOrSearch = () => {
  return props.filters.search || props.filters.type || props.filters.tag || hasActiveFilters()
}

const getActiveAdvancedFilters = () => {
  return filterOptions.value.filter(filter => props.filters[filter.key])
}

const clearSearchFilter = () => {
  searchQuery.value = ''
  router.get('/contacts', {
    ...props.filters,
    search: undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearTypeFilter = () => {
  router.get('/contacts', {
    ...props.filters,
    type: undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearSingleFilter = (filterKey) => {
  router.get('/contacts', {
    ...props.filters,
    [filterKey]: undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearAllActiveFilters = () => {
  searchQuery.value = ''
  const cleanFilters = {}
  
  // Manter apenas os filtros que não são considerados "ativos" (paginação, ordenação)
  if (props.filters.page) cleanFilters.page = props.filters.page
  if (props.filters.sort_by) cleanFilters.sort_by = props.filters.sort_by
  if (props.filters.sort_direction) cleanFilters.sort_direction = props.filters.sort_direction
  if (props.filters.view) cleanFilters.view = props.filters.view

  router.get('/contacts', cleanFilters, {
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
