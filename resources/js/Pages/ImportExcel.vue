<template>
  <app-layout :title="title">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ title }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="upload">
              <input
                type="file"
                @input="form.excel = $event.target.files[0]"
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
              />
              <progress
                v-if="form.progress"
                :value="form.progress.percentage"
                max="100"
              >
                {{ form.progress.percentage }}%
              </progress>
              <button type="submit">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";

export default defineComponent({
  data() {
    return {
      title: "Import Excel",
      form: this.$inertia.form({
        excel: null,
      }),
    };
  },
  components: {
    AppLayout,
  },
  methods: {
    upload() {
      this.form.post(route("upload-excel"));
    },
  },
});
</script>
