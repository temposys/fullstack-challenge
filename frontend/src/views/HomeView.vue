<script setup lang="ts">
import { ref } from 'vue';
import WeatherView from "@/components/WeatherView.vue";
import Modal from "@/components/Modal.vue";

const isModalOpen = ref(false);
const userSelected = ref<any>(null);

const openWeatherModal = (user: any) => {
  userSelected.value = user;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  userSelected.value = null;
};

const formatTime = (dateTime: string) => {
  return new Date(dateTime).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-primary-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Weather Dashboard</h1>
            <p class="text-sm text-gray-600">Weather data for 20 randomly selected users</p>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
          <weather-view @show-details="openWeatherModal"></weather-view>
        </div>
      </div>
    </main>

    <!-- Weather Details Modal -->
    <Modal
      :is-open="isModalOpen"
      :title="userSelected?.name + (userSelected?.weather?.city ? '. ' + userSelected?.weather?.city : '')"
      @close="closeModal"
    >
   
      <div class="mt-4">
        <div v-if="userSelected?.weather" class="space-y-6">
          <div class="space-y-1">
            <p class="text-sm font-medium text-gray-500">{{ new Date(userSelected.weather.calculatedAt).toLocaleString() }}</p>
          </div>
          <!-- Weather Icon and Main Info -->
          <div class="flex items-center justify-center space-x-4">
            <img 
              :src="userSelected.weather.icon" 
              :alt="userSelected.weather.description"
              class="w-16 h-16"
            />
            <div class="text-center">
              <p class="text-4xl font-bold">{{ userSelected.weather.temperature }}°F</p>
              <p class="text-xl text-gray-600 capitalize">{{ userSelected.weather.description }}</p>
            </div>
          </div>

          <!-- Detailed Weather Info -->
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Feels Like</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.feelsLike }}°F</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Humidity</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.humidity }}%</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Wind Speed</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.windSpeed }} m/h</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Wind Angle</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.windAngle }} m/h</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Wind Direction</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.windDirection }}</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Pressure</p>
              <p class="text-lg font-semibold">{{ userSelected.weather.pressure }} hPa</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Sunrise</p>
              <p class="text-lg font-semibold">{{ new Date(userSelected.weather.sunrise).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true }) }}</p>
            </div>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-500">Sunset</p>
              <p class="text-lg font-semibold">{{ formatTime(userSelected.weather.sunset) }}</p>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>
