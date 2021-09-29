/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

export default {
  props: {
    filterMethod: {
      type: Object,
      required: true,
    },
    items: {
      type: Array,
      default: () => () => [],
    },
  },
  methods: {
    apply(value) {
      this.$emit('apply', value);
    },
  },
};
