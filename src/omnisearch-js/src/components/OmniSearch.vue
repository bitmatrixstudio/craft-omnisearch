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
    initialFilters: {
      type: Array,
      default: () => ([]),
    },
  },
  data() {
    return {
      activeFilters: [...this.initialFilters],
    };
  },
  watch: {
    activeFilters: {
      deep: true,
      handler() {
        this.updateFilterCriteria();
      },
    },
  },
  methods: {
    addFilter(filter) {
      this.activeFilters.push(filter);
    },
    removeFilter(index) {
      this.activeFilters.splice(index, 1);
    },
    updateFilterCriteria() {
      const elementIndex = window?.Craft?.elementIndex;
      const criteria = elementIndex?.settings?.criteria;

      if (criteria == null) {
        return;
      }

      criteria.omnisearchFilters = [...this.activeFilters];
      elementIndex.updateElements();
    },
  },
};
</script>

<style lang="scss">
  .omnisearch {
    position: relative;
    margin-top: -1em;
    margin-bottom: 1em;

    .btn:not(.small) {
      font-size: 1em;
    }

    .omnisearch__active-filters {
      display: inline-block;
    }

    .omnisearch__filter-panel {
      display: block;
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

    .omnisearch__list-item {
      cursor: pointer;
      padding: 10px 14px;
      margin: 0 -0.5rem;

      &:hover {
        background-color: #f3f7fc;
      }
    }
  }
</style>
