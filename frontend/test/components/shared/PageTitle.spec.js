import { mount } from '@vue/test-utils'
import PageTitle from '@/components/shared/PageTitle.vue'

describe('PageTitle test', () => {
    it('If valid props', () => {
        const wrapper = mount(PageTitle, {
            propsData: {
                title: 'This is a title'
            }
        });

        expect(wrapper.props().classes).toBe('This is a title');
    });
});