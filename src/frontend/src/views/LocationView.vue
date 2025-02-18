<template>
          <header id="top-header">
      <div class="header-wrap">
        <div class="header-back">
          <a href="javascript:history.go(-1)"><img src="assets/svg/back-btn-white.svg" alt="back-btn"></a>
        </div>
        <div class="header-name">
          <p>location</p>
        </div>
      </div>
      <div class="boder"></div>
    </header>
    <div class="pt-16">
        <h1 class="text-3xl text-white text-center font-semibold mb-4">Where are we going?</h1>
        <form action="#" @submit.prevent="">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="px-4 py-5 sm:p-6">
                    <div>
                        <GMapAutocomplete
                            placeholder="My destination"
                            @place_changed="handleLocationChanged"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                        </GMapAutocomplete>
                        <div class="mt-4" style="height: 300px;">
                            <GMapMap
                                v-if="location.current.geometry"
                                :center="location.current.geometry || location.destination.geometry"
                                :zoom="12"
                                style="width: 100%; height: 100%;"
                            >
                                <GMapMarker 
                                    v-if="location.destination.geometry?.lat"
                                    :position="location.destination.geometry" 
                                />
                            </GMapMap>
                            <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                                <p>Loading map...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" px-4 py-3 text-right sm:px-6 sign-in-password-btn mt-32">
                    <a>
                        <button
                        @click.prevent="handleSelectLocation"
                        type="button"
                        >
                        Find A Ride
                    </button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</template>
<script setup>
import { useLocationStore } from '@/stores/location'
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'

const location = useLocationStore()
const router = useRouter()
const mapCenter = ref({
    lat: 0,
    lng: 0
})

onMounted(async () => {
    await location.updateCurrentLocation()
    mapCenter.value = location.current.geometry
})


const handleLocationChanged = (e) => {
    console.log('handleLocationChanged', e)
    location.$patch({
        destination: {
            name: e.name,
            address: e.formatted_address,
            geometry: {
                lat: e.geometry.location.lat(),
                lng: e.geometry.location.lng()
            }
        }
    })
    mapCenter.value = {
        lat: e.geometry.location.lat(),
        lng: e.geometry.location.lng()
    }
}

const handleSelectLocation = () => {
    if (location.destination.name !== '') {
        router.push({
            name: 'map'
        })
    }
}
</script>