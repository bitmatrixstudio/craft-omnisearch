<template>
  <div class="omnisearch__filter btn small">
    <span class="omnisearch__filter-text">{{ fieldName }} {{ operatorText }}</span>
    <button
      type="button"
      class="omnisearch__remove-filter-btn"
      @click="removeFilter">
      &times;
    </button>
  </div>
</template>
<script>
export default {
  name: 'FilterButton',
  props: {
    filter: {
      type: Object,
      required: true,
    },
  },
  computed: {
    fieldName() {
      return this.filter.field.fieldName;
    },
    operatorText() {
      const { operator, value } = this.filter;
      switch (operator) {
        case 'is_present': {
          return 'is present';
        }

        case 'is_not_present': {
          return 'is not present';
        }

        case 'starts_with': {
          return `starts with "${value}"`;
        }

        case 'contain': {
          return `contains "${value}"`;
        }

        case 'not_contain': {
          return `does not contain "${value}"`;
        }

        default: {
          return 'Invalid operator';
        }
      }
    },
  },
  methods: {
    removeFilter() {
      this.$emit('remove-filter', this.filter);
    },
  },
};
</script>

<style lang="scss">
  .omnisearch__filter {
    margin-right: 0.5em;
  }

  .omnisearch__remove-filter-btn {
    border: none;
    border-radius: 50%;
    padding: 0;
    width: 16px;
    line-height: 1rem;
    margin-left: 0.25em;
    font-size: 16px;
    background-color: transparent;
    cursor: pointer;

    &:hover {
      color: #F00;
    }
  }
</style>
