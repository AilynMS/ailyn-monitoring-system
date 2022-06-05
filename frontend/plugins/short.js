import Vue from 'vue';

//  Max refers to the limit of characters to display
Vue.filter('short', function (val, max) {
    if(val.length <= max) return val; 
    else return `${val.substr(0, max).trim()}...`;
})