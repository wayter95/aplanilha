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
            <div class="p-6">
              <!-- Aba Dados -->
              <div v-show="activeMainTab === 'data'" class="space-y-6">
                <!-- INFORMAÇÕES BÁSICAS -->
                <div class="bg-light-50 dark:bg-dark-800 rounded-lg p-6">
                  <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white mb-4">Informações Básicas</h3>
                  
                  <div class="grid grid-cols-12 gap-4">
                    <!-- Primeira linha -->
                    <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-2">
                      <label class="ti-form-label mb-2 text-sm">Tipo</label>
                      <select 
                        v-model="form.type" 
                        class="ti-form-select rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.type }"
                        required
                      >
                        <option value="customer">Cliente</option>
                        <option value="supplier">Fornecedor</option>
                        <option value="location">Local</option>
                      </select>
                      <div v-if="backendErrors?.type" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.type[0] }}
                      </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 md:col-span-9 lg:col-span-6">
                      <label class="ti-form-label mb-2 text-sm">Nome da Empresa</label>
                      <input 
                        v-model="form.name"
                        type="text"
                        class="ti-form-input rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.name }"
                        placeholder="Nome da empresa"
                        required
                      />
                      <div v-if="backendErrors?.name" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.name[0] }}
                      </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 md:col-span-4 lg:col-span-4">
                      <label class="ti-form-label mb-2 text-sm">Linha de Negócio</label>
                      <input 
                        v-model="form.name_line"
                        type="text"
                        class="ti-form-input rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.name_line }"
                        placeholder="Linha de negócio"
                        required
                      />
                      <div v-if="backendErrors?.name_line" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.name_line[0] }}
                      </div>
                    </div>

                    <!-- Segunda linha -->
                    <div class="col-span-12 sm:col-span-6 md:col-span-3 lg:col-span-3">
                      <label class="ti-form-label mb-2 text-sm">Telefone</label>
                      <input 
                        v-model="form.phone"
                        type="tel"
                        class="ti-form-input rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.phone }"
                        placeholder="(11) 9999-9999"
                        maxlength="15"
                        required
                      />
                      <div v-if="backendErrors?.phone" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.phone[0] }}
                      </div>
                    </div>
                    
                    <div class="col-span-12 sm:col-span-6 md:col-span-5 lg:col-span-5">
                      <label class="ti-form-label mb-2 text-sm">E-mail Principal</label>
                      <input 
                        v-model="form.email"
                        type="email"
                        class="ti-form-input rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.email }"
                        placeholder="email@empresa.com"
                        required
                      />
                      <div v-if="backendErrors?.email" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.email[0] }}
                      </div>
                    </div>
                    
                    <div class="col-span-12 sm:col-span-6 md:col-span-4 lg:col-span-4">
                      <label class="ti-form-label mb-2 text-sm">Website</label>
                      <input 
                        v-model="form.website"
                        type="url"
                        class="ti-form-input rounded-lg !py-2.5 !px-3 w-full"
                        :class="{ 'border-red-500 focus:border-red-500': backendErrors?.website }"
                        placeholder="https://site.com"
                        required
                      />
                      <div v-if="backendErrors?.website" class="text-red-500 text-sm mt-1">
                        {{ backendErrors.website[0] }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- ENDEREÇOS EM DUAS COLUNAS -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                  <!-- COLUNA ESQUERDA -->
                  <div class="space-y-6">
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
                        class="p-4 border-t border-defaultborder dark:border-white/10"
                      >
                        <div class="grid grid-cols-12 gap-3">
                          <div class="col-span-12 sm:col-span-8 md:col-span-7">
                            <label class="ti-form-label mb-1 text-sm">Rua</label>
                            <input 
                              v-model="form.street_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.street_visiting }"
                              placeholder="Nome da rua"
                              required
                            />
                            <div v-if="backendErrors?.street_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.street_visiting[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">Nº</label>
                            <input 
                              v-model="form.house_number_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.house_number_visiting }"
                              placeholder="123"
                              required
                              maxlength="10"
                            />
                            <div v-if="backendErrors?.house_number_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.house_number_visiting[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-3">
                            <label class="ti-form-label mb-1 text-sm">CEP</label>
                            <input 
                              v-model="form.postal_code_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.postal_code_visiting }"
                              placeholder="00000-000"
                              maxlength="9"
                              required
                            />
                            <div v-if="backendErrors?.postal_code_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.postal_code_visiting[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-6 md:col-span-6">
                            <label class="ti-form-label mb-1 text-sm">Cidade</label>
                            <input 
                              v-model="form.city_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.city_visiting }"
                              placeholder="Cidade"
                              required
                            />
                            <div v-if="backendErrors?.city_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.city_visiting[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-3 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">UF</label>
                            <input 
                              v-model="form.state_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.state_visiting }"
                              placeholder="SP"
                              maxlength="2"
                              required
                            />
                            <div v-if="backendErrors?.state_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.state_visiting[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-9 md:col-span-4">
                            <label class="ti-form-label mb-1 text-sm">País</label>
                            <input 
                              v-model="form.country_visiting"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.country_visiting }"
                              placeholder="Brasil"
                              required
                            />
                            <div v-if="backendErrors?.country_visiting" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.country_visiting[0] }}
                            </div>
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
                        class="p-4 border-t border-defaultborder dark:border-white/10"
                      >
                        <!-- Checkbox para copiar endereço de visita -->
                        <div class="mb-4">
                          <div class="flex items-center gap-2">
                            <input 
                              type="checkbox"
                              v-model="copyToMailing"
                              id="copyToMailing"
                              class="form-check-input"
                            />
                            <label class="text-sm text-defaulttextcolor dark:text-white cursor-pointer" for="copyToMailing">
                              Mesmo endereço de visita
                            </label>
                          </div>
                        </div>
                        
                        <div class="grid grid-cols-12 gap-3" :class="{ 'opacity-50 pointer-events-none': copyToMailing }">
                          <div class="col-span-12 sm:col-span-8 md:col-span-7">
                            <label class="ti-form-label mb-1 text-sm">Rua</label>
                            <input 
                              v-model="form.street_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.street_mailing }"
                              placeholder="Nome da rua"
                              required
                            />
                            <div v-if="backendErrors?.street_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.street_mailing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">Nº</label>
                            <input 
                              v-model="form.house_number_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.house_number_mailing }"
                              placeholder="123"
                              maxlength="10"
                              required
                            />
                            <div v-if="backendErrors?.house_number_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.house_number_mailing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-3">
                            <label class="ti-form-label mb-1 text-sm">CEP</label>
                            <input 
                              v-model="form.postal_code_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.postal_code_mailing }"
                              placeholder="00000-000"
                              maxlength="9"
                              required
                            />
                            <div v-if="backendErrors?.postal_code_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.postal_code_mailing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-6 md:col-span-6">
                            <label class="ti-form-label mb-1 text-sm">Cidade</label>
                            <input 
                              v-model="form.city_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.city_mailing }"
                              placeholder="Cidade"
                              required
                            />
                            <div v-if="backendErrors?.city_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.city_mailing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-3 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">UF</label>
                            <input 
                              v-model="form.state_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.state_mailing }"
                              placeholder="SP"
                              maxlength="2"
                              required
                            />
                            <div v-if="backendErrors?.state_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.state_mailing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-9 md:col-span-4">
                            <label class="ti-form-label mb-1 text-sm">País</label>
                            <input 
                              v-model="form.country_mailing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.country_mailing }"
                              placeholder="Brasil"
                              required
                            />
                            <div v-if="backendErrors?.country_mailing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.country_mailing[0] }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- COLUNA DIREITA -->
                  <div class="space-y-6">
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
                        class="p-4 border-t border-defaultborder dark:border-white/10"
                      >
                        <!-- Checkbox para copiar endereço de visita -->
                        <div class="mb-4">
                          <div class="flex items-center gap-2">
                            <input 
                              type="checkbox"
                              v-model="copyToBilling"
                              id="copyToBilling"
                              class="form-check-input"
                            />
                            <label class="text-sm text-defaulttextcolor dark:text-white cursor-pointer" for="copyToBilling">
                              Mesmo endereço de visita
                            </label>
                          </div>
                        </div>
                        
                        <div class="grid grid-cols-12 gap-3" :class="{ 'opacity-50 pointer-events-none': copyToBilling }">
                          <div class="col-span-12 sm:col-span-8 md:col-span-7">
                            <label class="ti-form-label mb-1 text-sm">Rua</label>
                            <input 
                              v-model="form.street_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.street_billing }"
                              placeholder="Nome da rua"
                              required
                            />
                            <div v-if="backendErrors?.street_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.street_billing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">Nº</label>
                            <input 
                              v-model="form.house_number_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.house_number_billing }"
                              placeholder="123"
                              maxlength="10"
                              required
                            />
                            <div v-if="backendErrors?.house_number_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.house_number_billing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-4 md:col-span-3">
                            <label class="ti-form-label mb-1 text-sm">CEP</label>
                            <input 
                              v-model="form.postal_code_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.postal_code_billing }"
                              placeholder="00000-000"
                              maxlength="9"
                              required
                            />
                            <div v-if="backendErrors?.postal_code_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.postal_code_billing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-6 md:col-span-6">
                            <label class="ti-form-label mb-1 text-sm">Cidade</label>
                            <input 
                              v-model="form.city_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.city_billing }"
                              placeholder="Cidade"
                              required
                            />
                            <div v-if="backendErrors?.city_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.city_billing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-3 md:col-span-2">
                            <label class="ti-form-label mb-1 text-sm">UF</label>
                            <input 
                              v-model="form.state_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.state_billing }"
                              placeholder="SP"
                              maxlength="2"
                              required
                            />
                            <div v-if="backendErrors?.state_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.state_billing[0] }}
                            </div>
                          </div>
                          <div class="col-span-12 sm:col-span-9 md:col-span-4">
                            <label class="ti-form-label mb-1 text-sm">País</label>
                            <input 
                              v-model="form.country_billing"
                              type="text"
                              class="ti-form-input rounded-lg !py-2 !px-3 w-full"
                              :class="{ 'border-red-500 focus:border-red-500': backendErrors?.country_billing }"
                              placeholder="Brasil"
                              required
                            />
                            <div v-if="backendErrors?.country_billing" class="text-red-500 text-sm mt-1">
                              {{ backendErrors.country_billing[0] }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- OBSERVAÇÕES GERAIS -->
                    <div class="bg-light-50 dark:bg-dark-800 rounded-lg p-6">
                      <h3 class="text-lg font-semibold text-defaulttextcolor dark:text-white mb-4">Observações Gerais</h3>
                      <div>
                        <textarea 
                          v-model="form.general_notes"
                          rows="4"
                          class="ti-form-input rounded-lg !py-3 !px-4"
                          placeholder="Observações gerais sobre o contato..."
                        ></textarea>
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

                <div v-if="contactPersons.length > 0" class="space-y-4">
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                      v-for="(person, index) in contactPersons"
                      :key="person.id"
                      class="bg-bodybg dark:bg-dark-800 rounded-xl border border-defaultborder dark:border-white/10 p-4 hover:shadow-lg hover:border-primary/40 transition-all duration-300 relative group flex flex-col"
                    >
                      <!-- Badge com número da pessoa -->
                      <div class="absolute -top-2 -left-2 w-6 h-6 bg-primary text-white text-xs rounded-full flex items-center justify-center font-bold">
                        {{ index + 1 }}
                      </div>
                      
                      <!-- Header compacto com avatar e ações -->
                      <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                          <div class="w-10 h-10 bg-primary/10 dark:bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="ri-user-line text-primary text-lg"></i>
                          </div>
                          <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-defaulttextcolor dark:text-white text-sm truncate" :title="`${person.first_name} ${person.last_name}`">
                              {{ person.first_name }} {{ person.last_name }}
                            </h4>
                            <p v-if="person.role" class="text-xs text-primary font-medium truncate" :title="person.role">
                              {{ person.role }}
                            </p>
                          </div>
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="flex gap-1 flex-shrink-0">
                          <button 
                            @click="editContactPerson(index)"
                            class="w-7 h-7 bg-primary/10 hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 rounded-lg flex items-center justify-center text-primary transition-all hover:scale-110"
                            title="Editar pessoa"
                          >
                            <i class="ri-edit-line text-xs"></i>
                          </button>
                          <button 
                            @click="deleteContactPerson(index)"
                            class="w-7 h-7 bg-danger/10 hover:bg-danger/20 dark:bg-danger/20 dark:hover:bg-danger/30 rounded-lg flex items-center justify-center text-danger transition-all hover:scale-110"
                            title="Excluir pessoa"
                          >
                            <i class="ri-delete-bin-line text-xs"></i>
                          </button>
                        </div>
                      </div>
                      
                      <!-- Informações de contato expandidas -->
                      <div class="space-y-2 mb-3 flex-1">
                        <!-- Linha única com telefone + primeiro e-mail (quando existir) -->
                        <div class="flex items-center gap-3 text-xs text-defaulttextcolor/70 dark:text-white/70">
                          <div v-if="person.mobile" class="flex items-center gap-1.5">
                            <i class="ri-phone-line text-primary text-sm flex-shrink-0"></i>
                            <span class="truncate" :title="person.mobile">{{ person.mobile }}</span>
                          </div>

                          <div v-if="person.emails && person.emails.length > 0" class="flex items-center gap-1.5">
                            <i class="ri-mail-line text-primary text-sm flex-shrink-0"></i>
                            <span class="truncate" :title="person.emails[0]">{{ person.emails[0] }}</span>
                          </div>
                        </div>

                        <!-- E-mails adicionais (exibe a partir do segundo) -->
                        <div v-if="person.emails && person.emails.length > 1" class="mt-2 space-y-1">
                          <div v-for="(email, emailIndex) in person.emails.slice(1, 4)" :key="emailIndex" class="flex items-center gap-1.5 text-xs text-defaulttextcolor/70 dark:text-white/70 pl-0">
                            <i class="ri-mail-line text-primary text-sm flex-shrink-0 opacity-70"></i>
                            <span class="truncate flex-1" :title="email">{{ email }}</span>
                          </div>
                          <div v-if="person.emails.length > 4" class="text-xs text-defaulttextcolor/50 dark:text-white/50 pl-5">
                            +{{ person.emails.length - 4 }} mais
                          </div>
                        </div>

                        <!-- Notas expandidas -->
                        <div v-if="person.notes && person.notes.length > 0" class="border-t border-defaultborder dark:border-white/10 pt-2 mt-3">
                          <div class="flex items-center gap-1.5 text-xs font-medium text-defaulttextcolor dark:text-white mb-2">
                            <i class="ri-sticky-note-line text-primary text-sm"></i>
                            <span>Notas ({{ person.notes.length }})</span>
                          </div>
                          
                          <div class="space-y-2">
                            <div 
                              v-for="(note, noteIndex) in person.notes.slice(0, 2)" 
                              :key="note.id"
                              class="bg-light-50 dark:bg-dark-700 rounded-lg p-2 border border-defaultborder/20 dark:border-white/10"
                            >
                              <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                  <h6 class="font-medium text-defaulttextcolor dark:text-white text-xs mb-1 truncate" :title="note.name">
                                    {{ note.name }}
                                  </h6>
                                  <p class="text-xs text-defaulttextcolor/70 dark:text-white/70 line-clamp-2 leading-relaxed" :title="note.content">
                                    {{ note.content }}
                                  </p>
                                  <div class="flex items-center gap-1 mt-1">
                                    <i class="ri-calendar-line text-xs text-defaulttextcolor/50 dark:text-white/50"></i>
                                    <span class="text-xs text-defaulttextcolor/50 dark:text-white/50">{{ formatDate(note.note_date) }}</span>
                                  </div>
                                </div>
                                <!-- Botões de ação da nota -->
                                <div class="flex gap-1 flex-shrink-0">
                                  <button 
                                    @click="editContactPersonNoteFromList(index, noteIndex)"
                                    class="w-5 h-5 bg-primary/10 hover:bg-primary/20 dark:bg-primary/20 dark:hover:bg-primary/30 rounded flex items-center justify-center text-primary transition-all"
                                    title="Editar nota"
                                  >
                                    <i class="ri-edit-line text-xs"></i>
                                  </button>
                                  <button 
                                    @click="deleteContactPersonNote(index, noteIndex)"
                                    class="w-5 h-5 bg-danger/10 hover:bg-danger/20 dark:bg-danger/20 dark:hover:bg-danger/30 rounded flex items-center justify-center text-danger transition-all"
                                    title="Excluir nota"
                                  >
                                    <i class="ri-delete-bin-line text-xs"></i>
                                  </button>
                                </div>
                              </div>
                            </div>
                            
                            <!-- Indicador de mais notas -->
                            <div v-if="person.notes.length > 2" class="text-xs text-defaulttextcolor/50 dark:text-white/50 text-center py-1">
                              +{{ person.notes.length - 2 }} nota{{ person.notes.length - 2 !== 1 ? 's' : '' }} adicional{{ person.notes.length - 2 !== 1 ? 'is' : '' }}
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
      <div class="bg-bodybg dark:bg-dark rounded-xl shadow-lg max-w-sm w-full mx-auto max-h-[85vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-3 border-b border-defaultborder dark:border-white/10 bg-light-100 dark:bg-dark-800">
          <h3 class="text-base font-semibold text-defaulttextcolor dark:text-white">
            {{ contactPersonModalMode === 'create' ? 'Adicionar Pessoa' : 'Editar Pessoa' }}
          </h3>
          <button
            @click="closeContactPersonModal"
            class="text-defaulttextcolor/60 hover:text-defaulttextcolor dark:text-white/60 dark:hover:text-white transition-colors text-lg"
          >
            ×
          </button>
        </div>
        
        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-3">
          <div class="space-y-4">
            <!-- Informações Pessoais -->
            <div>
              <h4 class="text-sm font-semibold text-defaulttextcolor dark:text-white mb-3">Informações Pessoais</h4>
              
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="ti-form-label mb-1 text-xs">Nome</label>
                  <input 
                    v-model="contactPersonForm.first_name"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-2 text-sm w-full"
                    :class="{ 'border-red-500 focus:border-red-500': contactPersonErrors?.first_name }"
                    placeholder="Nome"
                    required
                  />
                  <div v-if="contactPersonErrors?.first_name" class="text-red-500 text-xs mt-1">
                    {{ contactPersonErrors.first_name[0] }}
                  </div>
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Sobrenome</label>
                  <input 
                    v-model="contactPersonForm.last_name"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-2 text-sm w-full"
                    :class="{ 'border-red-500 focus:border-red-500': contactPersonErrors?.last_name }"
                    placeholder="Sobrenome"
                    required
                  />
                  <div v-if="contactPersonErrors?.last_name" class="text-red-500 text-xs mt-1">
                    {{ contactPersonErrors.last_name[0] }}
                  </div>
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Função</label>
                  <input 
                    v-model="contactPersonForm.role"
                    type="text"
                    class="ti-form-input rounded-lg !py-2 !px-2 text-sm w-full"
                    :class="{ 'border-red-500 focus:border-red-500': contactPersonErrors?.role }"
                    placeholder="Gerente"
                    required
                  />
                  <div v-if="contactPersonErrors?.role" class="text-red-500 text-xs mt-1">
                    {{ contactPersonErrors.role[0] }}
                  </div>
                </div>
                <div>
                  <label class="ti-form-label mb-1 text-xs">Telefone</label>
                  <input 
                    v-model="contactPersonForm.mobile"
                    type="tel"
                    class="ti-form-input rounded-lg !py-2 !px-2 text-sm w-full"
                    :class="{ 'border-red-500 focus:border-red-500': contactPersonErrors?.mobile }"
                    placeholder="(11) 99999-9999"
                    required
                  />
                  <div v-if="contactPersonErrors?.mobile" class="text-red-500 text-xs mt-1">
                    {{ contactPersonErrors.mobile[0] }}
                  </div>
                </div>
              </div>
            </div>

            <!-- E-mails -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-defaulttextcolor dark:text-white">E-mails</h4>
                <button 
                  @click="addEmail"
                  class="ti-btn ti-btn-secondary !py-1 !px-2 rounded text-xs"
                >
                  + Add
                </button>
              </div>
              
              <div class="space-y-2">
                <div v-for="(email, index) in contactPersonForm.emails" :key="index" class="space-y-2">
                  <div class="flex items-center gap-1">
                    <input 
                      v-model="contactPersonForm.emails[index]"
                      type="email"
                      class="ti-form-input rounded-lg flex-1 !py-2 !px-2 text-sm"
                      :class="{ 'border-red-500 focus:border-red-500': contactPersonErrors?.emails }"
                      :placeholder="`E-mail ${index + 1}`"
                    />
                    <button 
                      v-if="contactPersonForm.emails.length > 1"
                      @click="removeEmail(index)"
                      class="ti-btn ti-btn-danger-full !py-2 !px-2 rounded flex-shrink-0"
                      title="Remover"
                    >
                      <i class="ri-delete-bin-line text-xs"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="contactPersonErrors?.emails" class="text-red-500 text-xs mt-1">
                {{ contactPersonErrors.emails[0] }}
              </div>
            </div>

            <!-- Notas -->
            <div>
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-defaulttextcolor dark:text-white">Notas</h4>
                <button 
                  @click="addContactPersonNote"
                  class="ti-btn ti-btn-primary !py-1 !px-2 rounded text-xs"
                >
                  + Nota
                </button>
              </div>
              
              <div v-if="contactPersonForm.notes.length === 0" class="text-center py-4">
                <p class="text-xs text-defaulttextcolor/60 dark:text-white/60">Nenhuma nota</p>
              </div>

              <div v-else class="space-y-2">
                <div 
                  v-for="(note, index) in contactPersonForm.notes" 
                  :key="index"
                  class="bg-light-100 dark:bg-dark-700 rounded-lg p-2"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0 pr-2">
                      <h5 class="text-xs font-medium text-defaulttextcolor dark:text-white mb-1 truncate">{{ note.name }}</h5>
                      <p class="text-xs text-defaulttextcolor/70 dark:text-white/70 mb-1 leading-relaxed line-clamp-2">{{ note.content }}</p>
                      <p class="text-xs text-defaulttextcolor/50 dark:text-white/50">{{ formatDate(note.note_date) }}</p>
                    </div>
                    <div class="flex flex-col gap-1">
                      <button 
                        @click="editContactPersonNoteInModal(index)"
                        class="w-5 h-5 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 rounded flex items-center justify-center text-blue-600 dark:text-blue-400 transition-colors"
                        title="Editar"
                      >
                        <i class="ri-edit-line text-xs"></i>
                      </button>
                      <button 
                        @click="removeContactPersonNote(index)"
                        class="w-5 h-5 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 rounded flex items-center justify-center text-red-600 dark:text-red-400 transition-colors"
                        title="Remover"
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

        <!-- Footer -->
        <div class="flex gap-2 justify-end p-3 border-t border-defaultborder dark:border-white/10 bg-light-50 dark:bg-dark-800">
          <button
            @click="closeContactPersonModal"
            class="ti-btn ti-btn-soft-secondary !py-2 !px-3 rounded text-sm"
          >
            Cancelar
          </button>
          <button
            @click="saveContactPerson"
            class="ti-btn ti-btn-primary-full !py-2 !px-3 rounded text-sm"
          >
            {{ contactPersonModalMode === 'create' ? 'Salvar' : 'Atualizar' }}
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
        
        <div class="p-6 space-y-4">
          <div>
            <label class="ti-form-label mb-2 text-sm">Título da Nota</label>
            <input 
              v-model="newContactPersonNote.name"
              type="text"
              class="ti-form-input rounded-lg !py-2.5 !px-3"
              placeholder="Título da nota"
            />
          </div>
          
          <div>
            <label class="ti-form-label mb-2 text-sm">Conteúdo</label>
            <textarea 
              v-model="newContactPersonNote.content"
              rows="4"
              class="ti-form-input rounded-lg !py-2.5 !px-3"
              placeholder="Conteúdo da nota..."
            ></textarea>
          </div>
          
          <div>
            <label class="ti-form-label mb-2 text-sm">Data</label>
            <input 
              v-model="newContactPersonNote.note_date"
              type="date"
              class="ti-form-input rounded-lg !py-2.5 !px-3"
            />
          </div>
        </div>

        <div class="flex gap-3 justify-end p-6 border-t border-defaultborder dark:border-white/10">
          <button
            @click="closeContactPersonNoteModal"
            class="ti-btn ti-btn-soft-secondary !py-2.5 !px-6 rounded-lg"
          >
            Cancelar
          </button>
          <button
            @click="saveContactPersonNote"
            class="ti-btn ti-btn-primary-full !py-2.5 !px-6 rounded-lg"
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
import axios from 'axios'
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

// Estado para erros do backend
const backendErrors = ref({})

// Estado para erros do formulário de pessoa de contato
const contactPersonErrors = ref({})

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

// Estado das checkboxes para copiar endereços
const copyToMailing = ref(false)
const copyToBilling = ref(false)

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

const handleSubmit = async () => {
  if (processing.value) return

  // Limpar erros anteriores
  backendErrors.value = {}
  
  // Validações básicas antes de enviar
  const validationErrors = {}
  
  // Validar nome (obrigatório)
  if (!form.value.name || !form.value.name.trim()) {
    validationErrors.name = ['Nome da empresa é obrigatório']
  } else if (form.value.name.length < 2) {
    validationErrors.name = ['Nome da empresa deve ter pelo menos 2 caracteres']
  } else if (form.value.name.length > 255) {
    validationErrors.name = ['Nome da empresa deve ter no máximo 255 caracteres']
  }
  
  // Validar tipo (obrigatório)
  if (!form.value.type || !['customer', 'supplier', 'location'].includes(form.value.type)) {
    validationErrors.type = ['Tipo deve ser Cliente, Fornecedor ou Local']
  }
  
  // Validar e-mail (obrigatório)
  if (!form.value.email || !form.value.email.trim()) {
    validationErrors.email = ['E-mail é obrigatório']
  } else {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(form.value.email)) {
      validationErrors.email = ['E-mail deve ter formato válido']
    } else if (form.value.email.length > 255) {
      validationErrors.email = ['E-mail deve ter no máximo 255 caracteres']
    }
  }
  
  // Validar telefone (obrigatório)
  if (!form.value.phone || !form.value.phone.trim()) {
    validationErrors.phone = ['Telefone é obrigatório']
  } else {
    const phoneRegex = /^(\(\d{2}\)\s?|\d{2}\s?)?\d{4,5}-?\d{4}$/
    if (!phoneRegex.test(form.value.phone.replace(/\s/g, ''))) {
      validationErrors.phone = ['Telefone deve ter formato válido (ex: (11) 99999-9999)']
    }
  }
  
  // Validar linha de negócio (obrigatório)
  if (!form.value.name_line || !form.value.name_line.trim()) {
    validationErrors.name_line = ['Linha de negócio é obrigatória']
  } else if (form.value.name_line.length < 2) {
    validationErrors.name_line = ['Linha de negócio deve ter pelo menos 2 caracteres']
  } else if (form.value.name_line.length > 255) {
    validationErrors.name_line = ['Linha de negócio deve ter no máximo 255 caracteres']
  }
  
  // Validar website (obrigatório)
  if (!form.value.website || !form.value.website.trim()) {
    validationErrors.website = ['Website é obrigatório']
  } else {
    try {
      new URL(form.value.website.startsWith('http') ? form.value.website : 'https://' + form.value.website)
    } catch {
      validationErrors.website = ['Website deve ser uma URL válida']
    }
    if (form.value.website.length > 255) {
      validationErrors.website = ['Website deve ter no máximo 255 caracteres']
    }
  }
  
  // Validar campos obrigatórios do endereço de visita
  // Rua (obrigatório)
  if (!form.value.street_visiting || !form.value.street_visiting.trim()) {
    validationErrors.street_visiting = ['Rua é obrigatória']
  } else if (form.value.street_visiting.length < 3) {
    validationErrors.street_visiting = ['Rua deve ter pelo menos 3 caracteres']
  } else if (form.value.street_visiting.length > 255) {
    validationErrors.street_visiting = ['Rua deve ter no máximo 255 caracteres']
  }

  // Número (obrigatório)
  if (!form.value.house_number_visiting || !form.value.house_number_visiting.trim()) {
    validationErrors.house_number_visiting = ['Número  é obrigatório']
  } else if (form.value.house_number_visiting.length > 10) {
    validationErrors.house_number_visiting = ['Número deve ter no máximo 10 caracteres']
  }

  // CEP de visita (obrigatório)
  if (!form.value.postal_code_visiting || !form.value.postal_code_visiting.trim()) {
    validationErrors.postal_code_visiting = ['CEP é obrigatório']
  } else if (!cepRegex.test(form.value.postal_code_visiting)) {
    validationErrors.postal_code_visiting = ['CEP deve ter formato válido (ex: 12345-678)']
  }

  // Cidade (obrigatório)
  if (!form.value.city_visiting || !form.value.city_visiting.trim()) {
    validationErrors.city_visiting = ['Cidade é obrigatória']
  } else if (form.value.city_visiting.length > 100) {
    validationErrors.city_visiting = ['Cidade deve ter no máximo 100 caracteres']
  } else if (!/^[A-Za-zÀ-ÿ\s\.\-]+$/.test(form.value.city_visiting)) {
    validationErrors.city_visiting = ['Cidade deve conter apenas letras, espaços, pontos e hífens']
  }

  // Estado (obrigatório)
  if (!form.value.state_visiting || !form.value.state_visiting.trim()) {
    validationErrors.state_visiting = ['Estado é obrigatório']
  } else if (!/^[A-Za-z]{2}$/.test(form.value.state_visiting)) {
    validationErrors.state_visiting = ['Estado deve ter 2 letras (ex: SP)']
  }

  // País (obrigatório)
  if (!form.value.country_visiting || !form.value.country_visiting.trim()) {
    validationErrors.country_visiting = ['País é obrigatório']
  } else if (form.value.country_visiting.length > 100) {
    validationErrors.country_visiting = ['País deve ter no máximo 100 caracteres']
  }
  
  // Validar CEP nos endereços de correspondência e cobrança (se fornecido)
  const cepRegex = /^\d{5}-?\d{3}$/
  
  if (form.value.postal_code_mailing && form.value.postal_code_mailing.trim()) {
    if (!cepRegex.test(form.value.postal_code_mailing)) {
      validationErrors.postal_code_mailing = ['CEP deve ter formato válido (ex: 12345-678)']
    }
  }
  
  if (form.value.postal_code_billing && form.value.postal_code_billing.trim()) {
    if (!cepRegex.test(form.value.postal_code_billing)) {
      validationErrors.postal_code_billing = ['CEP deve ter formato válido (ex: 12345-678)']
    }
  }

  // Validar campos obrigatórios do endereço de correspondência (quando não é cópia)
  if (!copyToMailing.value) {
    if (!form.value.street_mailing || !form.value.street_mailing.trim()) {
      validationErrors.street_mailing = ['Rua é obrigatória']
    } else if (form.value.street_mailing.length > 255) {
      validationErrors.street_mailing = ['Rua deve ter no máximo 255 caracteres']
    }

    if (!form.value.house_number_mailing || !form.value.house_number_mailing.trim()) {
      validationErrors.house_number_mailing = ['Número é obrigatório']
    } else if (form.value.house_number_mailing.length > 10) {
      validationErrors.house_number_mailing = ['Número casa deve ter no máximo 10 caracteres']
    }

    if (!form.value.postal_code_mailing || !form.value.postal_code_mailing.trim()) {
      validationErrors.postal_code_mailing = ['CEP é obrigatório']
    }

    if (!form.value.city_mailing || !form.value.city_mailing.trim()) {
      validationErrors.city_mailing = ['Cidade é obrigatória']
    } else if (form.value.city_mailing.length > 100) {
      validationErrors.city_mailing = ['Cidade deve ter no máximo 100 caracteres']
    }

    if (!form.value.state_mailing || !form.value.state_mailing.trim()) {
      validationErrors.state_mailing = ['Estado é obrigatório']
    } else if (!/^[A-Za-z]{2}$/.test(form.value.state_mailing)) {
      validationErrors.state_mailing = ['Estado deve ter 2 letras (ex: SP)']
    }

    if (!form.value.country_mailing || !form.value.country_mailing.trim()) {
      validationErrors.country_mailing = ['País é obrigatório']
    } else if (form.value.country_mailing.length > 100) {
      validationErrors.country_mailing = ['País deve ter no máximo 100 caracteres']
    }
  }

  // Validar campos obrigatórios do endereço de cobrança (quando não é cópia)
  if (!copyToBilling.value) {
    if (!form.value.street_billing || !form.value.street_billing.trim()) {
      validationErrors.street_billing = ['Rua é obrigatória']
    } else if (form.value.street_billing.length > 255) {
      validationErrors.street_billing = ['Rua deve ter no máximo 255 caracteres']
    }

    if (!form.value.house_number_billing || !form.value.house_number_billing.trim()) {
      validationErrors.house_number_billing = ['Número é obrigatório']
    } else if (form.value.house_number_billing.length > 10) {
      validationErrors.house_number_billing = ['Número deve ter no máximo 10 caracteres']
    }

    if (!form.value.postal_code_billing || !form.value.postal_code_billing.trim()) {
      validationErrors.postal_code_billing = ['CEP é obrigatório']
    }

    if (!form.value.city_billing || !form.value.city_billing.trim()) {
      validationErrors.city_billing = ['Cidade é obrigatória']
    } else if (form.value.city_billing.length > 100) {
      validationErrors.city_billing = ['Cidade deve ter no máximo 100 caracteres']
    }

    if (!form.value.state_billing || !form.value.state_billing.trim()) {
      validationErrors.state_billing = ['Estado é obrigatório']
    } else if (!/^[A-Za-z]{2}$/.test(form.value.state_billing)) {
      validationErrors.state_billing = ['Estado deve ter 2 letras (ex: SP)']
    }

    if (!form.value.country_billing || !form.value.country_billing.trim()) {
      validationErrors.country_billing = ['País é obrigatório']
    } else if (form.value.country_billing.length > 100) {
      validationErrors.country_billing = ['País deve ter no máximo 100 caracteres']
    }
  }
  
  // Validar estados adicionais (quando já fornecidos)
  const stateRegex = /^[A-Za-z]{2}$/
  
  if (form.value.state_mailing && form.value.state_mailing.trim() && copyToMailing.value) {
    if (!stateRegex.test(form.value.state_mailing)) {
      validationErrors.state_mailing = ['Estado deve ter 2 letras (ex: SP)']
    }
  }
  
  if (form.value.state_billing && form.value.state_billing.trim() && copyToBilling.value) {
    if (!stateRegex.test(form.value.state_billing)) {
      validationErrors.state_billing = ['Estado deve ter 2 letras (ex: SP)']
    }
  }

  // Se há erros de validação, mostrar e parar
  if (Object.keys(validationErrors).length > 0) {
    backendErrors.value = validationErrors
    activeMainTab.value = 'data'
    // Scroll para o topo do formulário
    window.scrollTo({ top: 0, behavior: 'smooth' })
    return
  }

  processing.value = true

  // Limpar e formatar dados das pessoas de contato
  const formattedContactPersons = contactPersons.value.map(person => {
    return {
      first_name: person.first_name,
      last_name: person.last_name || '',
      mobile: person.mobile || '',
      role: person.role || '',
      emails: person.emails.filter(email => email && email.trim() !== ''),
      notes: person.notes.map(note => ({
        name: note.name,
        content: note.content || '',
        note_date: note.note_date
      }))
    }
  })

  const submitData = {
    ...form.value,
    temp_key: props.tempKey,
    contact_persons: formattedContactPersons
  }

  console.log('Dados a serem enviados:', submitData)

  const url = props.mode === 'create' 
    ? '/api/contacts' 
    : `/api/contacts/${props.contact.id}`
  
  const method = props.mode === 'create' ? 'post' : 'put'

  console.log('URL:', url, 'Método:', method)

  try {
    const response = await axios[method](url, submitData)
    console.log('Sucesso:', response.data)
    
    processing.value = false
    hasUnsavedChanges.value = false
    
    // Redirecionar usando router.visit do Inertia
    router.visit('/contacts', {
      preserveState: false,
      replace: true
    })
  } catch (error) {
    processing.value = false
    console.error('Erro ao salvar contato:', error)
    
    if (error.response?.data?.errors) {
      // Mostrar erros de validação do backend
      const errors = error.response.data.errors
      backendErrors.value = errors
      activeMainTab.value = 'data'
      // Scroll para o topo do formulário
      window.scrollTo({ top: 0, behavior: 'smooth' })
    } else if (error.response?.data?.message) {
      // Mostrar mensagem de erro do servidor
      alert(`Erro: ${error.response.data.message}`)
    } else {
      // Erro genérico
      alert(`Erro ao salvar contato: ${error.message}`)
    }
  }
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
  // Limpar erros anteriores
  contactPersonErrors.value = {}
  
  // Validações dos campos obrigatórios
  const validationErrors = {}
  
  // Validar nome (obrigatório)
  if (!contactPersonForm.value.first_name || !contactPersonForm.value.first_name.trim()) {
    validationErrors.first_name = ['Nome é obrigatório']
  } else if (contactPersonForm.value.first_name.length < 2) {
    validationErrors.first_name = ['Nome deve ter pelo menos 2 caracteres']
  } else if (contactPersonForm.value.first_name.length > 100) {
    validationErrors.first_name = ['Nome deve ter no máximo 100 caracteres']
  } else if (!/^[A-Za-zÀ-ÿ\s\.\-]+$/.test(contactPersonForm.value.first_name)) {
    validationErrors.first_name = ['Nome deve conter apenas letras, espaços, pontos e hífens']
  }
  
  // Validar sobrenome (obrigatório)
  if (!contactPersonForm.value.last_name || !contactPersonForm.value.last_name.trim()) {
    validationErrors.last_name = ['Sobrenome é obrigatório']
  } else if (contactPersonForm.value.last_name.length < 2) {
    validationErrors.last_name = ['Sobrenome deve ter pelo menos 2 caracteres']
  } else if (contactPersonForm.value.last_name.length > 100) {
    validationErrors.last_name = ['Sobrenome deve ter no máximo 100 caracteres']
  } else if (!/^[A-Za-zÀ-ÿ\s\.\-]+$/.test(contactPersonForm.value.last_name)) {
    validationErrors.last_name = ['Sobrenome deve conter apenas letras, espaços, pontos e hífens']
  }
  
  // Validar função (obrigatório)
  if (!contactPersonForm.value.role || !contactPersonForm.value.role.trim()) {
    validationErrors.role = ['Função é obrigatória']
  } else if (contactPersonForm.value.role.length < 2) {
    validationErrors.role = ['Função deve ter pelo menos 2 caracteres']
  } else if (contactPersonForm.value.role.length > 100) {
    validationErrors.role = ['Função deve ter no máximo 100 caracteres']
  }
  
  // Validar telefone (obrigatório)
  if (!contactPersonForm.value.mobile || !contactPersonForm.value.mobile.trim()) {
    validationErrors.mobile = ['Telefone é obrigatório']
  } else {
    const phoneRegex = /^(\(\d{2}\)\s?|\d{2}\s?)?\d{4,5}-?\d{4}$/
    if (!phoneRegex.test(contactPersonForm.value.mobile.replace(/\s/g, ''))) {
      validationErrors.mobile = ['Telefone deve ter formato válido (ex: (11) 99999-9999)']
    }
  }
  
  // Validar e-mails (se fornecidos)
  const validEmails = contactPersonForm.value.emails.filter(email => email.trim() !== '')
  if (validEmails.length > 0) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    for (let i = 0; i < validEmails.length; i++) {
      if (!emailRegex.test(validEmails[i])) {
        validationErrors.emails = [`E-mail ${i + 1} deve ter formato válido`]
        break
      }
    }
  }
  
  // Se há erros de validação, mostrar e parar
  if (Object.keys(validationErrors).length > 0) {
    contactPersonErrors.value = validationErrors
    return
  }

  const personData = {
    id: contactPersonModalMode.value === 'edit' && contactPersonFormIndex.value >= 0 
      ? contactPersons.value[contactPersonFormIndex.value].id 
      : Date.now(),
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
  } else if (contactPersonModalMode.value === 'edit' && contactPersonFormIndex.value >= 0) {
    // Editar pessoa existente - verificar se o índice é válido
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

  // Resetar variáveis de controle
  contactPersonModalMode.value = 'create'
  contactPersonFormIndex.value = -1
  contactPersonErrors.value = {}
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
  contactPersonErrors.value = {} // Limpar erros ao editar
  
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
  contactPersonErrors.value = {}
  
  contactPersonForm.value = {
    first_name: '',
    last_name: '',
    mobile: '',
    role: '',
    emails: [''],
    notes: []
  }
}

