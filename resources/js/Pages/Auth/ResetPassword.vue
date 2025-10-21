<template>
  <div class="min-h-screen bg-bodybg text-defaulttextcolor">
    <div class="grid grid-cols-12 authentication mx-0 min-h-screen">
      <div class="xxl:col-span-7 xl:col-span-7 lg:col-span-12 col-span-12 bg-bodybg">
        <div class="grid grid-cols-12 items-center h-full min-h-screen">
          <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>
          <div class="xxl:col-span-6 xl:col-span-6 lg:col-span-6 md:col-span-6 sm:col-span-8 col-span-12">
            <div class="p-[3rem]">
              <div class="mb-4">
                <a aria-label="anchor" href="/">
                  <h2 class="text-2xl font-bold">Aplanilha</h2>
                </a>
              </div>
              <p class="h5 font-semibold mb-2 text-defaulttextcolor">Redefinir senha</p>
              <p class="mb-4 text-textmuted font-normal">Defina uma nova senha para sua conta</p>
              
              <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-12">
                  <div class="xl:col-span-12 col-span-12">
                    <Input
                      id="reset-email"
                      :model-value="form.email"
                      type="email"
                      label="Email"
                      :disabled="true"
                      help="Este é o e-mail para o qual foi enviado o link de recuperação"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12">
                    <InputPassword
                      id="reset-password"
                      v-model="form.password"
                      label="Nova Senha"
                      placeholder="Digite sua nova senha"
                      :required="true"
                      :error="errors.password"
                      help="Use pelo menos 8 caracteres"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12">
                    <InputPassword
                      id="reset-password-confirmation"
                      v-model="form.password_confirmation"
                      label="Confirmar Nova Senha"
                      placeholder="Confirme sua nova senha"
                      :required="true"
                      :error="errors.password_confirmation"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12 grid">
                    <button 
                      type="submit" 
                      class="ti-btn ti-btn-lg bg-primary text-white btn-wave !font-medium w-full"
                      :disabled="isLoading"
                    >
                      <span v-if="isLoading">Redefinindo...</span>
                      <span v-else>Redefinir Senha</span>
                    </button>
                  </div>
                  <div class="xl:col-span-12 col-span-12 text-center mt-4">
                    <a href="/sign-in" class="text-primary">Voltar ao Login</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>
        </div>
      </div>
      
      <div class="xxl:col-span-5 xl:col-span-5 lg:col-span-5 col-span-12 xl:block hidden px-0 bg-cover bg-center bg-no-repeat" :style="{ backgroundImage: `url(${backgroundImage})` }">
        <div class="authentication-cover bg-gradient-to-br from-primary/20 to-secondary/20">
          <div class="aunthentication-cover-content rounded bg-black/50">
            <div class="text-white text-center p-[3rem] flex items-center justify-center h-full">
              <div>
                <div class="mb-[3rem]">
                  <img :src="authImage1" class="authentication-image" alt="">
                </div>
                <h6 class="font-semibold text-[1rem]">Nova Senha</h6>
                <p class="font-normal text-[0.875rem] opacity-[0.7]">
                  Defina uma nova senha segura para proteger sua conta. Use pelo menos 8 caracteres.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Toast Container -->
    <ToastContainer position="top-right" />
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import Input from '@/Components/Input.vue';
import InputPassword from '@/Components/InputPassword.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';

import backgroundImage from '../../../assets/images/authentication/1.jpg';
import authImage1 from '../../../assets/images/authentication/2.png';

const page = usePage();
const { success, error } = useToast();

const props = defineProps({
  token: String,
  email: String,
});

const form = ref({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const isLoading = ref(false);
const errors = ref({});

const handleSubmit = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    router.post('/reset-password', form.value, {
      onSuccess: () => {
        success('Senha redefinida com sucesso! Você pode fazer login com sua nova senha.');
        setTimeout(() => {
          router.visit('/sign-in');
        }, 2000);
      },
      onError: (responseErrors) => {
        errors.value = responseErrors;
        if (responseErrors.token) {
          error('O link de redefinição de senha é inválido ou expirou. Solicite uma nova recuperação.');
        } else if (responseErrors.password) {
          error(responseErrors.password[0] || 'Erro na validação da senha.');
        } else {
          error('Não foi possível redefinir sua senha. Tente novamente.');
        }
      },
      onFinish: () => {
        isLoading.value = false;
      }
    });
  } catch (err) {
    error('Erro inesperado ao redefinir senha. Tente novamente.');
    isLoading.value = false;
  }
};
</script>

<style scoped></style>