<template>
<div class="panel panel-default">
	<div class="panel-heading">Members Directory</div>
	<table class="table table-striped">
		<tbody>
			<tr v-for="member in users" transition="user-row" class="animated">
				<td>
					<img class="user-avatar" v-bind:src="member.avatar" alt="{{ member.name }}"/>
				</td>
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
	props: {
		users: {
			required: true,
			twoWay: true
		},

		user: {
			required: true,
			twoWay: true
		}
	},
	data() {
		return {
			apiToken: $('meta[name="api-token"]').attr('content'),
		};
	},
	ready() {

	},
	methods: {
		startConversation(sid, rid) {
			// create a new conversation
			this.$http.post('/api/v1/conversations', {
				api_token: this.apiToken, sender_id: sid, recipient_id: rid
			}).then(function(response) {
				// log the conversation id
				console.log('conversation: ' , response.data.conversation);

				// broadcast new conversation
				this.$dispatch('newConversation', response.data.conversation);

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