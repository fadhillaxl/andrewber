<template>
    <ThePreLoader v-if="isLoading" />
    <TheSideBar/>
    <div class="let-you-page-main">
        <div class="header-sec">
            <div class="header-sec-wrap">
                <div class="header-setting">

                <a data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"><img src="assets/images/map/setting-icon.svg" alt="setting-icon"></a>
            </div>
            <div  class="header-profile account-redirect">
                <a href="/profile">
                <img src="assets/images/map/profile.png" alt="profile-img">

                </a>
            </div>
        </div>
    </div>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4 text-white text-center">SiJek</h1>
        <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-center">
            <div class="bg-black px-4 py-5 sm:p-6">
                <div class="flex justify-between">
                    <button
                        @click="handleStartDriving"
                        class="rounded-md border border-transparent bg-grey py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        <img src="assets/svg/carpool-svgrepo-com.svg" class="menu_list" alt="" srcset="">
                        Start Driving
                    </button>
                    <button
                        @click="handleFindARide"
                        class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        <img src="assets/svg/Driver-logo.svg" alt="" srcset="">
                        Find A Ride
                    </button>
                </div>
            </div>
        </div>  
    </div> 
    </div> 
</template>
<script setup>
import { useRouter } from 'vue-router'
import http from '@/helpers/http'
import TheNav from '../components/NavView.vue'
import TheSideBar from '../components/SideBar.vue'
import ThePreLoader from '../components/PreLoader.vue'
import { ref, onMounted } from 'vue'

const router = useRouter()
const isLoading = ref(true)

onMounted(() => {
    // Hide loader when page is fully loaded
    window.addEventListener('load', () => {
        isLoading.value = false
    })
    
    // Also hide loader if page is already loaded
    if (document.readyState === 'complete') {
        isLoading.value = false
    }
})

const handleStartDriving = () => {
    http().get('https://sijek-backend.me/api/driver')
        .then((response) => {
            if (response.data.driver) {
                router.push({
                    name: 'standby'
                })
            } else {
                router.push({
                    name: 'driver'
                })
            }
        })
        .catch((error) => {
            console.error(error)
        })
}
const handleFindARide = () => {
    router.push({
        name: 'location'
    })
}
</script>