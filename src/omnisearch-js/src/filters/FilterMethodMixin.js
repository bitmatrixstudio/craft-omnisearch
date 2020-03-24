export default {
  props: {
    filterMethod: {
      type: Object,
      required: true,
    },
    items: {
      type: Array,
    },
  },
  methods: {
    apply() {
      this.$emit('apply');
    },
  },
};
