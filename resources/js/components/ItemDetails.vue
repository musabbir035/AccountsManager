<template>
  <div class="details">
    <div v-for="key in Object.keys(this.detailsData)" :key="key">
      <div v-if="key != 'added_by_name'">
        <div class="details-label">{{ formatKeyName(key) }}:</div>
        <div class="details-content">{{ detailsData[key] }}</div>
      </div>
      <div v-else>
        <div class="details-label text-right">Added By:</div>
        <div class="details-content">
          <router-link :to="'/user/' + addedById">
            {{ detailsData[key] }}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["detailsData"],
  dat() {
    return {
      addedById: null,
    };
  },
  methods: {
    formatKeyName(key) {
      const a = key.split("_");
      var el = "";
      a.forEach((e) => {
        e = e.charAt(0).toUpperCase() + e.substring(1);
        el = el + " " + e;
      });
      return el;
    },
  },
  beforeMount() {
    delete this.detailsData.role_id;
    if (!this.detailsData.deleted_at) {
      delete this.detailsData.deleted_at;
    }
    this.addedById = this.detailsData.added_by_id || null;
    if (this.detailsData.added_by_id) {
      delete this.detailsData.added_by_id;
    }
  },
};
</script>

<style>
.details-label {
  width: 40%;
  display: inline-block;
  margin-right: 5px;
  font-weight: 600;
}
.details-content {
  width: calc(100%-40%);
  display: inline-block;
}
</style>