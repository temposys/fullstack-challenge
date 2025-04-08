<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";
// import wsClient from "@/utils/websocket";
import type { Weather, User } from "@/types/ApiTypes";

const API_DOMAIN = import.meta.env.VITE_API_DOMAIN;
const users = ref<User[]>([]);
const retryCounts = ref<{ [key: number]: number }>({});
const apiError = ref(false);
const refreshInterval = ref<number | null>(null);
const emit = defineEmits<{
  (e: "show-details", user: any): void;
}>();

let retryCount = 0;
const maxRetries = 3;

const fetchUsersData = async () => {
  apiError.value = false;
  try {
    const response = await axios.get(`${API_DOMAIN}/users?limit=10`);
    users.value = response.data;

    retryCount = 0;
    await updateMissingWeather();
  } catch (error) {
    apiError.value = true;
  }
};

const updateUserData = async (userIds: number[]) => {
  const response = await axios.post(`${API_DOMAIN}/users`, {
    users: userIds,
  });

  const updatedUsers = response.data;
  updatedUsers.forEach((updatedUser: User) => {
    const index = users.value.findIndex((u: User) => u.id === updatedUser.id);
    if (index !== -1 && updatedUser.weather) {
      users.value[index].weather = updatedUser.weather;
    }
  });
};

const updateMissingWeather = async () => {
  // Get IDs of users without weather data
  const usersWithoutWeather = users.value
    .filter((user: User) => !user.weather)
    .map((user: User) => user.id);

  if (usersWithoutWeather.length === 0) {
    apiError.value = false;
    return;
  }

  if (retryCount >= maxRetries) {
    apiError.value = true;
    return;
  }

  try {
    await updateUserData(usersWithoutWeather);
    retryCount++;
    // Wait 1 second before next retry
    setTimeout(() => {
      updateMissingWeather();
    }, 1000);
  } catch (error) {
    apiError.value = true;
  }
};

const showDetails = (user: any) => {
  emit("show-details", user);
};

const refreshAllWeatherData = async () => {
  if (users.value.length === 0) return;

  try {
    const userIds = users.value.map((user: User) => user.id);
    await updateUserData(userIds);
  } catch (error) {
    // do not show an error as it will be refreshed once a minute
  }
};

const startAutoRefresh = () => {
  stopAutoRefresh(); // Clear any existing interval
  refreshInterval.value = window.setInterval(
    refreshAllWeatherData,
    Number(import.meta.env.VITE_AUTOREFRESH)
  );
};

const stopAutoRefresh = () => {
  if (refreshInterval.value !== null) {
    clearInterval(refreshInterval.value);
    refreshInterval.value = null;
  }
};

// Update via WebSocket
// const subscribeWeatherUpdates = () => {
//   // Subscribe to weather updates using the new WebSocket client
//   wsClient.onWeatherUpdate(
//     (data: { userId: number; weather: Weather | null }) => {
//       const index = users.value.findIndex(
//         (u: { id: number }) => u.id === data.userId
//       );
//       if (index !== -1) {
//         users.value[index].weather = data.weather;
//       }
//     }
//   );
// };

onMounted(() => {
  fetchUsersData();
  startAutoRefresh();
  // subscribeWeatherUpdates();
});

onUnmounted(() => {
  stopAutoRefresh();
});
</script>

<template>
  <div
    v-if="apiError"
    class="bg-red-50 border border-red-200 text-red-800 rounded-md p-4 mb-4"
  >
    <div class="flex items-center">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path
          fill-rule="evenodd"
          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
          clip-rule="evenodd"
        ></path>
      </svg>
      <span class="font-medium">Connection Error:</span>
      <span class="ml-1">Failed to load data from the server.</span>
    </div>
    <div class="mt-3">
      <button
        @click="fetchUsersData"
        class="bg-red-600 hover:bg-red-700 text-white font-medium py-1 px-3 rounded text-sm"
      >
        Retry
      </button>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Name
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Weather Condition
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Temperature
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Actions
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="user in users" :key="user.id">
          <td
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
          >
            {{ user.name }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <template v-if="user.weather">
              <img
                :src="user.weather.icon"
                :title="user.weather.condition"
                alt="user.weather.condition"
              />
              {{ user.weather.condition }}
            </template>
            <template v-else-if="retryCounts[user.id] >= 3">
              <span class="text-red-500">Error</span>
            </template>
            <template v-else>Loading...</template>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <template v-if="user.weather"
              >{{ user.weather.temperature }}°F</template
            >
            <template v-else-if="retryCounts[user.id] >= 3">
              <span class="text-red-500">Error</span>
            </template>
            <template v-else>Loading...</template>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <button
              @click="showDetails(user)"
              class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              v-if="user.weather"
            >
              <span class="flex items-center"> Weather Details </span>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
