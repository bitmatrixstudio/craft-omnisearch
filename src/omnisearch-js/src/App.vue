<template>
  <div id="app" style="margin: 30px 15px;">
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
          handle: 'title',
          name: 'Title',
        },
        {
          handle: 'postDate',
          name: 'Post Date',
        },
        {
          handle: 'area',
          name: 'Area',
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
