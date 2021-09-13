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
