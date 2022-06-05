import { mount } from '@vue/test-utils'
import LoadingEffect from '@/components/shared/LoadingEffect.vue'

describe('LoadingEffect test', () => {
    it('If LoadingEffect is not empty', () => {
        const wrapper = mount(LoadingEffect);
        const div = wrapper.find('div');
        expect(div.exists()).toBeTruthy();
    });
});