<template>
  <div class="omnisearch">
    <div class="omnisearch__active-filters"
         v-if="activeFilters.length > 0">
      <filter-button
        v-for="(filter, index) in activeFilters"
        :filter="filter"
        :key="index"
        @remove-filter="removeFilter(index)"
      />
    </div>
    <add-filter-button
      :fields="fields"
      @add-filter="addFilter"
    />
  </div>
</template>

<script>
import AddFilterButton from './AddFilterButton.vue';
import FilterButton from './FilterButton.vue';

export default {
  name: 'OmniSearch',
  components: { FilterButton, AddFilterButton },
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
  },
  data() {
    return {
      activeFilters: [],
    };
  },
  methods: {
    addFilter(filter) {
      this.activeFilters.push(filter);
    },
    removeFilter(index) {
      this.activeFilters.splice(index, 1);
    },
  },
};
</script>

<style lang="scss">
  .omnisearch__active-filters {
    display: inline-block;
  }

  .omnisearch__filter-panel {
    padding: 0 !important;
  }

  .omnisearch__field-list-search {
    padding: 0.5rem;
  }

  .omnisearch__field-list-search + .omnisearch__filter-panel-body {
    padding-top: 0;
  }

  .omnisearch__filter-panel-body,
  .omnisearch__filter-panel-footer {
    padding: 0.5rem;
  }
</style>