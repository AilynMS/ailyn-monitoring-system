import { mount } from '@vue/test-utils'
import AuthBox from '@/components/auth/AuthBox.vue'

describe('AuthBox', () => {
    it('A title and text button arrives', () => {
        const wrapper = mount(AuthBox, {
            propsData: {
                title: 'Hello',
                submitText: 'I am a text'
            }
        });

        expect(wrapper.props().title).toBe('Hello');
        expect(wrapper.props().submitText).toBe('I am a text');
    });
});