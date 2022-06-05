import { mount } from '@vue/test-utils'
import Notification from '@/components/shared/Notification.vue'

describe('Notification test', () => {
    it('If valid props', () => {
        const wrapper = mount(Notification, {
            propsData: {
                classes: 'mt-2',
                errorTitle: 'I am a error title',
                errorText: 'a error',
                showCloseButton: true
            }
        });

        expect(wrapper.props().classes).toBe('mt-2');
        expect(wrapper.props().errorTitle).toBe('I am a error title');
        expect(wrapper.props().errorText).toBe('a error');
        expect(wrapper.props().showCloseButton).toBeTruthy();
    });
});