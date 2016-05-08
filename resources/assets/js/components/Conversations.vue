<template>
	<div class="Conversations" v-show="show" transition="conversations">
		<!-- <header>
			<div>
				Conversations
				<nav>
					<ul>
						<li><i @click="show = false" class="fa fa-close">&nbsp;</i></li>
					</ul>
				</nav>
			</div>

		</header> -->
		<ul class="list">
			<li v-for="conversation in conversations" @click="toggleActive(conversation)" class="{{ isSelected ? 'active' : '' }}">
				<div v-if="conversation.sender_id === user.id">
					<img v-bind:src="conversation.recipient.avatar" alt="{{ conversation.recipient.name }}"/>
					{{ conversation.recipient.name }}
					<span class="status">
						<i class="fa fa-circle">&nbsp;</i>
					</span>
				</div>
				<div v-else>
					<img v-bind:src="conversation.sender.avatar" alt="{{ conversation.sender.name }}"/>
					{{ conversation.sender.name }}
					<span class="status">
						<i class="fa fa-circle">&nbsp;</i>
					</span>
				</div>
			</li>
		</ul>
	</div>
</template>

<script>
export default {
	props: {
		conversations: {
			require: true,
			twoWay: true
		},
		show: {
			 type: Boolean,
			 required: true,
			 twoWay: true
		},
		user: {
			required: true
		}
	},
	data() {
		return {
			active: false,
			apiToken: $('meta[name="api-token"]').attr('content')
		};
	},
	ready() {

	},
	computed: {
		isSelected() {
			return this.active;
		}
	},
	methods: {
		toggleActive(conversation) {
			// reload the conversation and it's messages
			this.$http.get('/api/v1/conversations/' + conversation.id, {
				api_token: this.apiToken
			}).then(function(response) {
				conversation = response.data;
				conversation.active = ! conversation.active;
				this.$dispatch('activateConversation', conversation);
				this.active = !this.active;

				// scroll message into view
				setTimeout(function() {
					document.querySelector('.Chatbox__content').scrollTop = 10000000;
				}, 50);
			}, function(response){
				// error callback
				console.log('Failed to reload conversation', conversation);
			});
		}
	}
}
</script>

<style>
	.Conversations {
		position: fixed;
		right: 0;
		top: 58px;
		z-index: 0;
		height: 100%;
		min-height: 100%;
		background: #fff;
		/*background: #e5e5e5;*/
		/*min-width: 25em;*/
		min-width: 235px;

		/* animated */
		-webkit-animation-duration: 0.5s;
	    animation-duration: 0.5s;
	    -webkit-animation-fill-mode: both;
	    animation-fill-mode: both;

	    border-left: solid 1px #ccc;
	}

	.Conversations header {
		background: #273a6e;
		color: #fff;
		position: relative;
		clear: both;
		padding: 0.25em 0;
	}

	.Conversations header div {
		position: relative;
		padding-left: 1em;
		font-weight: 500;
	}

	.Conversations header div nav {
		position: absolute;
		right: 0.25em;
		top: 0;
	}

	.Conversations header div nav li  {
		cursor: pointer;
	}

	.Conversations ul {
		list-style: none;
		margin: 0;
		padding: 0;
	}

	.Conversations ul.list li {
		position: relative;
		padding: 0.5em 1em;
		border-bottom: solid 1px #ddd;
		cursor: pointer;
	}
	.Conversations ul.list li:hover {
		background: rgba(214, 213, 213, 0.67);
	}

	.Conversations ul li img {
		width: 25px;
		height: 25px;
		margin-right: 1em;
		border: solid 1px #ddd;
	}

	.Conversations ul li span {
		position: absolute;
		right: 1em;
		top: 1em;
		font-size: smaller;
	}
	.Conversations ul li span i {
		color: #5cb85c;
	}
	@-webkit-keyframes slideInRight {
    from {
        -webkit-transform: translate3d(100%, 0, 0);
        transform: translate3d(100%, 0, 0);
        visibility: visible;
    }

    to {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }
}

@keyframes slideInRight {
    from {
        -webkit-transform: translate3d(100%, 0, 0);
        transform: translate3d(100%, 0, 0);
        visibility: visible;
    }

    to {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }
}
.conversations-enter {
	-webkit-animation-name: slideInRight;
    animation-name: slideInRight;
}

@-webkit-keyframes slideOutRight {
    from {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }

    to {
        visibility: hidden;
        -webkit-transform: translate3d(100%, 0, 0);
        transform: translate3d(100%, 0, 0);
    }
}

@keyframes slideOutRight {
    from {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }

    to {
        visibility: hidden;
        -webkit-transform: translate3d(100%, 0, 0);
        transform: translate3d(100%, 0, 0);
    }
}

.conversations-leave {
	-webkit-animation-name: slideOutRight;
    animation-name: slideOutRight;
}
</style>
