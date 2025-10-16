<template>
  <div class="container">
    <div class="flex justify-center authentication authentication-basic items-center h-full text-defaultsize text-defaulttextcolor">
        <div class="grid grid-cols-12">
            <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-3 sm:col-span-2"></div>
            <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-6 sm:col-span-8 col-span-12">
                <div class="my-[2.5rem] flex justify-center">
                    <a href="/index">
                        <img src="../assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
                        <img src="../assets/images/brand-logos/desktop-dark.png" alt="logo" class="desktop-dark">
                    </a>
                </div>
                <div class="box">
                    <div class="box-body !p-[3rem]">
                        <p class="h5 font-semibold mb-2 text-center">Sign In</p>
                        <div class="grid grid-cols-12">
                            <div class="xl:col-span-12 col-span-12 mb-3">
                                <label for="signin-username" class="form-label text-default">User Name</label>
                                <input type="text" class="form-control form-control-lg w-full !rounded-md" id="signin-username" placeholder="user name">
                            </div>
                            <div class="xl:col-span-12 col-span-12 mb-3">
                                <label for="signin-password" class="form-label text-default block">Password<a href="/resetpassword-basic" class="ltr:float-right rtl:float-left text-danger">Forget password ?</a></label>
                                <div class="input-group">
                                    <input type="password" class="form-control !border-s border-defaultborder dark:border-defaultborder/10 form-control-lg !rounded-s-md" id="signin-password" placeholder="password">
                                    <button aria-label="button" class="ti-btn ti-btn-light !rounded-s-none !mb-0" type="button" onclick="togglePassword()" id="button-addon2"><i class="ri-eye-off-line align-middle"></i></button>
                                </div>
                                <div class="mt-2">
                                    <div class="form-check !ps-0">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label text-[#8c9097] dark:text-white/50 font-normal" for="defaultCheck1">
                                            Remember password ?
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="xl:col-span-12 col-span-12 grid mt-2">
                                <a href="/index" class="ti-btn ti-btn-primary !bg-primary  btn-wave !text-white !font-medium">Sign In</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-[0.75rem] text-[#8c9097] dark:text-white/50 mt-4">Dont have an account? <a href="/signup-basic" class="text-primary">Sign Up</a></p>
                        </div>
                        <div class="text-center my-4 authentication-barrier">
                            <span>OR</span>
                        </div>
                        <div class="btn-list text-center">
                            <button aria-label="button" type="button" class="ti-btn ti-btn-icon ti-btn-light me-[0.365rem]">
                                <i class="ri-facebook-line font-bold text-dark opacity-[0.7]"></i>
                            </button>
                            <button aria-label="button" type="button" class="ti-btn ti-btn-icon ti-btn-light me-[0.365rem]">
                                <i class="ri-google-line font-bold text-dark opacity-[0.7]"></i>
                            </button>
                            <button aria-label="button" type="button" class="ti-btn ti-btn-icon ti-btn-light">
                                <i class="ri-twitter-x-line font-bold text-dark opacity-[0.7]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xxl:col-span-4 xl:col-span-4 lg:col-span-4 md:col-span-3 sm:col-span-2"></div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';

const password = ref('password');

const togglePassword = () => {
    password.value = password.value === 'password' ? 'text' : 'password';
}
</script>

<style scoped></style>