// Funções para copiar endereços
const copyVisitingToMailing = (checked) => {
  if (checked) {
    form.value.street_mailing = form.value.street_visiting
    form.value.house_number_mailing = form.value.house_number_visiting
    form.value.postal_code_mailing = form.value.postal_code_visiting
    form.value.city_mailing = form.value.city_visiting
    form.value.state_mailing = form.value.state_visiting
    form.value.country_mailing = form.value.country_visiting
    form.value.lat_mailing = form.value.lat_visiting
    form.value.lng_mailing = form.value.lng_visiting
  }
}

const copyVisitingToBilling = (checked) => {
  if (checked) {
    form.value.street_billing = form.value.street_visiting
    form.value.house_number_billing = form.value.house_number_visiting
    form.value.postal_code_billing = form.value.postal_code_visiting
    form.value.city_billing = form.value.city_visiting
    form.value.state_billing = form.value.state_visiting
    form.value.country_billing = form.value.country_visiting
    form.value.lat_billing = form.value.lat_visiting
    form.value.lng_billing = form.value.lng_visiting
  }
}

// Watchers para as checkboxes
watch(copyToMailing, (newVal) => {
  copyVisitingToMailing(newVal)
  if (newVal) {
    hasUnsavedChanges.value = true
  }
})

watch(copyToBilling, (newVal) => {
  copyVisitingToBilling(newVal)
  if (newVal) {
    hasUnsavedChanges.value = true
  }
})

// Função para formatar data
const formatDate = (dateString) => {
  if (!dateString) return ''
  
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR')
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Automaticamente adiciona asterisco vermelho (*) para campos obrigatórios */
.required-field::after {
  content: " *";
  color: #ef4444;
  font-weight: 500;
}

/* Quando o input/select seguinte tem required, o label anterior recebe o asterisco */
.ti-form-label:has(+ input[required])::after,
.ti-form-label:has(+ select[required])::after,
.ti-form-label:has(+ textarea[required])::after {
  content: " *";
  color: #ef4444;
  font-weight: 500;
}

/* Para estruturas com classes específicas */
.ti-form-label:has(+ .ti-form-input[required])::after,
.ti-form-label:has(+ .ti-form-select[required])::after {
  content: " *";
  color: #ef4444;
  font-weight: 500;
}

/* Fallback para navegadores que não suportam :has() */
@supports not selector(:has(*)) {
  .ti-form-label[data-required="true"]::after {
    content: " *";
    color: #ef4444;
    font-weight: 500;
  }
}
</style>