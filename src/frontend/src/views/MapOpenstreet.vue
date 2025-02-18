<template>
    <div class="pt-16">
        <h1 class="text-3xl text-center text-white font-semibold mb-4">Here's your trip</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <div id="map" ref="map" style="width: 100%; height: 256px;"></div>
                    </div>
                    <div class="mt-2">
                        <p class="text-xl">Going to <strong>{{ location.destination.name }}</strong></p>
                        <p class="text-xl distance">Distance: <strong></strong></p> 
                        <p class="text-xl fare">Fare: <strong></strong></p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button
                        @click="handleConfirmTrip"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        Let's Go!</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import http from '@/helpers/http'
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router'
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const map = ref(null)

const handleConfirmTrip = () => {
    let intervalId;
    let attempt = 0;
    function createTrip() {
        http().post('https://sijek-backend.me/api/trip', {
        origin: location.current.geometry,
        destination: location.destination.geometry,
        destination_name: location.destination.name
        })
            .then((response) => {
                trip.$patch(response.data)
                router.push({
                    name: 'trip'
                })
            })
            .catch((error) => {
                console.error(error)
            })
        attempt++;
        if (attempt >= 3) {
            clearInterval(intervalId);
            // alert('No driver found, please try again later')
            // router.push({
            //     name: 'location'
            // })
        }
    }

    intervalId = setInterval(createTrip, 10000);
    
    
}

onMounted(async () => {
    // does the user have a location set?
    if (location.destination.name === '') {
        router.push({
            name: 'location'
        })
    }

    // lets get the users current location
    await location.updateCurrentLocation()

    // Initialize OpenStreetMap
    map.value = L.map('map').setView(location.current.geometry, 11);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map.value);

    // Add OSRM routing
    const routingControl = L.Routing.control({
        waypoints: [
            L.latLng(location.current.geometry.lat, location.current.geometry.lng),
            L.latLng(location.destination.geometry.lat, location.destination.geometry.lng)
        ],
        routeWhileDragging: true,
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1'
        })
    }).addTo(map.value);

    // Update fare when distance changes
    routingControl.on('routesfound', function(e) {
        const routes = e.routes;
        const distance = routes[0].summary.totalDistance / 1000;
        const fare = calculateFare(distance);
        
        trip.$patch({ 
            distance: distance.toFixed(2) + ' km',
            fare: 'Rp' + fare.toLocaleString('id-ID')
        });
    
    document.querySelector('.distance').innerHTML = distance.toFixed(2) + ' km';
    document.querySelector('.fare').innerHTML = 'Rp' + fare.toLocaleString('id-ID');
});
    // Listen for route calculation
const calculateFare = (distance) => {
    const baseFare = 10000;
    const additionalFarePerKm = 3500;
    const peakHourSurcharge = 3000;
    
    // Get current time in WIB (UTC+7)
    const now = new Date();
    const hours = now.getUTCHours() + 7;
    
    // Check if it's peak hour
    const isPeakHour = 
        (hours >= 6 && hours < 9) || // 06.00-09.00 WIB
        (hours >= 16 && hours < 20); // 16.00-20.00 WIB
    
    // Calculate fare
    let fare = baseFare;
    if (distance > 1) {
        fare += (distance - 1) * additionalFarePerKm;
    }
    if (isPeakHour) {
        fare += distance * peakHourSurcharge;
    }
    fare = Math.ceil(fare / 500) * 500;
    console.log(fare)

    return fare;
}


})
</script>