<template>
  <div id="app" style="margin: 30px 15px;">
    <omni-search :fields="fields"></omni-search>
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
          handle: 'tags',
          name: 'Tags',
          dataType: DATATYPES.LIST,
          items: [
            { value: 'a', label: 'Item A' },
            { value: 'b', label: 'Item B' },
            { value: 'c', label: 'Item C' },
            { value: 'd', label: 'Item D' },
            { value: 'e', label: 'Item E' },
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
