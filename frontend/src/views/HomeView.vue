<script setup lang="ts">
import { ref } from "vue";
import WeatherView from "@/components/WeatherView.vue";
import ModalWindow from "@/components/ModalWindow.vue";
import WeatherDetail from "@/components/WeatherDetail.vue";

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
  return new Date(dateTime).toLocaleTimeString([], {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
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
            <p class="text-sm text-gray-600">
              Weather data for 20 randomly selected users
            </p>
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
    <ModalWindow
      :is-open="isModalOpen"
      :title="
        userSelected?.name +
        (userSelected?.weather?.city ? '. ' + userSelected?.weather?.city : '')
      "
      @close="closeModal"
    >
      <div class="mt-4">
        <div v-if="userSelected?.weather" class="space-y-6">
          <div class="space-y-1">
            <p class="text-sm font-medium text-gray-500">
              {{ new Date(userSelected.weather.calculatedAt).toLocaleString() }}
            </p>
          </div>
          <!-- Weather Icon and Main Info -->
          <div class="flex items-center justify-center space-x-4">
            <img
              :src="userSelected.weather.icon"
              :alt="userSelected.weather.description"
              class="w-16 h-16"
            />
            <div class="text-center">
              <p class="text-4xl font-bold">
                {{ userSelected.weather.temperature }}°F
              </p>
              <p class="text-xl text-gray-600 capitalize">
                {{ userSelected.weather.description }}
              </p>
            </div>
          </div>

          <!-- Detailed Weather Info -->
          <div class="grid grid-cols-2 gap-6">
            <WeatherDetail
              label="Feels Like"
              :value="`${userSelected.weather.feelsLike}°F`"
            />
            <WeatherDetail
              label="Humidity"
              :value="`${userSelected.weather.humidity}%`"
            />
            <WeatherDetail
              label="Wind Speed"
              :value="`${userSelected.weather.windSpeed} m/h`"
            />
            <WeatherDetail
              label="Wind Angle"
              :value="`${userSelected.weather.windAngle} m/h`"
            />
            <WeatherDetail
              label="Wind Direction"
              :value="userSelected.weather.windDirection"
            />
            <WeatherDetail
              label="Pressure"
              :value="`${userSelected.weather.pressure} hPa`"
            />
            <WeatherDetail
              label="Sunrise"
              :value="formatTime(userSelected.weather.sunrise)"
            />
            <WeatherDetail
              label="Sunset"
              :value="formatTime(userSelected.weather.sunset)"
            />
          </div>
        </div>
      </div>
    </ModalWindow>
  </div>
</template>
