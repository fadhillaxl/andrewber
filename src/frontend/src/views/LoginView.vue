
<template>

  <ThePreLoader />
    <!-- Sign in screen content start -->
    <div class="let-you-page-main">
      <div class="let-you-top" >
        <div class="let-you-top-wrap">
          <header class="back-btn">
            <a href="javascript:history.go(-1)">
              <!-- <img src="assets/svg/back-btn-arrow.svg" alt="back-btn"> -->
            </a>
          </header>
          <div class="taxi-img_main">
            <!-- <img class="taxi-img_img" src="assets/images/let-you-screen/logo.svg" alt="logo"> -->
          </div>
        </div>
      </div>
      <div class="let-you-social-sec">
        <div class="lets_you_in_box">
          <h1 class="d-none">hidden</h1>
          <h2 class="lets_you_in_text">Sign In</h2>
          <!-- <form>
            <div class="mobile-form mt-32">
              <input type="number" id="mobile_code" class="sign-in-custom-input" placeholder="Enter Mobile Number">
            </div>
          </form>
          <div class="sign-in-password-btn mt-32">
            <a href="verify-number.html">Sign In</a>
          </div> -->
          <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="mobile-form mt-32">
                    <div>
                        <input type="text" v-maska data-maska="628##########" v-model="credentials.phone" name="phone" id="phone" placeholder="6287878191876"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="sign-in-password-btn mt-32">
                    
                        <a> 
                            <button type="submit" @submit.prevent="handleLogin"
                        >Continue</button>
                        </a>
                </div>
            </div>
          </form>
          <form v-else action="#" @submit.prevent="handleVerification">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="mobile-form mt-32">
                    <div>
                        <input type="text" v-maska data-maska="######" v-model="credentials.login_code" name="login_code" id="login_code" placeholder="123456"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="sign-in-password-btn mt-32">
                    <a>
                        <button type="submit" @submit.prevent="handleVerification"
                        >Verify</button>
                    </a>
                </div>
            </div>
            </form>
          <div class="or-section mt-32">
            <p>or continue with</p>
          </div>
          <div class="icons_main mt-32">
            <a href="https://www.facebook.com" target="_blank">
              <img class="icons" src="assets/images/let-you-screen/fb-icon.svg" alt="Icon-fb">
            </a>
            <a href="https://www.google.com" target="_blank">
              <img class="icons" src="assets/images/let-you-screen/google-icon.svg" alt="Icon-google">
            </a>
            <a href="https://www.icloud.com" target="_blank">
              <img class="apple" src="assets/images/let-you-screen/apple-icon.svg" alt="Icon-apple">
            </a>
            <a href="https://wa.me/+12345678899" target="_blank">
              <img class="icons" src="assets/images/let-you-screen/whatsapp.svg" alt="Icon-whatsapp">
            </a>
          </div>
        </div>
      </div>
      <footer id="let-you-footer">
        <div class="block-footer">
          <p>Don’t have an account? <a href="sign-up.html">Sign up</a></p>
        </div>
      </footer>
    </div>
    <!-- Sign in screen content end -->

</template>
<script setup>
import { vMaska } from 'maska'
import { reactive, ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';
import ThePreLoader from '../components/PreLoader.vue'


const router = useRouter()

const credentials = reactive({
    phone: null,
    login_code: null
})

const waitingOnVerification = ref(false)

onMounted(() => {
    if (localStorage.getItem('token')) {
        router.push({
            name: 'landing'
        })
    }
})

const getFormattedCredentials = () => {
    return {
        phone: credentials.phone.replaceAll(' ', '').replace('(', '').replace(')', '').replace('-', ''),
        login_code: credentials.login_code
    }
}

const handleLogin = () => {
    axios.post('https://sijek-backend.me/api/login', getFormattedCredentials())
        .then((response) => {
            console.log(response.data)
            waitingOnVerification.value = true
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
}

const handleVerification = () => {
    axios.post('https://sijek-backend.me/api/login/verify', getFormattedCredentials())
        .then((response) => {
            console.log(response.data) // auth token
            localStorage.setItem('token', response.data)
            router.push({
                name: 'landing'
            })
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
}
</script>