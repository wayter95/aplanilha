<template>
  <AppLayout :title="pageTitle" :description="pageDescription" :user="user">
    <div class="grid grid-cols-12 gap-6">
      <div class="xl:col-span-12 col-span-12">
        <div class="box custom-card">
          <!-- Header com botões -->
          <div class="box-header justify-between items-center py-4 px-6 border-b border-defaultborder dark:border-white/10">
            <div class="box-title text-lg font-semibold text-gray-900 dark:text-white">
              {{ pageTitle }}
            </div>
            <div class="flex gap-3">
              <div v-if="hasUnsavedChanges" class="flex items-center gap-2 text-red-500 text-sm">
                <i class="ri-error-warning-line"></i>
                Alterações não salvas
              </div>
              <button
                @click="handleCancel"
                class="ti-btn ti-btn-soft-secondary !py-2.5 !px-4 rounded-lg"
              >
                Cancelar
              </button>
              <button
                @click="handleSubmit"
                :disabled="processing"
                class="ti-btn ti-btn-primary-full !py-2.5 !px-6 rounded-lg"
                :class="{ 'opacity-50 cursor-not-allowed': processing }"
              >
                <span v-if="processing">Salvando...</span>
                <span v-else>{{ props.mode === 'create' ? 'Criar Contato' : 'Atualizar Contato' }}</span>
              </button>
            </div>
          </div>
          
          <div class="box-body p-0">
            <!-- Abas de Navegação -->
            <div class="text-sm font-medium text-center border-b border-defaultborder dark:border-white/10">
              <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                  <button
                    @click="activeMainTab = 'data'"
                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"
                    :class="
                      activeMainTab === 'data'
                        ? 'text-primary border-primary'
                        : 'border-transparent hover:text-defaulttextcolor hover:border-defaultborder dark:hover:text-white'
                    "
                  >
                    Dados
                  </button>
                </li>
                <li class="me-2">
                  <button
                    @click="activeMainTab = 'contact-persons'"
                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"
                    :class="
                      activeMainTab === 'contact-persons'
                        ? 'text-primary border-primary'
                        : 'border-transparent hover:text-defaulttextcolor hover:border-defaultborder dark:hover:text-white'
                    "
                  >
                    Pessoas de Contato
                  </button>
                </li>
              </ul>
            </div>

            <!-- Conteúdo das Abas Principais -->
            <div class="p-5">
              <!-- Aba Dados -->
              <div v-show="activeMainTab === 'data'" class="space-y-5">
                <div class="grid grid-cols-12 gap-5">
                  <!-- Coluna Principal -->
                  <div class="xl:col-span-8 col-span-12">
                    <div class="space-y-4">
                      <!-- INFORMAÇÕES BÁSICAS -->
                      <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Informações Básicas</h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-2">
                          <div class="xl:col-span-1">
                            <label class="ti-form-label mb-1 text-xs">Tipo</label>
                            <select v-model="form.type" class="ti-form-select rounded-md !py-1.5 !px-2 text-sm">
                              <option value="customer">Cliente</option>
                              <option value="supplier">Fornecedor</option>
                              <option value="location">Local</option>
                            </select>
                          </div>
                          <div class="col-span-1 md:col-span-3 xl:col-span-8">
                            <label class="ti-form-label mb-1 text-xs ">Nome da Empresa</label>
                            <input 
                              v-model="form.name"
                              type="text"
                              class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                              placeholder="Nome da empresa"
                              required
                            />
                          </div>
                          <div class="xl:col-span-1">
                            <label class="ti-form-label mb-1 text-xs">Telefone</label>
                            <input 
                              v-model="form.phone"
                              type="tel"
                              class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                              placeholder="(11) 9999-9999"
                            />
                          </div>
                          <div class="xl:col-span-3">
                            <label class="ti-form-label mb-1 text-xs">E-mail Principal</label>
                            <input 
                              v-model="form.email"
                              type="email"
                              class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                              placeholder="email@empresa.com"
                            />
                          </div>
                          <div class="xl:col-span-2">
                            <label class="ti-form-label mb-1 text-xs">Website</label>
                            <input 
                              v-model="form.website"
                              type="url"
                              class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                              placeholder="site.com"
                            />
                          </div>
                          <div class="xl:col-span-1">
                            <label class="ti-form-label mb-1 text-xs">Linha de Negócio</label>
                            <input 
                              v-model="form.name_line"
                              type="text"
                              class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                              placeholder="Linha de negócio"
                            />
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE VISITA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-3 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
                          @click="toggleSection('visitingAddress')"
                        >
                          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Endereço de Visita</h3>
                          <i 
                            class="ri-arrow-down-s-line text-xl transition-transform duration-200"
                            :class="{ 'transform rotate-180': !collapsedSections.visitingAddress }"
                          ></i>
                        </div>
                        
                        <div 
                          v-show="!collapsedSections.visitingAddress"
                          class="p-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-2">
                            <div class="col-span-2 md:col-span-4 xl:col-span-6">
                              <label class="ti-form-label mb-1 text-xs">Rua</label>
                              <input 
                                v-model="form.street_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">Nº</label>
                              <input 
                                v-model="form.house_number_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="123"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">CEP</label>
                              <input 
                                v-model="form.postal_code_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1 text-xs">Cidade</label>
                              <input 
                                v-model="form.city_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Cidade"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">UF</label>
                              <input 
                                v-model="form.state_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="SP"
                                maxlength="2"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">País</label>
                              <input 
                                v-model="form.country_visiting"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE CORRESPONDÊNCIA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-3 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
                          @click="toggleSection('mailingAddress')"
                        >
                          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Endereço de Correspondência</h3>
                          <i 
                            class="ri-arrow-down-s-line text-xl transition-transform duration-200"
                            :class="{ 'transform rotate-180': !collapsedSections.mailingAddress }"
                          ></i>
                        </div>
                        
                        <div 
                          v-show="!collapsedSections.mailingAddress"
                          class="p-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-2">
                            <div class="col-span-2 md:col-span-4 xl:col-span-6">
                              <label class="ti-form-label mb-1 text-xs">Rua</label>
                              <input 
                                v-model="form.street_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">Nº</label>
                              <input 
                                v-model="form.house_number_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="123"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">CEP</label>
                              <input 
                                v-model="form.postal_code_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1 text-xs">Cidade</label>
                              <input 
                                v-model="form.city_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Cidade"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">UF</label>
                              <input 
                                v-model="form.state_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="SP"
                                maxlength="2"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">País</label>
                              <input 
                                v-model="form.country_mailing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE COBRANÇA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-3 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
                          @click="toggleSection('billingAddress')"
                        >
                          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Endereço de Cobrança</h3>
                          <i 
                            class="ri-arrow-down-s-line text-xl transition-transform duration-200"
                            :class="{ 'transform rotate-180': !collapsedSections.billingAddress }"
                          ></i>
                        </div>
                        
                        <div 
                          v-show="!collapsedSections.billingAddress"
                          class="p-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-2 md:grid-cols-6 xl:grid-cols-12 gap-2">
                            <div class="col-span-2 md:col-span-4 xl:col-span-6">
                              <label class="ti-form-label mb-1 text-xs">Rua</label>
                              <input 
                                v-model="form.street_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">Nº</label>
                              <input 
                                v-model="form.house_number_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="123"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">CEP</label>
                              <input 
                                v-model="form.postal_code_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1 text-xs">Cidade</label>
                              <input 
                                v-model="form.city_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="Cidade"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">UF</label>
                              <input 
                                v-model="form.state_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="SP"
                                maxlength="2"
                              />
                            </div>
                            <div class="xl:col-span-1">
                              <label class="ti-form-label mb-1 text-xs">País</label>
                              <input 
                                v-model="form.country_billing"
                                type="text"
                                class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- NOTAS GERAIS -->
                      <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Observações Gerais</h3>
                        <div>
                          <textarea 
                            v-model="form.general_notes"
                            rows="2"
                            class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                            placeholder="Observações gerais sobre o contato."
                          ></textarea>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Sidebar -->
                  <div class="xl:col-span-4 col-span-12">
                    <div class="box custom-card">
                      <div class="box-header pb-3">
                        <div class="box-title text-defaulttextcolor dark:text-white">
                          Informações Adicionais
                        </div>
                      </div>
                      <div class="box-body space-y-4">
                        <div class="text-sm text-defaulttextcolor/70 dark:text-white/70">
                          <p>• Preencha todos os campos obrigatórios</p>
                          <p>• Adicione pessoas de contato na aba correspondente</p>
                          <p>• Verifique os endereços antes de salvar</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Aba Contact Persons -->
              <div v-show="activeMainTab === 'contact-persons'" class="py-6">
                <!-- Cabeçalho da seção de pessoas -->
                <div v-if="contactPersons.length > 0" class="flex items-center justify-between mb-6">
                  <div>
                    <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Pessoas de Contato</h3>
                    <p class="text-sm text-defaulttextcolor/60 dark:text-white/60">{{ contactPersons.length }} pessoa(s) adicionada(s)</p>
                  </div>
                  <button 
                    class="ti-btn ti-btn-primary-full !py-2 !px-4 rounded-lg flex items-center gap-2"
                    @click="openContactPersonModal"
                  >
                    <i class="ri-user-add-line"></i>
                    Adicionar Pessoa
                  </button>
                </div>

                <div v-if="contactPersons.length > 0" class="space-y-6">
                  <div 
                    v-for="(person, index) in contactPersons"
                    :key="person.id"
                    class="box custom-card relative"
                    :class="{ 'mt-6': index > 0 }"
                  >
                    <!-- Linha separadora visual para não ser a primeira pessoa -->
                    <div v-if="index > 0" class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                      <div class="w-16 h-0.5 bg-defaultborder dark:bg-white/10 rounded-full"></div>
                    </div>
                    
                    <div class="box-body">
                      <!-- Badge com número da pessoa -->
                      <div class="absolute -top-2 -left-2 w-6 h-6 bg-primary text-white text-xs rounded-full flex items-center justify-center font-semibold">
                        {{ index + 1 }}
                      </div>
                      
                      <!-- Cabeçalho da pessoa -->
                      <div class="flex items-start gap-3 mb-3">
                        <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center">
                          <i class="ri-user-line text-primary text-lg"></i>
                        </div>
                        <div class="flex-1">
                          <h4 class="font-semibold text-lg text-defaulttextcolor dark:text-white">
                            {{ person.first_name }} {{ person.last_name }}
                          </h4>
                          <p v-if="person.role" class="text-sm text-primary font-medium">{{ person.role }}</p>
                        </div>
                        <!-- Botões de ação -->
                        <div class="flex gap-2">
                          <button 
                            @click="editContactPerson(index)"
                            class="w-8 h-8 bg-primary/10 hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 rounded-lg flex items-center justify-center text-primary transition-colors"
                            title="Editar pessoa"
                          >
                            <i class="ri-edit-line text-sm"></i>
                          </button>
                          <button 
                            @click="deleteContactPerson(index)"
                            class="w-8 h-8 bg-danger/10 hover:bg-danger/20 dark:bg-danger/20 dark:hover:bg-danger/30 rounded-lg flex items-center justify-center text-danger transition-colors"
                            title="Excluir pessoa"
                          >
                            <i class="ri-delete-bin-line text-sm"></i>
                          </button>
                        </div>
                      </div>                        <!-- Informações de contato -->
                        <div class="space-y-2 mb-4">
                          <div v-if="person.mobile" class="flex items-center gap-2 text-sm text-defaulttextcolor/70 dark:text-white/70">
                            <i class="ri-phone-line text-primary"></i>
                            <span>{{ person.mobile }}</span>
                          </div>
                          
                          <div v-if="person.emails && person.emails.length > 0" class="space-y-1">
                            <div v-for="email in person.emails" :key="email" class="flex items-center gap-2 text-sm text-defaulttextcolor/70 dark:text-white/70">
                              <i class="ri-mail-line text-primary"></i>
                              <span>{{ email }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Notas -->
                        <div v-if="person.notes && person.notes.length > 0" class="border-t border-defaultborder dark:border-white/10 pt-4">
                          <h5 class="text-sm font-semibold text-defaulttextcolor dark:text-white mb-3 flex items-center gap-2">
                            <i class="ri-sticky-note-line text-primary"></i>
                            Notas ({{ person.notes.length }})
                          </h5>
                          <div class="space-y-3">
                            <div 
                              v-for="(note, noteIndex) in person.notes" 
                              :key="note.id"
                              class="border border-defaultborder dark:border-white/10 rounded-lg p-4 bg-light-50 dark:bg-dark-800"
                            >
                              <div class="flex items-start gap-3">
                                <div class="flex-1">
                                  <h6 class="font-semibold text-defaulttextcolor dark:text-white text-sm mb-1">{{ note.name }}</h6>
                                  <p class="text-sm text-defaulttextcolor/80 dark:text-white/80 leading-relaxed mb-2">{{ note.content }}</p>
                                  <div class="flex items-center gap-1">
                                    <i class="ri-calendar-line text-xs text-defaulttextcolor/50 dark:text-white/50"></i>
                                    <p class="text-xs text-defaulttextcolor/50 dark:text-white/50">{{ formatDate(note.note_date) }}</p>
                                  </div>
                                </div>
                                <!-- Botões de ação da nota -->
                                <div class="flex gap-1">
                                  <button 
                                    @click="editContactPersonNoteFromList(index, noteIndex)"
                                    class="w-6 h-6 bg-primary/10 hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 rounded flex items-center justify-center text-primary transition-colors"
                                    title="Editar nota"
                                  >
                                    <i class="ri-edit-line text-xs"></i>
                                  </button>
                                  <button 
                                    @click="deleteContactPersonNote(index, noteIndex)"
                                    class="w-6 h-6 bg-danger/10 hover:bg-danger/20 dark:bg-danger/20 dark:hover:bg-danger/30 rounded flex items-center justify-center text-danger transition-colors"
                                    title="Excluir nota"
                                  >
                                    <i class="ri-delete-bin-line text-xs"></i>
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center py-16">
                  <div class="max-w-md mx-auto">
                    <div class="w-24 h-24 bg-light-100 dark:bg-dark-700 rounded-full flex items-center justify-center mx-auto mb-6 border-4 border-dashed border-defaultborder dark:border-white/20">
                      <i class="ri-user-add-line text-4xl text-defaulttextcolor/40 dark:text-white/40"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-defaulttextcolor dark:text-white mb-3">Nenhuma pessoa de contato</h3>
                    <p class="text-defaulttextcolor/60 dark:text-white/60 mb-8 leading-relaxed">
                      Adicione pessoas de contato para facilitar a comunicação com esta empresa. 
                      Você pode incluir informações como nome, cargo, telefone e e-mail.
                    </p>
                    <button 
                      class="ti-btn ti-btn-primary-full !py-3 !px-6 rounded-lg flex items-center gap-2 mx-auto"
                      @click="openContactPersonModal"
                    >
                      <i class="ri-user-add-line"></i>
                      Adicionar Primeira Pessoa
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para Contact Person -->
    <div
      v-if="showContactPersonModal"
      class="hs-overlay fixed inset-0 z-[80] bg-black/50 flex items-center justify-center p-4"
      @click.self="closeContactPersonModal"
    >
      <div class="bg-bodybg dark:bg-dark rounded-xl shadow-lg max-w-lg w-full mx-auto max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-defaultborder dark:border-white/10 bg-light-100 dark:bg-dark-800">
          <h3 class="text-xl font-semibold text-defaulttextcolor dark:text-white">
            {{ contactPersonModalMode === 'create' ? 'Adicionar Pessoa de Contato' : 'Editar Pessoa de Contato' }}
          </h3>
          <button
            @click="closeContactPersonModal"
            class="text-defaulttextcolor/60 hover:text-defaulttextcolor dark:text-white/60 dark:hover:text-white transition-colors text-2xl"
          >
            ×
          </button>
        </div>
        
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="space-y-5">
            <!-- Informações Pessoais -->
            <div>
              <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white mb-3">Informações Pessoais</h4>
              
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="ti-form-label mb-1 text-xs">Nome</label>
                  <input 
                    v-model="contactPersonForm.first_name"
                    type="text"
                    class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                    placeholder="Nome"
                    required
                  />
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Sobrenome</label>
                  <input 
                    v-model="contactPersonForm.last_name"
                    type="text"
                    class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                    placeholder="Sobrenome"
                  />
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Função/Cargo</label>
                  <input 
                    v-model="contactPersonForm.role"
                    type="text"
                    class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                    placeholder="Ex: Gerente de Vendas"
                  />
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Telefone Móvel</label>
                  <input 
                    v-model="contactPersonForm.mobile"
                    type="tel"
                    class="ti-form-input rounded-md !py-1.5 !px-2 text-sm"
                    placeholder="(11) 99999-9999"
                  />
                </div>
              </div>
            </div>

            <!-- E-mails -->
            <div>
              <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white mb-3">E-mails</h4>
              
              <div v-for="(email, index) in contactPersonForm.emails" :key="index" class="flex gap-2 mb-2">
                <input 
                  v-model="contactPersonForm.emails[index]"
                  type="email"
                  class="ti-form-input rounded-lg flex-1 !py-2 !px-3"
                  :placeholder="`E-mail ${index + 1}`"
                />
                <button 
                  v-if="contactPersonForm.emails.length > 1"
                  @click="removeEmail(index)"
                  class="ti-btn ti-btn-danger-full !py-2 !px-3 rounded-lg"
                >
                  -
                </button>
              </div>
              
              <button 
                @click="addEmail"
                class="ti-btn ti-btn-secondary !py-2 !px-4 rounded-lg text-sm"
              >
                + Adicionar E-mail
              </button>
            </div>

            <!-- Notas -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Notas</h4>
                <button 
                  @click="addContactPersonNote"
                  class="ti-btn ti-btn-primary !py-1 !px-3 rounded-lg text-sm"
                >
                  + Nova Nota
                </button>
              </div>
              
              <div v-if="contactPersonForm.notes.length === 0" class="text-center py-6">
                <p class="text-defaulttextcolor/60 dark:text-white/60">Nenhuma nota adicionada</p>
              </div>

              <div v-else class="space-y-2">
                <div 
                  v-for="(note, index) in contactPersonForm.notes" 
                  :key="index"
                  class="bg-light-100 dark:bg-dark-700 rounded-lg p-3 relative"
                >
                  <div class="flex items-center justify-between mb-1">
                    <h5 class="font-medium text-defaulttextcolor dark:text-white text-sm">{{ note.name }}</h5>
                    <div class="flex gap-1">
                      <button 
                        @click="editContactPersonNoteInModal(index)"
                        class="w-6 h-6 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 rounded-md flex items-center justify-center text-blue-600 dark:text-blue-400 transition-colors"
                        title="Editar nota"
                      >
                        <i class="ri-edit-line text-xs"></i>
                      </button>
                      <button 
                        @click="removeContactPersonNote(index)"
                        class="w-6 h-6 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 rounded-md flex items-center justify-center text-red-600 dark:text-red-400 transition-colors"
                        title="Remover nota"
                      >
                        <i class="ri-delete-bin-line text-xs"></i>
                      </button>
                    </div>
                  </div>
                  <p class="text-sm text-defaulttextcolor/70 dark:text-white/70">{{ note.content }}</p>
                  <p class="text-xs text-defaulttextcolor/50 dark:text-white/50 mt-1">{{ formatDate(note.note_date) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex gap-3 justify-end p-6 border-t border-defaultborder dark:border-white/10 bg-light-50 dark:bg-dark-800">
          <button
            @click="closeContactPersonModal"
            class="ti-btn ti-btn-soft-secondary !py-2.5 !px-6 rounded-lg"
          >
            Cancelar
          </button>
          <button
            @click="saveContactPerson"
            class="ti-btn ti-btn-primary-full !py-2.5 !px-6 rounded-lg"
          >
            {{ contactPersonModalMode === 'create' ? 'Salvar Pessoa' : 'Atualizar Pessoa' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal para Nova Nota da Pessoa de Contato -->
    <div
      v-if="showContactPersonNoteModal"
      class="hs-overlay fixed inset-0 z-[100] bg-black/60 flex items-center justify-center p-4"
      @click.self="closeContactPersonNoteModal"
    >
      <div class="bg-bodybg dark:bg-dark rounded-xl shadow-lg max-w-md w-full mx-auto">
        <div class="flex items-center justify-between p-6 border-b border-defaultborder dark:border-white/10">
          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">
            {{ contactPersonNoteModalMode === 'create' ? 'Nova Nota' : 'Editar Nota' }}
          </h3>
          <button
            @click="closeContactPersonNoteModal"
            class="text-defaulttextcolor/60 hover:text-defaulttextcolor dark:text-white/60 dark:hover:text-white transition-colors text-xl"
          >
            ×
          </button>
        </div>
        
        <div class="p-5 space-y-3">
          <div>
            <label class="ti-form-label mb-1.5 text-sm">Título da Nota</label>
            <input 
              v-model="newContactPersonNote.name"
              type="text"
              class="ti-form-input rounded-lg !py-2 !px-3"
              placeholder="Título da nota"
            />
          </div>
          
          <div>
            <label class="ti-form-label mb-1.5 text-sm">Conteúdo</label>
            <textarea 
              v-model="newContactPersonNote.content"
              rows="3"
              class="ti-form-input rounded-lg !py-2 !px-3"
              placeholder="Conteúdo da nota..."
            ></textarea>
          </div>
          
          <div>
            <label class="ti-form-label mb-1.5 text-sm">Data</label>
            <input 
              v-model="newContactPersonNote.note_date"
              type="date"
              class="ti-form-input rounded-lg !py-2 !px-3"
            />
          </div>
        </div>

        <div class="flex gap-3 justify-end p-6 border-t border-defaultborder dark:border-white/10">
          <button
            @click="closeContactPersonNoteModal"
            class="ti-btn ti-btn-soft-secondary !py-2 !px-4 rounded-lg"
          >
            Cancelar
          </button>
          <button
            @click="saveContactPersonNote"
            class="ti-btn ti-btn-primary-full !py-2 !px-4 rounded-lg"
          >
            {{ contactPersonNoteModalMode === 'create' ? 'Adicionar' : 'Atualizar' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  mode: {
    type: String,
    required: true,
    validator: (value) => ['create', 'edit'].includes(value)
  },
  tempKey: {
    type: String,
    default: null
  },
  contact: {
    type: Object,
    default: null
  },
  user: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

// Estado das abas
const activeMainTab = ref('data')
const processing = ref(false)
const hasUnsavedChanges = ref(false)
const showContactPersonModal = ref(false)
const showContactPersonNoteModal = ref(false)

// Controle dos modais
const contactPersonModalMode = ref('create') // 'create' | 'edit'
const contactPersonFormIndex = ref(-1) // Índice da pessoa sendo editada
const contactPersonNoteModalMode = ref('create') // 'create' | 'edit'
const editingNoteIndex = ref(-1) // Índice da nota sendo editada

// Estado dos endereços colapsáveis
const collapsedSections = ref({
  visitingAddress: false,
  mailingAddress: true,
  billingAddress: true
})

// Funções para toggle dos endereços
const toggleSection = (section) => {
  collapsedSections.value[section] = !collapsedSections.value[section]
}

// Dados do formulário principal (baseado na tabela contacts)
const form = ref({
  // Campos da tabela contacts
  type: 'customer',
  name: '',
  email: '',
  phone: '',
  name_line: '',
  website: '',
  
  // Endereço de visita
  street_visiting: '',
  house_number_visiting: '',
  postal_code_visiting: '',
  city_visiting: '',
  state_visiting: '',
  country_visiting: 'Brasil',
  lat_visiting: null,
  lng_visiting: null,
  
  // Endereço de correspondência
  street_mailing: '',
  house_number_mailing: '',
  postal_code_mailing: '',
  city_mailing: '',
  state_mailing: '',
  country_mailing: '',
  lat_mailing: null,
  lng_mailing: null,
  
  // Endereço de cobrança
  street_billing: '',
  house_number_billing: '',
  postal_code_billing: '',
  city_billing: '',
  state_billing: '',
  country_billing: '',
  lat_billing: null,
  lng_billing: null,
  
  // Notas gerais do contato
  general_notes: ''
})

// Formulário para pessoa de contato (baseado na tabela contact_person)
const contactPersonForm = ref({
  first_name: '',
  last_name: '',
  mobile: '',
  role: '',
  emails: [''], // Para contact_person_emails
  notes: [] // Para contact_person_notes
})

// Lista de pessoas de contato
const contactPersons = ref([])

// Estado das notas
const newContactPersonNote = ref({
  name: '',
  content: '',
  note_date: new Date().toISOString().split('T')[0]
})

const pageTitle = computed(() => {
  return props.mode === 'create' ? 'Criar contato' : 'Editar contato'
})

const pageDescription = computed(() => {
  return props.mode === 'create' 
    ? 'Adicionar um novo contato ao sistema' 
    : 'Modificar informações do contato'
})

// Monitorar mudanças no formulário
watch(form, () => {
  hasUnsavedChanges.value = true
}, { deep: true })

// Carregar dados do contato se estiver editando
onMounted(() => {
  if (props.mode === 'edit' && props.contact) {
    Object.assign(form.value, props.contact)
    hasUnsavedChanges.value = false
  }
})

const handleSubmit = () => {
  if (processing.value) return

  processing.value = true

  const submitData = {
    ...form.value,
    temp_key: props.tempKey,
    contact_persons: contactPersons.value
  }

  const url = props.mode === 'create' 
    ? '/api/contacts' 
    : `/api/contacts/${props.contact.id}`
  
  const method = props.mode === 'create' ? 'post' : 'put'

  router[method](url, submitData, {
    preserveState: true,
    onSuccess: () => {
      processing.value = false
      hasUnsavedChanges.value = false
      router.visit('/contacts', {
        preserveState: false,
        replace: true
      })
    },
    onError: (errors) => {
      processing.value = false
      console.error('Erro ao salvar contato:', errors)
      
      if (Object.keys(errors).length > 0) {
        activeMainTab.value = 'data'
      }
    }
  })
}

const handleCancel = () => {
  if (hasUnsavedChanges.value) {
    if (confirm('Você tem alterações não salvas. Deseja realmente cancelar?')) {
      router.visit('/contacts')
    }
  } else {
    router.visit('/contacts')
  }
}

// Funções para gerenciamento de e-mails
const addEmail = () => {
  contactPersonForm.value.emails.push('')
}

const removeEmail = (index) => {
  contactPersonForm.value.emails.splice(index, 1)
}

// Funções para gerenciamento de notas da pessoa de contato
const addContactPersonNote = () => {
  contactPersonNoteModalMode.value = 'create'
  editingNoteIndex.value = -1
  newContactPersonNote.value = {
    name: '',
    content: '',
    note_date: new Date().toISOString().split('T')[0]
  }
  showContactPersonNoteModal.value = true
}

// Editar nota da pessoa de contato
const editContactPersonNote = (personIndex, noteIndex) => {
  const note = contactPersons.value[personIndex].notes[noteIndex]
  contactPersonNoteModalMode.value = 'edit'
  contactPersonFormIndex.value = personIndex
  editingNoteIndex.value = noteIndex
  
  newContactPersonNote.value = {
    name: note.name,
    content: note.content,
    note_date: note.note_date
  }
  
  showContactPersonNoteModal.value = true
}

// Editar nota da lista de pessoas já criadas
const editContactPersonNoteFromList = (personIndex, noteIndex) => {
  const note = contactPersons.value[personIndex].notes[noteIndex]
  contactPersonNoteModalMode.value = 'edit'
  contactPersonFormIndex.value = personIndex
  editingNoteIndex.value = noteIndex
  
  newContactPersonNote.value = {
    name: note.name,
    content: note.content,
    note_date: note.note_date
  }
  
  showContactPersonNoteModal.value = true
}

// Editar nota dentro do modal da pessoa de contato
const editContactPersonNoteInModal = (noteIndex) => {
  const note = contactPersonForm.value.notes[noteIndex]
  contactPersonNoteModalMode.value = 'edit'
  contactPersonFormIndex.value = -1 // Indica que estamos editando no modal
  editingNoteIndex.value = noteIndex
  
  newContactPersonNote.value = {
    name: note.name,
    content: note.content,
    note_date: note.note_date
  }
  
  showContactPersonNoteModal.value = true
}

const saveContactPersonNote = () => {
  if (!newContactPersonNote.value.name || !newContactPersonNote.value.content) {
    alert('Título e conteúdo são obrigatórios')
    return
  }

  if (contactPersonNoteModalMode.value === 'create') {
    // Adicionar nova nota
    contactPersonForm.value.notes.push({
      ...newContactPersonNote.value,
      id: Date.now()
    })
  } else {
    // Editar nota existente
    const personIndex = contactPersonFormIndex.value
    const noteIndex = editingNoteIndex.value
    
    if (personIndex >= 0 && noteIndex >= 0) {
      // Se estamos editando uma pessoa existente na lista
      contactPersons.value[personIndex].notes[noteIndex] = {
        ...contactPersons.value[personIndex].notes[noteIndex],
        ...newContactPersonNote.value
      }
    } else if (personIndex === -1 && noteIndex >= 0) {
      // Se estamos editando no formulário da pessoa de contato (modal)
      contactPersonForm.value.notes[noteIndex] = {
        ...contactPersonForm.value.notes[noteIndex],
        ...newContactPersonNote.value
      }
    }
  }

  // Limpar formulário
  newContactPersonNote.value = {
    name: '',
    content: '',
    note_date: new Date().toISOString().split('T')[0]
  }

  showContactPersonNoteModal.value = false
  hasUnsavedChanges.value = true
}

const removeContactPersonNote = (index) => {
  if (confirm('Tem certeza que deseja remover esta nota?')) {
    contactPersonForm.value.notes.splice(index, 1)
  }
}

// Deletar nota de pessoa existente
const deleteContactPersonNote = (personIndex, noteIndex) => {
  if (confirm('Tem certeza que deseja excluir esta nota?')) {
    contactPersons.value[personIndex].notes.splice(noteIndex, 1)
    hasUnsavedChanges.value = true
  }
}

const closeContactPersonNoteModal = () => {
  const hasData = newContactPersonNote.value.name || newContactPersonNote.value.content
  
  if (hasData && confirm('Você tem dados não salvos. Deseja realmente fechar?')) {
    showContactPersonNoteModal.value = false
    // Limpar formulário
    newContactPersonNote.value = {
      name: '',
      content: '',
      note_date: new Date().toISOString().split('T')[0]
    }
  } else if (!hasData) {
    showContactPersonNoteModal.value = false
  }
}

const saveContactPerson = () => {
  // Validar campos obrigatórios
  if (!contactPersonForm.value.first_name) {
    alert('Nome é obrigatório')
    return
  }

  // Filtrar e-mails vazios
  const validEmails = contactPersonForm.value.emails.filter(email => email.trim() !== '')

  const personData = {
    id: contactPersonModalMode.value === 'edit' ? contactPersons.value[contactPersonFormIndex.value].id : Date.now(),
    first_name: contactPersonForm.value.first_name,
    last_name: contactPersonForm.value.last_name,
    mobile: contactPersonForm.value.mobile,
    role: contactPersonForm.value.role,
    emails: validEmails,
    notes: [...contactPersonForm.value.notes]
  }

  if (contactPersonModalMode.value === 'create') {
    // Adicionar nova pessoa
    contactPersons.value.push(personData)
  } else {
    // Editar pessoa existente
    contactPersons.value[contactPersonFormIndex.value] = personData
  }

  // Limpar formulário
  contactPersonForm.value = {
    first_name: '',
    last_name: '',
    mobile: '',
    role: '',
    emails: [''],
    notes: []
  }

  showContactPersonModal.value = false
  hasUnsavedChanges.value = true
}

// Abrir modal para criar nova pessoa de contato
const openContactPersonModal = () => {
  contactPersonModalMode.value = 'create'
  contactPersonFormIndex.value = -1
  resetContactPersonForm(false)
  showContactPersonModal.value = true
}

// Editar pessoa de contato
const editContactPerson = (index) => {
  const person = contactPersons.value[index]
  contactPersonModalMode.value = 'edit'
  contactPersonFormIndex.value = index
  
  contactPersonForm.value = {
    first_name: person.first_name,
    last_name: person.last_name,
    mobile: person.mobile,
    role: person.role,
    emails: person.emails.length > 0 ? [...person.emails] : [''],
    notes: [...person.notes]
  }
  
  showContactPersonModal.value = true
}

// Deletar pessoa de contato
const deleteContactPerson = (index) => {
  const person = contactPersons.value[index]
  const personName = `${person.first_name} ${person.last_name}`.trim()
  
  if (confirm(`Tem certeza que deseja excluir a pessoa de contato "${personName}"?`)) {
    contactPersons.value.splice(index, 1)
    hasUnsavedChanges.value = true
  }
}

const closeContactPersonModal = () => {
  // Verificar se há dados não salvos
  const hasData = contactPersonForm.value.first_name || 
                 contactPersonForm.value.last_name || 
                 contactPersonForm.value.mobile || 
                 contactPersonForm.value.role ||
                 contactPersonForm.value.emails.some(email => email.trim() !== '') ||
                 contactPersonForm.value.notes.length > 0
  
  if (hasData && confirm('Você tem dados não salvos. Deseja realmente fechar?')) {
    resetContactPersonForm()
  } else if (!hasData) {
    resetContactPersonForm()
  }
}

// Função auxiliar para resetar o formulário da pessoa de contato
const resetContactPersonForm = (closeModal = true) => {
  if (closeModal) {
    showContactPersonModal.value = false
  }
  
  contactPersonModalMode.value = 'create'
  contactPersonFormIndex.value = -1
  
  contactPersonForm.value = {
    first_name: '',
    last_name: '',
    mobile: '',
    role: '',
    emails: [''],
    notes: []
  }
}

// Função para formatar data
const formatDate = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR')
}
</script>