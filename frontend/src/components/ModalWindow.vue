<template>
  <dialog
    ref="dialog"
    :open="isOpen"
    class="rounded-lg bg-white shadow-xl px-10 py-6 w-96"
  >
    <!-- Close button -->
    <div class="absolute right-0 top-0 pr-4 pt-4">
      <button
        type="button"
        class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        @click="closeDialog"
      >
        <span class="sr-only">Close</span>
        <svg
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>

    <!-- Content -->
    <div class="flex">
      <div class="mt-3 text-center sm:mt-0 w-full">
        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
          {{ title }}
        </h3>
        <slot></slot>
      </div>
    </div>
  </dialog>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from "vue";

const props = defineProps<{
  isOpen: boolean;
  title: string;
}>();

const emit = defineEmits<{
  (e: "close"): void;
}>();

const dialog = ref<HTMLDialogElement | null>(null);

const closeDialog = () => {
  if (dialog.value) {
    dialog.value.close();
    emit("close");
  }
};

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === "Escape") {
    closeDialog();
  }
};

watch(
  () => props.isOpen,
  (newVal) => {
    if (dialog.value) {
      if (newVal) {
        dialog.value.showModal();
      } else {
        dialog.value.close();
      }
    }
  }
);

onMounted(() => {
  document.addEventListener("keydown", handleEscape);
});

onUnmounted(() => {
  document.removeEventListener("keydown", handleEscape);
});
</script>
