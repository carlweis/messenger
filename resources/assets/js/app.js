import Vue from 'vue';

// import mixins

// import components
import UserDirectory from './components/UserDirectory.vue';
import Conversations from './components/Conversations.vue';

// vue transitons
Vue.transition('user-row', {
	enterClass: 'slideInUp',
	leaveClass: 'slideOutDown'
});

// use vue resource
Vue.use(require('vue-resource'));
Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
let authKey = $('meta[name="api-token"]').attr('content');

Vue.http.headers.common['Authorization'] = authKey;

new Vue({
	el: 'body',

	components: { UserDirectory, Conversations },

	data: {
		users: []
	},

	ready() {

	}
});