import { mount, RouterLinkStub } from '@vue/test-utils'
import Verify from '@/pages/auth/verify/index.vue';
import 'regenerator-runtime/runtime';

describe('Verify email test', () => {
    it('has correct text content', () => {
        const wrapper = mount(Verify, {
            stubs: {
                nuxtLink: RouterLinkStub
            }
        });

        const p = wrapper.find('p');
        expect(p.exists()).toBe(true);
    });
});