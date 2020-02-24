<template>
  <div id="app">
    <omni-search :fields="fields"></omni-search>
  </div>
</template>

<script>
import OmniSearch from './components/OmniSearch.vue';
import './styles.scss';

export default {
  name: 'App',
  data() {
    return {
      fields: [
        {
          fieldName: 'Title',
        },
        {
          fieldName: 'Post Date',
        },
        {
          fieldName: 'Area',
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
