import Vue from 'vue';
import Moment from 'moment';

// let socket = io('http://192.168.10.10:3000');
let socket = io('http://codedevise.com:3000');

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
	let timeago = new Date(value);
	timeago = Moment(timeago).fromNow();
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
		// socket setup
		socket.on('chat.message', function(message) {
			// add the message if it matches the any of the users conversations
			this.conversations.forEach(function(conversation) {
				if (conversation.id === message.conversation_id) {
					// play sms audio fx
					let audio = document.createElement('audio');
					audio.setAttribute('src', '/audio/sms.mp3');
					audio.play();
					audio = null;

					conversation.messages.push(message);
					// scroll message into view
					setTimeout(function() {
						document.querySelector('.Chatbox__content').scrollTop = 10000000;
					}, 50);
				}
			});

			// if (this.activeConversation.id === message.conversation_id) {
			// 	// play sms audio fx
			// 	let audio = document.createElement('audio');
			// 	audio.setAttribute('src', '/audio/sms.mp3');
			// 	audio.play();
			// 	audio = null;

			// 	this.activeConversation.messages.push(message);
			// 	// scroll message into view
			// 	setTimeout(function() {
			// 		document.querySelector('.Chatbox__content').scrollTop = 10000000;
			// 	}, 50);
			// }
		}.bind(this));

		this.$http.get('/api/v1/users', {api_token: this.apiToken}).then(function(response) {
			// set the users array
			this.users = response.data;

			// set the currently authenticated user
			this.user = this.users.filter(function(user) {
				return user.api_token === this.apiToken;
			}.bind(this))[0];

			// remove user from users
			this.users = this.users.filter(function(user) {
				return user.api_token !== this.apiToken;
			}.bind(this));

			// load the users conversations
			this.loadConversations();

		}, function(response) {
			// error callback
			console.log(response);
		});
	},

	events: {
		messageSent(response) {
			if (response.message) {
				console.log('message received', response.message);
				response.conversation.active = true;
				response.conversation.newMessage = true;
				response.conversation.lastMessage = this.lastMessage(response.conversation);
				console.log('response ...', response.conversation.lastMessage);
				this.activeConversation = response.conversation;

				// this.activeConversation.messages.push(response.message);
				// scroll message into view
				setTimeout(function() {
					document.querySelector('.Chatbox__content').scrollTop = 10000000;
				}, 50);
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
				this.activateConversation(conversation);
			}
		}
	},
	methods: {
		lastMessage(conversation) {
			// return the last message and the name of the sender or You said...
			let lastMessageSent = conversation.messages[conversation.messages.length -1];

			// if we don't have a message, just return
			if (lastMessageSent === undefined) return '';

			// otherwise see if it's from the current user or not
			return lastMessageSent.user.id === this.user.id ?
				'You said ' + lastMessageSent.body.substr(0, 25) + '...' :
				lastMessageSent.user.name + ' said ' + lastMessageSent.body.substr(0, 25) + '...'
		},
		activateConversation(conversation) {
			// reload the conversation and it's messages
			this.$http.get('/api/v1/conversations/' + conversation.id, {
				api_token: this.apiToken
			}).then(function(response) {
				conversation = response.data;
				// deactivate all conversations
				this.deactivateAllConversations();

				conversation.active = true;
				this.activeConversation = conversation;
				this.showChatbox = true;

				// get the conversations existing index in the collection
				const conversation_index = this.conversations.map(function(obj, index) {
					if (obj.id === conversation.id) {
						return index;
					}
				}).filter(isFinite);

				// remove from the conversations and re-add
				this.conversations = this.conversations.filter(function(convo) {
					return convo.id !== conversation.id;
				});
				this.conversations.splice(conversation_index, 0, conversation);

				// maximize chatbox
				document.querySelector('.Chatbox').classList.add('maximize');
				document.querySelector('.Chatbox').classList.remove('minimize');

				// show the Chatbox__content and Chatbox__input
				document.querySelector('.Chatbox__content').setAttribute('style', 'display:block');
				document.querySelector('.Chatbox__input').setAttribute('style', 'display:block');

				// scroll message into view
				setTimeout(function() {
					document.querySelector('.Chatbox__content').scrollTop = 10000000;
				}, 50);
			}, function(response){
				// error callback
				console.log('Failed to reload conversation', conversation);
			});
		},
		deactivateAllConversations() {
			this.conversations.forEach(function(conversation) {
				conversation.active = false;
			});
		},
		startConversation(sid, rid) {
			// create a new conversation
			this.$http.post('/api/v1/conversations', {
				api_token: this.apiToken, sender_id: sid, recipient_id: rid
			}).then(function(response) {
				// log the conversation id
				console.log('conversation: ' , response.data.conversation);

				// broadcast new conversation - causes a duplication bug...not needed in parent?
				// this.$dispatch('newConversation', response.data.conversation);

				// create a new chat window
				this.activeConversation = response.data.conversation;
				this.activateConversation(this.activeConversation);

			}, function(response) {
				// error callback
				console.log(response);
			});
		},
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