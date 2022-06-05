import { mount } from '@vue/test-utils'
import SearchBar from '@/components/shared/SearchBar.vue'

describe('Navigation', () => {
    it('Testing the navigation works correctly', () => {
        const wrapper = mount(SearchBar);
        
        expect(wrapper.vm.searchText).toBeNull();
    });
});