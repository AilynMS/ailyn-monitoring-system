import { mount, RouterLinkStub } from '@vue/test-utils'
import NavigationMenu from '@/components/shared/navigation/NavigationMenu.vue'

describe('Navigation', () => {
    it('Testing the navigation works correctly', () => {
        const wrapper = mount(NavigationMenu, {
            stubs: {
                nuxtLink: RouterLinkStub
            }
        });

        wrapper.setData({
            showDropdown: false
        });

        wrapper.vm.handleDropdown();
        expect(wrapper.vm.showDropdown).toBeTruthy();
    });
});