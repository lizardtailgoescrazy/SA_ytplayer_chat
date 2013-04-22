$(function () {
	"use strict";

	//Pre-identifying elements
	var content = $('#board');
	var input = $('#chatBox');
	//var status = $('#status');

	content.height(($(window).height())*0.63);

	if (checkForWebsockets() == false){
		canWebsocket = false;
		$("#board").html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. You will be unable to use the chat but the YouTube player will still work !</b></p>");
		$("#chatBox").hide();
		$("#board").attr('disabled', 'disabled');
	}
	else{
		connection = new WebSocket('ws://54.244.117.108:1337');

		connection.onopen = function () {
			//enable and clear chatbox
			$("#chatBox").val("");
			$("#chatBox").removeAttr('disabled');
			var msg = {
			    type: "setup",
			    id: getUrlVars()['id'],
			    username:   sessionUsername,
			    date: Date.now()
			};
			connection.send(JSON.stringify(msg));
		};

		connection.onerror = function (error) {
			$("#board").html("<p class='errorSpace'><b>Sorry, unable to contact the chat server.</b></p>");
			canWebsocket = false;
			if(!checkPlaylist){
				checkPlaylist = setInterval(doThings, 2000);
			}
		};

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
				//Removed this feature, no use, extra processing
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
			else if (json.type === 'ytplayer') { 
				if(playlistState === "ERROR_1" || playlistState === "ERROR_2"){
					if(player){
						//Player already intiated
						readForNext();
					}
					else{
						//Player un-intiated
						doThings();
					}
				}
				writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
			}
			else {
				console.log('Incompatible JSON: ', json);
			}
		};
	}
		
	//Check for submit of chatBox
	input.keydown(function(e) {
		if (e.keyCode === 13) {
			var msg = $(this).val();
			if(!msg){
				return;
			}
		
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

	//Recieve message from server
	function writeMessage(author, message, color, dt) {
		if(canWebsocket){
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
	}
});