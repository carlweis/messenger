<template>
	<div class="Chatbox effect8" transition="chatbox" v-show="show">
		<header>
			<div class="title">
				<i class="fa fa-circle"></i>
				<span>{{ title }}</span>
			</div>
			<nav>
				<ul>
					<li>
						<a @click="minimize"><i class="fa fa-minus">&nbsp;</i></a>
					</li>
					<!-- <li>
						<a @click="maximize"><i class="fa fa-square-o">&nbsp;</i></a>
					</li> -->
					<li>
						<a @click="close"><i class="fa fa-close">&nbsp;</i></a>
					</li>
				</ul>
			</nav>
		</header>
		<div class="Chatbox__content">
			<li v-for="message in conversation.messages" track-by="$index" v-bind:class="message.user.id === user.id ? 'self' : 'other'">
			  <div class="avatar">
			    <img v-bind:src="message.user.avatar">
			  </div>
			  <div class="chatboxmessagecontent">
			    <p>{{ message.body }}</p>
			    <time datetime="{{ message.created_at }}" title="{{ message.created_at }}">
			      {{ message.user.name }} • {{ message.created_at | timeago }}
			    </time>
			  </div>

			</li>
		</div>
		<div class="Chatbox__input">
			<textarea v-model="message" @keyup.enter="send" placeholder="Enter a message..." autofocus="true"></textarea>
		</div>
	</div>
</template>

<script>
	// let socket = io('http://192.168.10.10:3000');
	let socket = io('http://codedevise.com:3000');
	export default {
		props: {
			conversation: {
				required: true,
				twoWay: true
			},
			show: {
				 type: Boolean,
				 required: true,
				 twoWay: true,
				 default: true
			},
			user: {
				require: true,
				twoWay: true
			}
		},
		data() {
			return {
				message: '',
				apiToken: $('meta[name="api-token"]').attr('content'),
			};
		},
		computed: {
			title() {
				if (this.conversation.sender !== undefined &&
					this.conversation.recipient !== undefined) {
					if (this.conversation.sender_id === this.user.id) {
						return this.conversation.recipient.name;
					} else {
						return this.conversation.sender.name;
					}
				}
				return '';
			}
		},
		methods: {
			send(e) {

				console.log('send event', e);
				if (this.message.trim().length > 0 && !e.shiftKey) {

					// replace newline in message with <br>
					this.message = this.message.replace(/[\r\n]/g, "\n");

					this.$http.post('/api/v1/messages', {
						api_token: this.apiToken,
						user_id: this.user.id,
						conversation_id: this.conversation.id,
						body: this.message
					}).then(function(response) {
						// push the message to the parent
						this.$dispatch('messageSent', {message: response.data.message, conversation: this.conversation});

						// emit the message to the chatserver
						socket.emit('chat.message', response.data.message);

						// clear the message input
						this.message = '';

						// scroll message into view
						setTimeout(function() {
							document.querySelector('.Chatbox__content').scrollTop = 10000000;
						}, 50);

					},function(response) {
						// error callback
						console.log('Failed to send message', response);
					}.bind(this));
				}
			},
			maximize() {
				// maximize chatbox
				document.querySelector('.Chatbox').classList.add('maximize');
				document.querySelector('.Chatbox').classList.remove('minimize');

				// show the Chatbox__content and Chatbox__input
				document.querySelector('.Chatbox__content').setAttribute('style', 'display:block');
				document.querySelector('.Chatbox__input').setAttribute('style', 'display:block');
			},
			minimize() {
				// toggle the minimize and maximize classes
				if (document.querySelector('.Chatbox').classList.contains('minimize')) {
					// maximize chatbox
					document.querySelector('.Chatbox').classList.add('maximize');
					document.querySelector('.Chatbox').classList.remove('minimize');

					// show the Chatbox__content and Chatbox__input
					document.querySelector('.Chatbox__content').setAttribute('style', 'display:block');
					document.querySelector('.Chatbox__input').setAttribute('style', 'display:block');
				} else {
					// minimize chatbox
					document.querySelector('.Chatbox').classList.remove('maximize');
					document.querySelector('.Chatbox').classList.add('minimize');

					// hide the Chatbox__content and Chatbox__input
					setTimeout(function() {
						document.querySelector('.Chatbox__content').setAttribute('style', 'visibility:hidden');
						document.querySelector('.Chatbox__input').setAttribute('style', 'visibility:hidden');
					}, 800);
				}
			},
			close() {
				// if chatbox is in mimimized state
				if (document.querySelector('.Chatbox').classList.contains('minimize')) {
					// remove any classes and close the chatbox
					document.querySelector('.Chatbox').classList.remove('maximize');
					document.querySelector('.Chatbox').classList.remove('minimize');
					document.querySelector('.Chatbox').setAttribute('style', 'display:none');
				} else if (document.querySelector('.Chatbox').classList.contains('maximize')) {
					document.querySelector('.Chatbox').classList.remove('maximize');
				}
				// hide the chatbox
				this.show = false;
			}
		}
	}
