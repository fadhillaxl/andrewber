import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import VueGoogleMaps from '@fawmi/vue-google-maps'

// Import jQuery & Slick Carousel properly
import * as $ from 'jquery'; // Correct way for Vite
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import 'slick-carousel';
import './assets/js/jquery.min.js';
import './assets/js/jquery.ui.min.js';
import './assets/js/slick.min.js';

import './assets/js/intlTelInput.min.js';
import './assets/js/flag.js';
import './assets/js/custom.js';

// Import Bootstrap (JS & CSS)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyCAqdwRPpTtDGc6lWZKlSO0EPgkAKRo-8o',
        libraries: 'places'
    }
})

app.mount('#app')
