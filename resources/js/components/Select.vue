<template>
  <select
    v-model="selectedValue"
    @change="$emit('on-selected', selectedValue, type)"
    :id="type"
    class="form-select"
    aria-label="Default select example"
  >
    <option selected value="">
      {{ "Seleziona " + placeholder || "Seleziona" }}
    </option>
    <option v-for="(option, i) in options" :key="i" :value="option.value">
      {{ option.text }}
    </option>
  </select>
</template>

<script>
export default {
  nome: "Select",
  data() {
    return {
      selectedValue: "",
    };
  },
  props: ["options", "type", "placeholder"],
};
</script>

<style>
</style>