<template>
  <div :class="{ 'spinner-backdrop': backdrop }">
    <div class="spinner-outer">
      <div class="spinner spinner--double-bounce">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["backdrop"],
};
</script>
