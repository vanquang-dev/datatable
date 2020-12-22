<template>
  <nav class="pagination" v-if="!client">
    <span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>
    <a
      v-if="pagination.prevPageUrl"
      class="btn btn-secondary btn-sm"
      @click="$emit('prev');"
    >Prev</a>
    <a class="btn btn-secondary btn-sm" v-else :disabled="true">Prev</a>

    <a
      v-if="pagination.nextPageUrl"
      class="btn btn-secondary btn-sm"
      @click="$emit('next');"
    >Next</a>
    <a class="btn btn-secondary btn-sm" v-else :disabled="true">Next</a>
  </nav>

  <nav class="pagination" v-else>
    <span class="page-stats">
      {{pagination.from}} - {{pagination.to}} of {{filtered.length}}
      <span
        v-if="filtered.length < pagination.total"
      >(filtered from {{pagination.total}} total entries)</span>
    </span>
    <a
      v-if="pagination.prevPage"
      class="btn btn-secondary btn-sm"
      @click="$emit('prev');"
    >Prev</a>
    <a class="btn btn-secondary btn-sm" v-else :disabled="true">Prev</a>

    <a
      v-if="pagination.nextPage"
      class="btn btn-secondary btn-sm"
      @click="$emit('next');"
    >Next</a>
    <a class="btn btn-secondary btn-sm" v-else :disabled="true">Next</a>
  </nav>
</template>

<script>
export default {
  props: ["pagination", "client", "filtered"]
};
</script>

<style lang="scss" scoped>
.pagination {
  justify-content: flex-end !important;
  .page-stats {
    align-items: center;
    margin-right: 5px;
  }
  i {
    color: #3273dc !important;
  }
}
</style>