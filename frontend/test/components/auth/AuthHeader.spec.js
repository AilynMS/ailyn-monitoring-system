import { mount } from '@vue/test-utils';
import AuthHeader from '@/components/auth/AuthHeader.vue';

describe('AuthHeader Component', () => {
    it('Submitting properties', () => {
        const wrapper = mount(AuthHeader, {
            propsData: {
                brandLogo: '/undefined'
            }
        });
        expect(wrapper.props().brandLogo).toBe('/undefined');
        expect(wrapper.props().brandLogo).not.toBeUndefined();
    });
});