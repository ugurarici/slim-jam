<template>
  <app-layout title="Translations">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Translations
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center mb-4">
          <input type="checkbox" v-model="isAutoSaveEnabled" id="chkAutoSave" />
          <label for="chkAutoSave" class="ml-2">Auto Save</label>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="w-full mx-auto overflow-auto">
            <table class="table-auto w-full whitespace-no-wrap">
              <thead>
                <tr class="bg-indigo-700 text-white">
                  <th class="p-2">id</th>
                  <th class="p-2">string</th>
                  <th class="p-2">target</th>
                  <th class="p-2">result</th>
                </tr>
              </thead>
              <tbody>
                <translation-table-row
                  v-for="translation in translations"
                  :key="translation.id"
                  :translation="translation"
                  :autosave="isAutoSaveEnabled"
                />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TranslationTableRow from "./Partials/TranslationTableRow.vue";

export default defineComponent({
  props: ["translations"],
  components: {
    AppLayout,
    TranslationTableRow,
  },
  data() {
    return {
      isAutoSaveEnabled: true,
    };
  },
});
</script>
