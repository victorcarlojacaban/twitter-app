require('./bootstrap');

window.Vue = require('vue');

//Events
let Event = new Vue()
window.Event = Event;

Vue.component('timeline-component', require('./components/TimelineComponent.vue').default);
Vue.component('profile-component', require('./components/ProfileComponent.vue').default);

const app = new Vue({
    el: '#app',
});
