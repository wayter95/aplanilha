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
                    <div class="space-y-5">
                      <!-- INFORMAÇÕES BÁSICAS -->
                      <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Informações Básicas</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                          <div>
                            <label class="ti-form-label mb-1.5 text-sm">Tipo de Contato</label>
                            <select v-model="form.type" class="ti-form-select rounded-lg !py-2 !px-3">
                              <option value="customer">Cliente</option>
                              <option value="supplier">Fornecedor</option>
                              <option value="location">Local</option>
                            </select>
                          </div>
                          <div class="md:col-span-2">
                            <label class="ti-form-label mb-1.5 text-sm">Nome da Empresa *</label>
                            <input 
                              v-model="form.name"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3"
                              placeholder="Nome da empresa"
                              required
                            />
                          </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                          <div class="xl:col-span-2">
                            <label class="ti-form-label mb-1.5 text-sm">E-mail Principal</label>
                            <input 
                              v-model="form.email"
                              type="email"
                              class="ti-form-input rounded-lg !py-2 !px-3"
                              placeholder="email@empresa.com"
                            />
                          </div>
                          <div>
                            <label class="ti-form-label mb-1.5 text-sm">Telefone Principal</label>
                            <input 
                              v-model="form.phone"
                              type="tel"
                              class="ti-form-input rounded-lg !py-2 !px-3"
                              placeholder="(11) 9999-9999"
                            />
                          </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                          <div>
                            <label class="ti-form-label mb-1.5 text-sm">Nome da Linha</label>
                            <input 
                              v-model="form.name_line"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3"
                              placeholder="Linha de negócio"
                            />
                          </div>
                          <div>
                            <label class="ti-form-label mb-1.5 text-sm">Website</label>
                            <input 
                              v-model="form.website"
                              type="url"
                              class="ti-form-input rounded-lg !py-2 !px-3"
                              placeholder="https://www.empresa.com"
                            />
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE VISITA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-4 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
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
                          class="p-4 space-y-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 gap-3">
                            <div class="md:col-span-3 xl:col-span-4">
                              <label class="ti-form-label mb-1.5 text-sm">Rua</label>
                              <input 
                                v-model="form.street_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Número</label>
                              <input 
                                v-model="form.house_number_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="123"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">CEP</label>
                              <input 
                                v-model="form.postal_code_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1.5 text-sm">Cidade</label>
                              <input 
                                v-model="form.city_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Cidade"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Estado</label>
                              <input 
                                v-model="form.state_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="UF"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">País</label>
                              <input 
                                v-model="form.country_visiting"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE CORRESPONDÊNCIA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-4 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
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
                          class="p-4 space-y-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 gap-3">
                            <div class="md:col-span-3 xl:col-span-4">
                              <label class="ti-form-label mb-1.5 text-sm">Rua</label>
                              <input 
                                v-model="form.street_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Número</label>
                              <input 
                                v-model="form.house_number_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="123"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">CEP</label>
                              <input 
                                v-model="form.postal_code_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1.5 text-sm">Cidade</label>
                              <input 
                                v-model="form.city_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Cidade"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Estado</label>
                              <input 
                                v-model="form.state_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="UF"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">País</label>
                              <input 
                                v-model="form.country_mailing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- ENDEREÇO DE COBRANÇA -->
                      <div class="border border-defaultborder dark:border-white/10 rounded-lg">
                        <div 
                          class="flex items-center justify-between p-4 cursor-pointer bg-light-50 dark:bg-dark-800 rounded-t-lg hover:bg-light-100 dark:hover:bg-dark-700 transition-colors"
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
                          class="p-4 space-y-3 border-t border-defaultborder dark:border-white/10"
                        >
                          <div class="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-5 gap-3">
                            <div class="md:col-span-3 xl:col-span-4">
                              <label class="ti-form-label mb-1.5 text-sm">Rua</label>
                              <input 
                                v-model="form.street_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Nome da rua"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Número</label>
                              <input 
                                v-model="form.house_number_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="123"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">CEP</label>
                              <input 
                                v-model="form.postal_code_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="00000-000"
                              />
                            </div>
                            <div class="xl:col-span-2">
                              <label class="ti-form-label mb-1.5 text-sm">Cidade</label>
                              <input 
                                v-model="form.city_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="Cidade"
                              />
                            </div>
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">Estado</label>
                              <input 
                                v-model="form.state_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="UF"
                              />
                            </div>
                          </div>

                          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                              <label class="ti-form-label mb-1.5 text-sm">País</label>
                              <input 
                                v-model="form.country_billing"
                                type="text"
                                class="ti-form-input rounded-lg !py-2 !px-3"
                                placeholder="País"
                              />
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- NOTAS GERAIS -->
                      <div class="space-y-3">
                        <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">Observações Gerais</h3>
                        <div>
                          <textarea 
                            v-model="form.general_notes"
                            rows="3"
                            class="ti-form-input rounded-lg !py-2 !px-3"
                            placeholder="Observações gerais sobre o contato..."
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
              <div v-show="activeMainTab === 'contact-persons'" class="text-center py-12">
                <button 
                  class="ti-btn ti-btn-primary-full !py-3 !px-6 rounded-lg"
                  @click="showContactPersonModal = true"
                >
                  + Adicionar Pessoa de Contato
                </button>

                <div v-if="contactPersons.length > 0" class="mt-8">
                  <div 
                    v-for="person in contactPersons"
                    :key="person.id"
                    class="bg-light-100 dark:bg-dark-700 rounded-lg p-6 mb-4 text-left"
                  >
                    <h4 class="font-semibold text-defaulttextcolor dark:text-white">{{ person.first_name }} {{ person.last_name }}</h4>
                    <p class="text-sm text-defaulttextcolor/60 dark:text-white/60">{{ person.role }}</p>
                    <p class="text-sm text-defaulttextcolor/60 dark:text-white/60">{{ person.mobile }}</p>
                    <div v-if="person.emails && person.emails.length > 0">
                      <p v-for="email in person.emails" :key="email" class="text-sm text-defaulttextcolor/60 dark:text-white/60">{{ email }}</p>
                    </div>
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
      <div class="bg-bodybg dark:bg-dark rounded-xl shadow-lg max-w-2xl w-full mx-auto max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-defaultborder dark:border-white/10 bg-light-100 dark:bg-dark-800">
          <h3 class="text-xl font-semibold text-defaulttextcolor dark:text-white">
            Adicionar Pessoa de Contato
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
              
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="ti-form-label mb-1.5 text-sm">Nome *</label>
                  <input 
                    v-model="contactPersonForm.first_name"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-3"
                    placeholder="Nome"
                    required
                  />
                </div>
                <div>
                  <label class="ti-form-label mb-1.5 text-sm">Sobrenome</label>
                  <input 
                    v-model="contactPersonForm.last_name"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-3"
                    placeholder="Sobrenome"
                  />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3 mt-3">
                <div>
                  <label class="ti-form-label mb-1.5 text-sm">Função/Cargo</label>
                  <input 
                    v-model="contactPersonForm.role"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-3"
                    placeholder="Ex: Gerente de Vendas"
                  />
                </div>
                <div>
                  <label class="ti-form-label mb-1.5 text-sm">Telefone Móvel</label>
                  <input 
                    v-model="contactPersonForm.mobile"
                    type="tel"
                    class="ti-form-input rounded-lg !py-2 !px-3"
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
                  class="bg-light-100 dark:bg-dark-700 rounded-lg p-3"
                >
                  <div class="flex items-center justify-between mb-1">
                    <h5 class="font-medium text-defaulttextcolor dark:text-white text-sm">{{ note.name }}</h5>
                    <button 
                      @click="removeContactPersonNote(index)"
                      class="text-red-500 hover:text-red-700 text-xs"
                    >
                      Remover
                    </button>
                  </div>
                  <p class="text-sm text-defaulttextcolor/70 dark:text-white/70">{{ note.content }}</p>
                  <p class="text-xs text-defaulttextcolor/50 dark:text-white/50 mt-1">{{ note.note_date }}</p>
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
            Salvar Pessoa
          </button>
        </div>
      </div>
    </div>

    <!-- Modal para Nova Nota da Pessoa de Contato -->
    <div
      v-if="showContactPersonNoteModal"
      class="hs-overlay fixed inset-0 z-[90] bg-black/50 flex items-center justify-center p-4"
      @click.self="closeContactPersonNoteModal"
    >
      <div class="bg-bodybg dark:bg-dark rounded-xl shadow-lg max-w-md w-full mx-auto">
        <div class="flex items-center justify-between p-6 border-b border-defaultborder dark:border-white/10">
          <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white">
            Nova Nota
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
            Adicionar
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
  showContactPersonNoteModal.value = true
}

