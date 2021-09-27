<template>
  <div id="app" style="margin: 30px 15px;">
    <omni-search :fields="fields" :initial-filters="initialFilters"></omni-search>
  </div>
</template>

<script>
import OmniSearch from './components/OmniSearch.vue';
import DATATYPES from './datatypes';

import './styles.scss';

export default {
  name: 'App',
  data() {
    return {
      initialFilters: [
        {
          field: 'city',
          operator: 'in',
          value: ['1', '2'],
        },
      ],
      fields: [
        {
          handle: 'title',
          name: 'Title',
          dataType: DATATYPES.TEXT,
        },
        {
          handle: 'postDate',
          name: 'Post Date',
          dataType: DATATYPES.DATE,
        },
        {
          handle: 'rating',
          name: 'Rating',
          dataType: DATATYPES.NUMBER,
        },
        {
          handle: 'isFeatured',
          name: 'Is Featured',
          dataType: DATATYPES.BOOLEAN,
        },
        {
          handle: 'city',
          name: 'City',
          dataType: DATATYPES.LIST,
          items: [
            { value: '1', label: 'Singapore' },
            { value: '2', label: 'Johor Bahru' },
            { value: '3', label: 'Kuala Lumpur' },
            { value: '4', label: 'Penang' },
            { value: '5', label: 'Melbourne' },
            { value: '6', label: 'Sydney' },
            { value: '7', label: 'Perth' },
            { value: '8', label: 'New York' },
            { value: '9', label: 'London' },
            { value: '10', label: 'Manchester' },
            { value: '11', label: 'Liverpool' },
            { value: '12', label: 'Birmingham' },
            { value: '13', label: 'Tokyo' },
            { value: '14', label: 'Kyoto' },
            { value: '15', label: 'Osaka' },
            { value: '16', label: 'Sapparo' },
            { value: '17', label: 'Bangkok' },
            { value: '18', label: 'Pattaya' },
            { value: '19', label: 'Chiang Mai' },
            { value: '20', label: 'Phuket' },
            { value: '21', label: 'Hua Hin' },
            { value: '22', label: 'Ko Samui' },
          ],
        },
      ],
    };
  },
  components: {
    OmniSearch,
  },
};
</script>

<style lang="scss">
</style>
