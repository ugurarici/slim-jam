<template>
  <tr>
    <td class="p-2 w-1/12">{{ translation.id }}</td>
    <td class="p-2 w-6/12">{{ translation.string }}</td>
    <td class="p-2 w-1/12">{{ translation.target }}</td>
    <td class="p-2 w-4/12">
      <form @submit.prevent="updateTranslation" class="flex items-center">
        <input type="hidden" name="_method" value="PUT" />
        <input
          type="text"
          class="rounded-l-lg border-2 border-indigo-500"
          name="result"
          v-model="form.result"
        />
        <button
          type="submit"
          class="
            text-white
            bg-indigo-500
            px-4
            py-2
            border-indigo-500 border-2
            rounded-r-lg
            hover:bg-indigo-700 hover:border-indigo-700
          "
        >
          Edit
        </button>
        <jet-action-message :on="form.recentlySuccessful" class="ml-3">
          Updated
        </jet-action-message>
      </form>
    </td>
  </tr>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
export default {
  components: {
    JetActionMessage,
  },
  props: ["translation"],
  data() {
    return {
      form: this.$inertia.form({
        _method: "PUT",
        result: this.translation.result,
      }),
    };
  },
  methods: {
    updateTranslation() {
      this.form.post(route("translations.update", this.translation.id), {
        errorBag: "updateTranslation",
        preserveScroll: true,
      });
    },
  },
};
</script>
