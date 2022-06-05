import { mount, RouterLinkStub } from '@vue/test-utils'
import Register from '@/pages/auth/register/index.vue';
import 'regenerator-runtime/runtime';

describe('Login view', () => {
    it('Form operation test on submit', async () => {
        const wrapper = mount(Register, {
            stubs: {
                nuxtLink: RouterLinkStub
            }
        });

        wrapper.setData({
            isLoading: false,
            hasError: false,
            formCompleted: true,
            name: 'name',
            email: 'email@example.com',
            password: 'password',
            confirmPassword: 'password',
            organization: 'enterprise',
            hourZone: 'UTC',
            country: 'Colombia',
            reCaptchaToken: '12138412321',
            acceptTerms: true,
        })

        //  Submit event       
        await wrapper.vm.register();

        //  If submit is pressed, isLoading must be true
        expect(wrapper.vm.isLoading).toBeTruthy();
        expect(wrapper.vm.hasError).toBeFalsy();

        // If submit is pressed, the submit button should be disabled showing another in its place  
        expect(wrapper.find('.submitButton').exists()).toBeFalsy();
        expect(wrapper.find('.loadingButton').exists()).toBeTruthy();

        expect(wrapper.vm.name.length).toBeGreaterThan(0);
        expect(wrapper.vm.organization.length).toBeGreaterThan(0);
        expect(wrapper.vm.hourZone.length).toBeGreaterThan(0);
        expect(wrapper.vm.country.length).toBeGreaterThan(0);
        expect(wrapper.vm.email.length).toBeGreaterThan(0);
        expect(wrapper.vm.password.length).toBeGreaterThan(0);
        expect(wrapper.vm.confirmPassword.length).toBeGreaterThan(0);
        expect(wrapper.vm.reCaptchaToken.length).toBeGreaterThan(0);
        expect(wrapper.vm.acceptTerms).toBeTruthy();
    });
});