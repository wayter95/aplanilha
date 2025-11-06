<template>
  <div class="min-h-screen bg-bodybg text-defaulttextcolor">
    <div class="grid grid-cols-12 authentication mx-0 min-h-screen">

      <!-- 🟢 COLUNA DO FORMULÁRIO -->
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
              <p class="h5 font-semibold mb-2 text-defaulttextcolor">Entrar</p>
              <p class="mb-4 text-textmuted font-normal">Bem-vindo de volta!</p>

              <!-- ✅ FORMULÁRIO COM VEE-VALIDATE -->
              <BaseForm @submit="handleSubmit">
                <div class="grid grid-cols-12">
                  <div class="xl:col-span-12 col-span-12">
                    <Input
                      id="signin-email"
                      name="email"
                      type="email"
                      label="E-mail"
                      placeholder="email@exemplo.com"
                      required
                      :rules="emailRules"
                      v-model="form.email"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12">
                    <InputPassword
                      id="signin-password"
                      name="password"
                      label="Senha"
                      placeholder="senha"
                      required
                      :rules="passwordRules"
                      show-remember
                      forgot-password-link="/forgot-password"
                      v-model="form.password"
                      v-model:remember="form.remember"
                    />
                  </div>
                  <div class="xl:col-span-12 col-span-12 grid">
                    <button 
                      type="submit" 
                      class="ti-btn ti-btn-lg bg-primary text-white btn-wave !font-medium w-full"
                      :disabled="isLoading"
                    >
                      <span v-if="isLoading">Entrando...</span>
                      <span v-else>Entrar</span>
                    </button>
                  </div>
                </div>
              </BaseForm>
            </div>
          </div>
          <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>
        </div>
      </div>

      <!-- 🟡 COLUNA DAS IMAGENS (SWIPER) -->
      <div 
        class="xxl:col-span-5 xl:col-span-5 lg:col-span-5 col-span-12 xl:block hidden px-0 bg-cover bg-center bg-no-repeat" 
        :style="{ backgroundImage: `url(${backgroundImage})` }"
      >
        <div class="authentication-cover bg-gradient-to-br from-primary/20 to-secondary/20">
          <div class="aunthentication-cover-content rounded bg-black/50">
            <div class="swiper keyboard-control" ref="swiperContainer">
              <div class="swiper-wrapper">
                <div class="swiper-slide" v-for="(img, index) in slides" :key="index">
                  <div class="text-white text-center p-[3rem] flex items-center justify-center">
                    <div>
                      <div class="mb-[3rem]">
                        <img :src="img.src" class="authentication-image" alt="">
                      </div>
                      <h6 class="font-semibold text-[1rem]">{{ img.title }}</h6>
                      <p class="font-normal text-[0.875rem] opacity-[0.7]">{{ img.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="swiper-button-next"></div>
              <div class="swiper-button-prev"></div>
              <div class="swiper-pagination"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TOASTS -->
    <ToastContainer position="top-right" />
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { Autoplay, Keyboard, Navigation, Pagination, Swiper } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { nextTick, onMounted, ref } from 'vue';

import BaseForm from '@/Components/Form/BaseForm.vue';
import Input from '@/Components/Input.vue';
import InputPassword from '@/Components/InputPassword.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';
import { useAuthValidation } from '@/composables/useAuthValidation';

import backgroundImage from '../../../assets/images/authentication/1.jpg';
import authImage1 from '../../../assets/images/authentication/2.png';
import authImage2 from '../../../assets/images/authentication/3.png';

const page = usePage();
const { success, error } = useToast();
const { loginSchema } = useAuthValidation(); // opcional, se for usar schema

const form = ref({
    email: '',
    password: '',
    remember: false
});

const emailRules = 'required|email';
const passwordRules = 'required|min:6';
const isLoading = ref(false);
const swiperContainer = ref(null);

const slides = [
  {
    src: authImage1,
    title: 'Entrar',
    description: 'Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.'
  },
  {
    src: authImage2,
    title: 'Entrar',
    description: 'Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.'
  },
  {
    src: authImage1,
    title: 'Entrar',
    description: 'Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.'
  }
];

const handleSubmit = async (values, { setErrors }) => {
    isLoading.value = true;
    try {
        const formData = {
            email: values.email,
            password: values.password,
            remember: form.value.remember
        };

        router.post('/login', formData, {
            onSuccess: () => {
                success('Login realizado com sucesso!');
                router.visit('/');
            },
            onError: (errors) => {
                if (errors.email) setErrors({ email: errors.email });
                if (errors.password) setErrors({ password: errors.password });
                if (!errors.email && !errors.password) {
                    error('Credenciais inválidas.');
                }
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (err) {
        console.error('Login error:', err);
        error('Erro no login: ' + err.message);
        isLoading.value = false;
    }
};

onMounted(async () => {
    await nextTick();
    if (swiperContainer.value) {
        new Swiper(swiperContainer.value, {
            modules: [Navigation, Pagination, Autoplay, Keyboard],
            slidesPerView: 1,
            spaceBetween: 30,
            keyboard: { enabled: true },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            loop: true,
            autoplay: { delay: 1500, disableOnInteraction: false }
        });
    }
});
</script>

<style scoped></style>
