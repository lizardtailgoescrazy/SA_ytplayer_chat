$(function () {
	"use strict";

	//Extract GET variable from URL
	function getUrlVars() {
	    var vars = {};
	    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
	        vars[key] = value;
	    });
	    return vars;
	}

	//Pre-identifying elements
	var content = $('#board');
	var input = $('#chatBox');
	//var status = $('#status');

	// my color assigned by the server
	var myColor = false;

	//On incoming message
	connection.onmessage = function (message) {
		// Parse JSON object
		try {
			var json = JSON.parse(message.data);
		} 
		catch (e) {
			console.log('This doesn\'t look like a valid JSON: ', message.data);
			return;
		}

		if (json.type === 'color') { 
			// first response from the server with user's color
			myColor = json.data;
			//status.text(myName + ': ').css('color', myColor);
			input.removeAttr('disabled').focus();
			// from now user can start sending messages
		} 
		//History is unimplemented as of now
		//else if (json.type === 'history') { 
		//	for (var i=0; i < json.data.length; i++) {
		//		writeMessage(json.data[i].author, json.data[i].text,
		//			json.data[i].color, new Date(json.data[i].time));
		//	}
		//} 
		else if (json.type === 'message') { 
			// it's a single message
			input.removeAttr('disabled'); // let the user write another message
			writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
		} 
		else {
			console.log('Incompatible JSON: ', json);
		}
	};

	/**
	* Send mesage when user presses Enter key
	*/
	input.keydown(function(e) {
		if (e.keyCode === 13) {
			var msg = $(this).val();
			if(!msg){
				return;
			}
		// send the message as an ordinary text

			var prepedMsg = {
					    type: "message",
					    message: msg,
					};

			connection.send(JSON.stringify(prepedMsg));

			$(this).val('');
			// disable the input field to make the user wait until server
			// sends back response
			input.attr('disabled', 'disabled');
		}
	});

	/**
	* Add message to the chat window
	*/
	function writeMessage(author, message, color, dt) {
		var oldscrollHeight = content.attr("scrollHeight") - 20;
		content.append('<p><span style="color:' + color + '"><b>' + author + '</b></span> @ ' +
			+ (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
			+ (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes())
			+ ': ' + message + '</p>');
		var newscrollHeight = content.attr("scrollHeight") - 20;
		if(newscrollHeight > oldscrollHeight){
			content.animate({ scrollTop: newscrollHeight }, 'normal');
		}				
	}
});