<template>
  <div data-test="listOptions">
    <div class="flex-grow texticon search icon">
      <input class="text"
             type="text"
             v-model="keyword"
             placeholder="Search..."
             ref="keywordInput"
             data-test="listOptionsFilterInput"
      />
    </div>
    <div>
      <template v-if="isMultiSelect">
        Multi select
      </template>
      <template v-else>
        <div
          data-test="listOption"
          v-for="item in filteredListItems"
          :key="item.value">
          <label>
            <input
              type="radio"
              :value="item.value"
              :checked="value === item.value"
              @change="$emit('change', item.value)"
            /> {{ item.label }}</label>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import FilterMethodMixin from './FilterMethodMixin';

export default {
  name: 'ListFilter',
  mixins: [FilterMethodMixin],
  model: {
    prop: 'value',
    event: 'change',
  },
  props: {
    value: { type: String },
  },
  data() {
    return {
      keyword: '',
    };
  },
  computed: {
    filteredListItems() {
      return this.items.filter(
        (item) => item.label.toLowerCase().includes(this.keyword.toLowerCase()),
      );
    },
    isMultiSelect() {
      return ['in', 'not_in'].includes(this.filterMethod.operator);
    },
  },
  mounted() {
    this.$refs.keywordInput.focus();
  },
};
</script>
