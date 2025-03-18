import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import LoginView from '@/views/LoginView.vue'
import LandingView from '@/views/LandingView.vue'
import LocationView from '@/views/LocationView.vue'
import MapView from '@/views/MapView.vue'
import MapOpenstreetView from '@/views/MapOpenstreet.vue'
import TripView from '@/views/TripView.vue'
import StandbyView from '@/views/StandbyView.vue'
import DriverView from '@/views/DriverView.vue'
import DrivingView from '@/views/DrivingView.vue'
import HomeView from '@/views/HomeView.vue'
import AboutView from '@/views/AboutView.vue'
import ProfileView from '@/views/ProfileView.vue'



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'login', component: LoginView },
    { path: '/landing', name: 'landing', component: LandingView },
    { path: '/location', name: 'location', component: LocationView },
    { path: '/map', name: 'map', component: MapOpenstreetView },
    { path: '/map-openstreet', name: 'map-openstreet', component: MapOpenstreetView },
    { path: '/trip', name: 'trip', component: TripView },
    { path: '/standby', name: 'standby', component: StandbyView },
    { path: '/driver', name: 'driver', component: DriverView },
    { path: '/driving', name: 'driving', component: DrivingView },
    { path: '/home', name: 'home', component: HomeView },
    { path: '/about', name: 'about', component: AboutView },
    { path: '/profile', name: 'profile', component: ProfileView },
    { path: '/logout', name: 'logout', beforeEnter: logout }
  ]
})

// Authentication Guard
router.beforeEach(async (to) => {
  if (to.name === 'login') {
    return true
  }

  const token = localStorage.getItem('token')
  if (!token) {
    return { name: 'login' }
  }

  return checkTokenAuthenticity()
})

const checkTokenAuthenticity = async () => {
  try {
    await axios.get('http://127.0.0.1:8000/api/user', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    return true
  } catch (error) {
    localStorage.removeItem('token')
    return { name: 'login' }
  }
}

async function logout() {
  try {
    await axios.post('http://127.0.0.1:8000/api/logout', {}, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
  } catch (error) {
    console.error('Logout failed', error)
  } finally {
    localStorage.removeItem('token')
    return { name: 'login' }
  }
}

export default router
