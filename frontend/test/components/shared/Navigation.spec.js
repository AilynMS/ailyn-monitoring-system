import { mount, RouterLinkStub } from '@vue/test-utils'
import Navigation from '@/components/shared/navigation/Navigation.vue'

describe('Navigation', () => {
    it('Testing the navigation works correctly', () => {
        const wrapper = mount(Navigation, {
            stubs: {
                nuxtLink: RouterLinkStub
            }
        });

        wrapper.setData({
            showNavbar: false
        });

        wrapper.vm.navbarToggler();
        expect(wrapper.vm.showNavbar).toBeTruthy();

        wrapper.vm.navbarToggler();
        expect(wrapper.vm.showNavbar).toBeFalsy();
    });
});