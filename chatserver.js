var server = require('http').Server();
var io = require('socket.io')(server);
var Redis = require('ioredis');
var redis = new Redis();
// subscribe to chanel
redis.subscribe('chat-channel');

redis.on('message', function(channel, message) {
	console.log(channel, message);
	message = JSON.parse(message);
	io.emit(channel + ':' + message.event, message.data);
});

io.on('connection', function(socket) {
	console.log('new client connection');
	// io.emit('chat.message', 'User joined the chat');

	// message sent
	socket.on('chat.message', function(message) {
		// broadcast message to all listeners
		io.emit('chat.message', message);
	});

	// client disconnected
	socket.on('disconnect', function() {
		// io.emit('chat.message', 'User has disconnected');
		console.log('client disconnected');
	});
});
server.listen(3000);
console.log('Chat Server running on localhost:3000');