const saveContactPersonNote = () => {
  if (!newContactPersonNote.value.name || !newContactPersonNote.value.content) {
    alert('Título e conteúdo são obrigatórios')
    return
  }

  contactPersonForm.value.notes.push({
    ...newContactPersonNote.value,
    id: Date.now()
  })

  // Limpar formulário
  newContactPersonNote.value = {
    name: '',
    content: '',
    note_date: new Date().toISOString().split('T')[0]
  }

  showContactPersonNoteModal.value = false
}

const removeContactPersonNote = (index) => {
  contactPersonForm.value.notes.splice(index, 1)
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

  // Adicionar pessoa à lista
  contactPersons.value.push({
    id: Date.now(), // ID temporário
    first_name: contactPersonForm.value.first_name,
    last_name: contactPersonForm.value.last_name,
    mobile: contactPersonForm.value.mobile,
    role: contactPersonForm.value.role,
    emails: validEmails,
    notes: [...contactPersonForm.value.notes]
  })

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

const closeContactPersonModal = () => {
  // Verificar se há dados não salvos
  const hasData = contactPersonForm.value.first_name || 
                 contactPersonForm.value.last_name || 
                 contactPersonForm.value.mobile || 
                 contactPersonForm.value.role ||
                 contactPersonForm.value.emails.some(email => email.trim() !== '') ||
                 contactPersonForm.value.notes.length > 0
  
  if (hasData && confirm('Você tem dados não salvos. Deseja realmente fechar?')) {
    showContactPersonModal.value = false
    // Limpar formulário
    contactPersonForm.value = {
      first_name: '',
      last_name: '',
      mobile: '',
      role: '',
      emails: [''],
      notes: []
    }
  } else if (!hasData) {
    showContactPersonModal.value = false
  }
}
</script>