</script>

<style>
	.Chatbox {
		position: fixed;
		bottom: 0;
		margin-left: 1em;
		width: 280px;
		right: 350px;

		/* animated */
		-webkit-animation-duration: 0.5s;
	    animation-duration: 0.5s;
	    -webkit-animation-fill-mode: both;
	    animation-fill-mode: both;

		border: 1px solid rgba(29, 49, 91, .3);
		border-radius: 3px 3px 0 0;
    	/*box-shadow: 0 1px 1px rgba(0, 0, 0, .3);*/
	}

	.Chatbox__expanded {
		-webkit-transition: all .5s ease-in-out;
		transition: all .5s ease-in-out;
		transform: scale(1.4);
		width: 600px;
		height: 600px;
	}
	.Chatbox:nth-child(0) {
		right: 250px;
	}
	.Chatbox:nth-child(1) {
		right: 500px !important;
	}
	.Chatbox:nth-child(2) {
		right: 750px !important;
	}
	.Chatbox:nth-child(3) {
		right: 1000px !important;
	}
	.Chatbox header {
	    background-color: #436790;
	    color: white;
	    padding: 0.5rem;
	    overflow: hidden;
	    border-top: 1px solid rgb(62, 94, 131);
	    border-right: 1px solid rgb(62, 94, 131);
	    border-left: 1px solid rgb(62, 94, 131);
	}

	.Chatbox header nav  {
		position: absolute;
		right: 0.5em;
		top: 0.25em;
	}
	.Chatbox header nav ul {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	.Chatbox header nav ul li {
		display: inline-block;
		margin-left: 0.5em;
	}
	.Chatbox header nav ul li a {
		color: #fff;
		cursor: pointer;
	}
	.Chatbox header div.title {
		font-size: 1em;
    	font-weight: 700;
    	height: 20px;
	}
	.Chatbox header div.title i.fa {
		color: #5cb85c;
		font-size: smaller;
		margin-right: 0.5em;
		position: absolute;
		left: 0.5em;
		top: 0.75em;
		display: block;
	}
	.Chatbox header div.title span {
		position: absolute;
		left: 1.5em;
		top: 0.25em;
		display: block;
	}
	.Chatbox__content {
		font-family: arial, sans-serif;
	    /*height: 280px;
	    width: 280px;*/
	    height: 280px;
	    width: 100%;
	    overflow-y: auto;
	    padding: 7px;
	    background: #edf0f3;
	    line-height: 1.3em;
	    list-style: none;
	}

	.Chatbox__content li {
	    padding: 0.5rem;
	    overflow: hidden;
	    display: flex;
	}

	.Chatbox__content .avatar {
	    width: 40px;
	    position: relative;
	}

	.Chatbox__content .avatar img {
	    display: block;
	    width: 100%;
	}

	.other .avatar:after {
	    content: "";
	    position: absolute;
	    top: 0;
	    right: 0;
	    width: 0;
	    height: 0;
	    border: 5px solid white;
	    border-left-color: transparent;
	    border-bottom-color: transparent;
	}

	.self {
	    justify-content: flex-end;
	    align-items: flex-end;
	}

	.self .chatboxmessagecontent {
	    order: 1;
	    border-bottom-right-radius: 0;
	}

	.self .avatar {
	    order: 2;
	}

	.self .avatar:after {
	    content: "";
	    position: absolute;
	    bottom: 0;
	    left: 0;
	    width: 0;
	    height: 0;
	    border: 5px solid white;
	    border-right-color: transparent;
	    border-top-color: transparent;
	    box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
	}

	.chatboxmessagecontent {
	    background: white;
	    padding: 10px;
	    border-radius: 2px;
	    /*box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);*/
	}

	.chatboxmessagecontent p {
	    font-size: 12px;
	    margin: 0 0 0.2rem 0;
	    -ms-word-break: break-all;

	    /* Non standard for webkit */
	    word-break: break-word;

	    -webkit-hyphens: auto;
	    -moz-hyphens: auto;
	    hyphens: auto;
	}

	.chatboxmessagecontent time {
	    font-size: 9px;
	    color: #ccc;
	}

	.Chatbox__input {
    padding: 5px;
    background-color: #ffffff;
    border-top: 1px solid #cccccc;
    /*border-left: 1px solid #cccccc;

    border-bottom: 1px solid #cccccc;*/
}

.Chatbox__input textarea {
    width: 262px;
    height: 28px;
    padding: 3px 0pt 3px 3px;
    border: none;
    overflow: hidden;
    resize: none !important;
}

.Chatbox__input textarea:focus {
    border: none;
    margin: 0;
    outline: none;
}

@keyframes slideInUp {
  from {
    -webkit-transform: translate3d(0, 100%, 0);
    transform: translate3d(0, 100%, 0);
    visibility: visible;
  }

  to {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}

.chatbox-enter {
  -webkit-animation-name: slideInUp;
  animation-name: slideInUp;
}

@-webkit-keyframes slideOutDown {
  from {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }

  to {
    visibility: hidden;
    -webkit-transform: translate3d(0, 100%, 0);
    transform: translate3d(0, 100%, 0);
  }
}

@keyframes slideOutDown {
  from {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }

  to {
    visibility: hidden;
    -webkit-transform: translate3d(0, 100%, 0);
    transform: translate3d(0, 100%, 0);
  }
}

.chatbox-leave {
  -webkit-animation-name: slideOutDown;
  animation-name: slideOutDown;
}

/* minimize */
@-webkit-keyframes slideDown {
  from {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }

  to {
    visibility: visible;
    -webkit-transform: translate3d(0, 92%, 0);
    transform: translate3d(0, 92%, 0);
  }
}

@keyframes slideDown {
  from {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
  to {
    visibility: visible;
    -webkit-transform: translate3d(0, 92%, 0);
    transform: translate3d(0, 92%, 0);
  }
}
.minimize {
  -webkit-animation-name: slideDown;
  animation-name: slideDown;
}

/* maximize */
@-webkit-keyframes slideUp {
  from {
  	-webkit-transform: translate3d(0, 92%, 0);
    transform: translate3d(0, 92%, 0);
  }

  to {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}

@keyframes slideUp {
  from {
  	-webkit-transform: translate3d(0, 92%, 0);
    transform: translate3d(0, 92%, 0);

  }
  to {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}

.maximize {
  -webkit-animation-name: slideUp;
  animation-name: slideUp;
  /*transition: height 200ms;
   height: 372px;*/
}

/* flash  */
@-webkit-keyframes flash {
  from, 50%, to {
    opacity: 1;
  }

  25%, 75% {
    opacity: 0;
  }
}

@keyframes flash {
  from, 50%, to {
    opacity: 1;
  }

  25%, 75% {
    opacity: 0;
  }
}

.flash {
  -webkit-animation-name: flash;
  animation-name: flash;
}

</style>