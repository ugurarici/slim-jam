<template>
  <div>burası Translation/Index.vue</div>
  <table>
    <tr>
      <th>id</th>
      <th>string</th>
      <th>target</th>
      <th>result</th>
    </tr>
    <tr v-for="translation in translations" :key="translation.id">
      <td>{{ translation.id }}</td>
      <td>{{ translation.string }}</td>
      <td>{{ translation.target }}</td>
      <td>{{ translation.result }}</td>
    </tr>
  </table>
</template>

<script>
export default {
  props: ["translations"],
};
</script>
