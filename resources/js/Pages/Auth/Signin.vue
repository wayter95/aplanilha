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
              <p class="h5 font-semibold mb-2 text-defaulttextcolor">Entrar</p>
              <p class="mb-4 text-textmuted font-normal">Bem-vindo de volta!</p>
              
              <form @submit.prevent="handleSubmit">
                <div class="grid grid-cols-12">
                  <div class="xl:col-span-12 col-span-12 mb-4">
                    <label for="signin-email" class="form-label text-defaulttextcolor">E-mail</label>
                    <input 
                      type="email" 
                      class="form-control form-control-lg w-full !rounded-md bg-defaultbackground border-inputborder text-defaulttextcolor" 
                      id="signin-email" 
                      placeholder="email@exemplo.com"
                      v-model="form.email"
                      required
                    >
                  </div>
                  <div class="xl:col-span-12 col-span-12 mb-4">
                    <label for="signin-password" class="form-label text-defaulttextcolor block">
                      Senha
                      <a href="/forgot-password" class="ltr:float-right rtl:float-left text-danger">Esqueceu a senha?</a>
                    </label>
                    <div class="input-group">
                      <input 
                        :type="passwordType" 
                        class="form-control form-control-lg !border-s border-inputborder !rounded-e-none bg-defaultbackground text-defaulttextcolor" 
                        id="signin-password" 
                        placeholder="senha"
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
                    <div class="mt-2">
                      <div class="form-check !ps-0">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          v-model="form.remember"
                          id="defaultCheck1"
                        >
                        <label class="form-check-label text-textmuted font-normal" for="defaultCheck1">
                          Lembrar senha?
                        </label>
                      </div>
                    </div>
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
              </form>
            </div>
          </div>
          <div class="xxl:col-span-3 xl:col-span-3 lg:col-span-3 md:col-span-3 sm:col-span-2"></div>
        </div>
      </div>
      
      <div class="xxl:col-span-5 xl:col-span-5 lg:col-span-5 col-span-12 xl:block hidden px-0 bg-cover bg-center bg-no-repeat" :style="{ backgroundImage: `url(${backgroundImage})` }">
        <div class="authentication-cover bg-gradient-to-br from-primary/20 to-secondary/20">
          <div class="aunthentication-cover-content rounded bg-black/50">
            <div class="swiper keyboard-control" ref="swiperContainer">
              <div class="swiper-wrapper">
                <div class="swiper-slide">
                  <div class="text-white text-center p-[3rem] flex items-center justify-center">
                    <div>
                      <div class="mb-[3rem]">
                        <img :src="authImage1" class="authentication-image" alt="">
                      </div>
                      <h6 class="font-semibold text-[1rem]">Entrar</h6>
                      <p class="font-normal text-[0.875rem] opacity-[0.7]">
                        Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="swiper-slide">
                  <div class="text-white text-center p-[3rem] flex items-center justify-center">
                    <div>
                      <div class="mb-[3rem]">
                        <img :src="authImage2" class="authentication-image" alt="">
                      </div>
                      <h6 class="font-semibold text-[1rem]">Entrar</h6>
                      <p class="font-normal text-[0.875rem] opacity-[0.7]">
                        Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="swiper-slide">
                  <div class="text-white text-center p-[3rem] flex items-center justify-center">
                    <div>
                      <div class="mb-[3rem]">
                        <img :src="authImage1" class="authentication-image" alt="">
                      </div>
                      <h6 class="font-semibold text-[1rem]">Entrar</h6>
                      <p class="font-normal text-[0.875rem] opacity-[0.7]">
                        Bem-vindo ao Aplanilha! Acesse sua conta para gerenciar suas planilhas e dados de forma eficiente e segura.
                      </p>
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
  </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { Autoplay, Keyboard, Navigation, Pagination, Swiper } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import { computed, nextTick, onMounted, ref } from 'vue';

import backgroundImage from '../../../assets/images/authentication/1.jpg';
import authImage1 from '../../../assets/images/authentication/2.png';
import authImage2 from '../../../assets/images/authentication/3.png';

const page = usePage();

const form = ref({
    email: '',
    password: '',
    remember: false
});

const isLoading = ref(false);

const showPassword = ref(false);

const swiperContainer = ref(null);

const passwordType = computed(() => showPassword.value ? 'text' : 'password');
const passwordIcon = computed(() => showPassword.value ? 'ri-eye-line' : 'ri-eye-off-line');

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const handleSubmit = async () => {
    isLoading.value = true;

    try {
        router.post('/login', form.value, {
            onSuccess: (page) => {
                console.log('Login successful:', page);
                router.visit('/');
            },
            onError: (errors) => {
                console.error('Login failed:', errors);
                alert('Erro no login: ' + JSON.stringify(errors));
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (error) {
        console.error('Login error:', error);
        alert('Erro no login: ' + error.message);
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
            keyboard: {
                enabled: true,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            loop: true,
            autoplay: {
                delay: 1500,
                disableOnInteraction: false
            }
        });
    }
});
</script>

<style scoped></style>