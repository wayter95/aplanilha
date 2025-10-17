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
                  <div class="xl:col-span-12 col-span-12 mb-4">
                    <label for="reset-email" class="form-label text-defaulttextcolor">Email</label>
                    <input 
                      type="email" 
                      class="form-control form-control-lg w-full !rounded-md bg-defaultbackground border-inputborder text-defaulttextcolor" 
                      id="reset-email" 
                      :value="email"
                      readonly
                    >
                  </div>
                  <div class="xl:col-span-12 col-span-12 mb-4">
                    <label for="reset-password" class="form-label text-defaulttextcolor">Nova Senha</label>
                    <div class="input-group">
                      <input 
                        :type="passwordType" 
                        class="form-control form-control-lg !border-s border-inputborder !rounded-e-none bg-defaultbackground text-defaulttextcolor" 
                        id="reset-password" 
                        placeholder="Nova senha"
                        v-model="form.password"
                        required
                      >
                      <button 
                        aria-label="button" 
                        type="button" 
                        class="ti-btn ti-btn-light !rounded-s-none !mb-0" 
                        @click="togglePassword"
                      >
                        <i :class="passwordIcon" class="align-middle"></i>
                      </button>
                    </div>
                  </div>
                  <div class="xl:col-span-12 col-span-12 mb-4">
                    <label for="reset-password-confirmation" class="form-label text-defaulttextcolor">Confirmar Nova Senha</label>
                    <div class="input-group">
                      <input 
                        :type="confirmPasswordType" 
                        class="form-control form-control-lg !border-s border-inputborder !rounded-e-none bg-defaultbackground text-defaulttextcolor" 
                        id="reset-password-confirmation" 
                        placeholder="Confirmar nova senha"
                        v-model="form.password_confirmation"
                        required
                      >
                      <button 
                        aria-label="button" 
                        type="button" 
                        class="ti-btn ti-btn-light !rounded-s-none !mb-0" 
                        @click="toggleConfirmPassword"
                      >
                        <i :class="confirmPasswordIcon" class="align-middle"></i>
                      </button>
                    </div>
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
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import backgroundImage from '../../../assets/images/authentication/1.jpg';
import authImage1 from '../../../assets/images/authentication/2.png';

const page = usePage();

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

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordType = computed(() => showPassword.value ? 'text' : 'password');
const passwordIcon = computed(() => showPassword.value ? 'ri-eye-line' : 'ri-eye-off-line');

const confirmPasswordType = computed(() => showConfirmPassword.value ? 'text' : 'password');
const confirmPasswordIcon = computed(() => showConfirmPassword.value ? 'ri-eye-line' : 'ri-eye-off-line');

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value;
};

const handleSubmit = async () => {
  isLoading.value = true;

  try {
    router.post('/reset-password', form.value, {
      onSuccess: () => {
        router.visit('/sign-in');
      },
      onError: (errors) => {
        console.error('Reset failed:', errors);
      },
      onFinish: () => {
        isLoading.value = false;
      }
    });
  } catch (error) {
    console.error('Reset error:', error);
    isLoading.value = false;
  }
};
</script>

<style scoped></style>