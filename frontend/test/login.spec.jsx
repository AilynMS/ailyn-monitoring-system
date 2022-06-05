import { mount, RouterLinkStub } from '@vue/test-utils'
import Login from '@/pages/auth/login/index.vue';
import 'regenerator-runtime/runtime';

describe('Login view', () => {
    it('Form operation test on submit', async () => {
        const wrapper = mount(Login, {
            stubs: {
                nuxtLink: RouterLinkStub
            }
        });

        wrapper.setData({
            isLoading: false,
            hasError: false,
            formCompleted: true,
            reCaptchaToken: 'example',
            email: 'email@example.com',
            password: 'password'
        })

        //  Submit event       
        await wrapper.vm.login();

        //  If submit is pressed, isLoading must be true
        expect(wrapper.vm.isLoading).toBeTruthy();
        expect(wrapper.vm.hasError).toBeFalsy();

        // If submit is pressed, the submit button should be disabled showing another in its place  
        expect(wrapper.find('.submitButton').exists()).toBeFalsy();
        expect(wrapper.find('.loadingButton').exists()).toBeTruthy();

        expect(wrapper.vm.email.length).toBeGreaterThan(0);
        expect(wrapper.vm.password.length).toBeGreaterThan(0);
    });
});