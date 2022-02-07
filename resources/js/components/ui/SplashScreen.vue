<template>
  <div class="splash">
    <div class="splash-inner">
      <h1>Accounts Manager</h1>
      <loading-spinner />
    </div>
  </div>
</template>

<script>
import LoadingSpinner from "./LoadingSpinner.vue";
export default {
  components: { LoadingSpinner },
};
</script>
