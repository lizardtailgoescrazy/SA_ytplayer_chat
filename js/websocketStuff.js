$(function () {
	"use strict";

	//Pre-identifying elements
	var content = $('#board');
	var input = $('#chatBox');
	//var status = $('#status');

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

	/*Create and get youtube API*/
	if (!window['YT']) {
		var YT = {};
	}
	if (!YT.Player) 
	{	
		(function(){
			var a = document.createElement('script');
			a.src = 'https:' + '//s.ytimg.com/yts/jsbin/www-widgetapi-vflEwv9hv.js';
			a.async = false;
			var b = document.getElementsByTagName('script')[0];
			b.parentNode.insertBefore(a, b);
		})();
	}

	//Set height of chat log box
	content.height(($(window).height())*0.63);

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

	if (checkForWebsockets() == false){
		canWebsocket = false;
		$("#board").html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. You will be unable to use the chat but the YouTube player will still work !</b></p>");
		$("#chatBox").hide();
		$("#board").attr('disabled', 'disabled');
	}
	else{
		connection = new WebSocket('ws://54.244.117.108:1337');
		//connection = new WebSocket('ws://arbiter:1337');

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
				logThis('This doesn\'t look like a valid JSON: ', message.data);
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
			else if (json.type === 'system') { 
				writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
				var temp = json.data.activeUsers;
				var activeUsers = temp.split(";");
				$("#activeUsers").empty();
				for (var i=0; i < activeUsers.length-1; i++) {
					logThis(activeUsers[i]);
					$("#activeUsers").append('<li><p>'+activeUsers[i]+'</p></li>');
				}
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
			else if (json.type === 'control') { 
				if(json.operation === 'skipToNext' && json.step === 'fin'){
					if(trimStuff(json.data.author) === trimStuff(currentDJ)){
						logThis("Skipping to next video");
						player.stopVideo();
						writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
						$.ajax({
							url: "next.php?cPlay="+currentlyPlaying,
							async: false,
							cache: false,
							success: function(response){
								//Do nothing
							}
						});
						if(canWebsocket){
							readForNext();
						}
						else{
							getNext = setInterval(readForNext, 1000);
						}
					}
				}
			}
			else {
				logThis('Incompatible JSON: ', json);
			}
		};
	}
});