import { shallowMount } from '@vue/test-utils';
import AddFilterButton from '../../src/components/AddFilterButton.vue';

describe('AddFilterButton.vue', () => {
  let wrapper;

  beforeEach(() => {
    wrapper = shallowMount(AddFilterButton, {
      propsData: {
        fields: [],
      },
    });
  });

  it('should render correctly', () => {
    expect(wrapper).toMatchSnapshot();
  });
});
