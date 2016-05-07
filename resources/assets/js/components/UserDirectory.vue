<template>
<div class="panel panel-default">
	<div class="panel-heading">Members Directory</div>
	<table class="table table-striped">
		<tbody>
			<tr v-for="member in users" transition="user-row" class="animated">
				<td>{{ member.id }}</td>
				<td>{{ member.name }}</td>
				<td>{{ member.email }}</td>
				<td>
					<a @click="startConversation(user.id, member.id)"  data-sid="{{ user.id }}" data-rid="{{ member.id }}"
						class="btn btn-success btn-xs">
						Start Conversation
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</template>

<script>
export default {
	data() {
		return {
			user: {},
			users: [],
			apiToken: $('meta[name="api-token"]').attr('content')
		};
	},
	ready() {
		this.$http.get('/api/v1/users', {api_token: this.apiToken}).then(function(response) {
			// set the users array
			this.users = response.data;

			// set the currently authenticated user
			this.user = this.users.filter(function(user) {
				return user.api_token == this.apiToken;
			}.bind(this))[0];

		}, function(response) {
			// error callback
			console.log(response);
		});
	},
	methods: {
		startConversation(sid, rid) {
			// create a new conversation
			this.$http.post('/api/v1/conversations', {
				api_token: this.apiToken, sender_id: sid, recipient_id: rid
			}).then(function(response) {
				// log the conversation id
				console.log('conversation: ' , response.data.conversation_id);
				// create a new chat window

			}, function(response) {
				// error callback
				console.log(response);
			});
		}
	}
};
</script>
<style>
	.user-row {
		opacity: 0;
	}
	.user-row-transition {
		transition: all 0.5s ease;
	}
	.user-row-enter {
		opacity: 1;
	}
	.user-row-leave {
		opacity: 0;
	}
</style>