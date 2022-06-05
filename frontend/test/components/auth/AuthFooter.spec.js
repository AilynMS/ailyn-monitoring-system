import { mount, RouterLinkStub } from '@vue/test-utils';
import AuthFooter from '@/components/auth/AuthFooter';

describe('AuthFooter', () => {
    it('Submitting properties', () => {
        const wrapper = mount(AuthFooter, {
            stubs: {
                nuxtLink: RouterLinkStub
            },
            propsData: {
                url: '/home',
                urlText: 'Url text',
                contextText: 'Context text'
            }
        });

        expect(wrapper.props().url).toBe('/home');
        expect(wrapper.props().url).not.toBeUndefined();

        expect(wrapper.props().urlText).toBe('Url text');
        expect(wrapper.props().urlText).not.toBeUndefined();

        expect(wrapper.props().contextText).toBe('Context text');
        expect(wrapper.props().contextText).not.toBeUndefined();
    });
});