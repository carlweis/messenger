import Vue from 'vue';
import Moment from 'moment';

// import mixins

// import components
import UserDirectory from './components/UserDirectory.vue';
import Conversations from './components/Conversations.vue';
import Chatbox from './components/Chatbox.vue';

// vue transitons
Vue.transition('user-row', {
	enterClass: 'slideInUp',
	leaveClass: 'slideOutDown'
});

// vue filters
Vue.filter('timeago', function(value, input) {
	console.log(value);
	let timeago = new Date(value);
	timeago = Moment(timeago).fromNow();
	console.log('timeago', timeago);
	return timeago;
});

// use vue resource
Vue.use(require('vue-resource'));
Vue.http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

new Vue({
	el: 'body',

	components: { UserDirectory, Conversations, Chatbox },

	data: {
		apiToken: $('meta[name="api-token"]').attr('content'),
		user: {},
		users: [],
		conversations: [],
		activeConversation: {},
		showConversations: true,
		showChatbox: false,
	},

	ready() {
		this.$http.get('/api/v1/users', {api_token: this.apiToken}).then(function(response) {
			// set the users array
			this.users = response.data;

			// set the currently authenticated user
			this.user = this.users.filter(function(user) {
				return user.api_token == this.apiToken;
			}.bind(this))[0];

			// load the users conversations
			this.loadConversations();

		}, function(response) {
			// error callback
			console.log(response);
		});
	},

	events: {
		messageSent(message) {
			if (message) {
				console.log('message received', message);
				this.activeConversation.messages.push(message);
				console.log('Message added', this.activeConversation.messages);
			}
		},
		newConversation(conversation) {
			if (conversation) {
				console.log(conversation);
				this.conversations.push(conversation);
			}
		},
		activateConversation(conversation) {
			if (conversation) {
				conversation.active = true;
				this.activeConversation = conversation;
				this.showChatbox = true;

				// toggle minimize

				// maximize chatbox
				document.querySelector('.Chatbox').classList.add('maximize');
				document.querySelector('.Chatbox').classList.remove('minimize');

				// show the Chatbox__content and Chatbox__input
				document.querySelector('.Chatbox__content').setAttribute('style', 'display:block');
				document.querySelector('.Chatbox__input').setAttribute('style', 'display:block');
			}
		}
	},
	methods: {
		loadConversations() {
			this.$http.get('/api/v1/conversations', {api_token: this.apiToken}).then(function(response) {
			// add the conversations
			this.conversations = response.data;

			// set the active conversation
			this.activeConversation = this.conversations[0];

			}, function(response) {
				// error callback
				console.log(response);
			}.bind(this));
		},
		toggleConversations() {
			this.showConversations = !this.showConversations;
		}
	}
});