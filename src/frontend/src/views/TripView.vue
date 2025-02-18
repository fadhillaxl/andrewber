<template>
    <div class="pt-16">
        <h1 class="text-3xl text-center text-white font-semibold mb-4">{{ title }}</h1>
        <div>
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <div id="map" ref="map" style="width:100%; height: 256px;"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <span>{{ message }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useLocationStore } from '@/stores/location'
import { useTripStore } from '@/stores/trip'
import { useRouter } from 'vue-router'
import { onMounted, ref } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';

const location = useLocationStore()
const trip = useTripStore()
const router = useRouter()

const title = ref('Waiting on a driver...')
const message = ref('When a driver accepts the trip, their info will appear here.')

const map = ref(null)
let routingControl = null

const currentIcon = {
    url: 'https://openmoji.org/data/color/svg/1F920.svg',
    scaledSize: {
        width: 24,
        height: 24
    }
}

const driverIcon = {
    url: 'https://openmoji.org/data/color/svg/1F698.svg',
    scaledSize: {
        width: 32,
        height: 32
    }
}

const updateMapBounds = () => {
    if (map.value && routingControl) {
        routingControl.setWaypoints([
            L.latLng(location.current.geometry.lat, location.current.geometry.lng),
            L.latLng(trip.driver_location.lat, trip.driver_location.lng)
        ]);
    }
}

onMounted(() => {
    // Initialize map
    map.value = L.map('map').setView(location.current.geometry, 14);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map.value);

    // Initialize routing control
    routingControl = L.Routing.control({
        waypoints: [
            L.latLng(location.current.geometry.lat, location.current.geometry.lng),
            L.latLng(trip.driver_location.lat, trip.driver_location.lng)
        ],
        routeWhileDragging: true,
        router: L.Routing.osrmv1({
            serviceUrl: 'https://router.project-osrm.org/route/v1'
        }),
        show: false // Hide the routing instructions
    }).addTo(map.value);

    let echo = new Echo({
        broadcaster: 'pusher',
        key: 'mykey',
        cluster: 'mt1',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss']
    })

    

    // Add timeout for driver acceptance
    let attempt = 0;
    const maxAttempts = 3;
    const intervalId = setInterval(() => {
        attempt++;
        console.log(attempt)
        if (attempt >= maxAttempts) {
            clearInterval(intervalId);
            alert('No driver found, please try again later');
            trip.reset();
            location.reset();
            router.push({ name: 'location' });
        }
    }, 10000); // Check every 10 seconds

    echo.channel(`passenger_${trip.user_id}`)
        .listen('TripAccepted', (e) => {
            clearInterval(intervalId); // Clear the timeout if trip is accepted
            trip.$patch(e.trip)
            console.log(e);

            title.value = "A driver is on the way!"
            message.value = `${e.trip.driver.user.name} is coming in a ${e.trip.driver.year} ${e.trip.driver.color} ${e.trip.driver.make} ${e.trip.driver.model} with a license plate #${e.trip.driver.license_plate}`
        })
        .listen('TripLocationUpdated', (e) => {
            trip.$patch(e.trip)

            setTimeout(updateMapBounds, 1000)
        })
        .listen('TripStarted', (e) => {
            trip.$patch(e.trip)
            location.$patch({
                current: {
                    geometry: e.trip.destination
                }
            })
            title.value = "You're on your way!"
            message.value = `You are headed to ${e.trip.destination_name}`
        })
        .listen('TripEnded', (e) => {
            trip.$patch(e.trip)

            title.value = "You've arrived!"
            message.value = `Hope you enjoyed your ride with ${e.trip.driver.user.name}`
            router.push({
                name: 'landing'
            })

            setTimeout(() => {
                trip.reset()
                location.reset()
                
                router.push({
                    name: 'landing'
                })
            }, 10000)
        })
})
</script>