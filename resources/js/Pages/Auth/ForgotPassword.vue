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
              <p class="h5 font-semibold mb-2 text-defaulttextcolor">Esqueci minha senha</p>
              <p class="mb-4 text-textmuted font-normal">Digite seu email para receber o link de recuperação</p>
              
              <BaseForm @submit="handleSubmit">
                <div class="grid grid-cols-12">
                  <div class="xl:col-span-12 col-span-12">
                    <Input
                      id="forgot-email"
                      name="email"
                      type="email"
                      label="Email"
                      placeholder="email@example.com"
                      required
                      :rules="emailRules"
                      v-model="form.email"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12 grid">
                    <button 
                      type="submit" 
                      class="ti-btn ti-btn-lg bg-primary text-white btn-wave !font-medium w-full"
                      :disabled="isLoading"
                    >
                      <span v-if="isLoading">Enviando...</span>
                      <span v-else>Enviar Link de Recuperação</span>
                    </button>
                  </div>
                  <div class="xl:col-span-12 col-span-12 text-center mt-4">
                    <a href="/sign-in" class="text-primary">Voltar ao Login</a>
                  </div>
                </div>
              </BaseForm>
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
                <h6 class="font-semibold text-[1rem]">Recuperação de Senha</h6>
                <p class="font-normal text-[0.875rem] opacity-[0.7]">
                  Digite seu email para receber instruções de como redefinir sua senha de forma segura.
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
import BaseForm from '@/Components/Form/BaseForm.vue';
import Input from '@/Components/Input.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';

import backgroundImage from '../../../assets/images/authentication/1.jpg';
import authImage1 from '../../../assets/images/authentication/2.png';

const page = usePage();
const { success, error } = useToast();

const form = ref({
  email: '',
});

const isLoading = ref(false);

const emailRules = 'required|email';

const handleSubmit = async (values, { setErrors }) => {
  isLoading.value = true;

  try {
    router.post('/forgot-password', { email: values.email }, {
      onSuccess: () => {
        success('Se o e-mail informado estiver cadastrado, você receberá instruções para redefinir sua senha.');
        form.value.email = '';
      },
      onError: (responseErrors) => {
        if (responseErrors.email) {
          setErrors({ email: responseErrors.email });
        }
        error(responseErrors.email || 'Erro ao enviar link de recuperação. Tente novamente.');
      },
      onFinish: () => {
        isLoading.value = false;
      }
    });
  } catch (err) {
    error('Erro inesperado ao enviar link de recuperação. Tente novamente.');
    isLoading.value = false;
  }
};
</script>

<style scoped></style>