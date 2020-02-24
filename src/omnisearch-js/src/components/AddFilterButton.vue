<template>
  <div class="omnisearch__add-filter">
    <button type="button"
            class="btn small icon omnisearch__add-filter-btn"
            @click="toggleMenu"
            ref="button"
    >
      {{ buttonText }}
    </button>
    <div v-if="showFieldMenu"
         class="menu omnisearch__filter-panel omnisearch__choose-fields"
         ref="filterPanel"
    >
      <template v-if="selectedField == null">
        <!-- Step 1: choose field from list -->
        <div class="omnisearch__field-list-search">
          <input class="text"
                 type="text"
                 v-model="keyword"
                 placeholder="Search attributes..."
                 ref="searchInput"
          />
        </div>
        <div class="omnisearch__filter-panel-body omnisearch__field-list">
          <div class="omnisearch__field-list-item"
               v-for="(field, index) in fieldList"
               :key="index"
               @click="setSelectedField(field)">
            {{ field.fieldName }}
          </div>
        </div>
      </template>
      <template v-else>
        <!-- Step 2: choose filter method -->
        <div class="omnisearch__filter-panel-body omnisearch__filter-methods">
          <div class="omnisearch__filter-method"
               v-for="filterMethod in filterMethods"
               :key="filterMethod.operator"
               @click="selectFilterMethod(filterMethod)"
          >
            {{ filterMethod.label }}
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { createPopper } from '@popperjs/core';
import sortBy from 'lodash/sortBy';

export default {
  name: 'AddFilterButton',
  props: {
    fields: {
      type: Array,
      required: true,
      default: () => ([]),
    },
  },
  data() {
    return {
      keyword: '',
      selectedField: null,
      showFieldMenu: false,
    };
  },
  computed: {
    buttonText() {
      if (this.showFieldMenu) {
        return this.selectedField ? this.selectedField.fieldName : 'Choose Field';
      }

      return '+ Add Filter';
    },
    fieldList() {
      const filteredFields = this.fields.filter(
        (field) => field.fieldName.toLowerCase().includes(this.keyword.toLowerCase()),
      );

      return sortBy(filteredFields, 'fieldName');
    },
    filterMethods() {
      return [
        { operator: 'contain', label: 'contains' },
        { operator: 'not_contain', label: 'does not contains' },
        { operator: 'equal', label: 'equals' },
        { operator: 'not_equal', label: 'not equal to' },
        { operator: 'starts_with', label: 'starts with' },
        { operator: 'is_present', label: 'is present' },
        { operator: 'is_not_present', label: 'is not present' },
      ];
    },
  },
  watch: {
    showFieldMenu(show) {
      if (show) {
        this.selectedField = null;

        this.$nextTick(() => {
          this.popper = createPopper(this.$refs.button, this.$refs.filterPanel, {
            placement: 'bottom-start',
          });

          this.$refs.searchInput.focus();
        });
      } else if (!show && this.popper) {
        this.popper.destroy();
      }
    },
  },
  methods: {
    toggleMenu() {
      this.showFieldMenu = !this.showFieldMenu;
    },
    setSelectedField(field) {
      this.selectedField = field;
    },
    selectFilterMethod(method) {
      const { operator } = method;

      this.showFieldMenu = false;

      this.$emit('add-filter', {
        field: {
          ...this.selectedField,
        },
        operator,
      });
    },
  },
  beforeDestroy() {
    if (this.popper) {
      this.popper.destroy();
    }
  },
};
</script>

<style scoped>
  .omnisearch__add-filter {
    display: inline-block;
  }
</style>
