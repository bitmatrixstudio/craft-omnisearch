import Vue from 'vue';
import OmniSearch from './components/OmniSearch.vue';

window.onload = function onLoad() {
  const contentContainer = document.querySelector('#content');
  const omnisearchFilters = window.omnisearchFilters || {};

  if (contentContainer !== null) {
    const omnisearchContainer = document.createElement('div');

    contentContainer.before(omnisearchContainer);

    new Vue({
      render: (h) => h(OmniSearch, {
        props: {
          fields: omnisearchFilters,
        },
      }),
    }).$mount(omnisearchContainer);
  }
};
