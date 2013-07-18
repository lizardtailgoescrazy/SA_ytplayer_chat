function markElementsForError(){
	logThis("Could not connect to the server.");
	content.html("<p class='errorSpace'><b>Sorry, unable to contact the chat server. Chat features will not work but the youtube features still work ! YAY !</b></p>");
	input.html("Connection error.");
}

function properJson(data){
	logThis(data);
	// Parse JSON object
	try {
		var json = JSON.parse(data);
	} 
	catch (e) {
		return false;
	}
	return json;
}


$(function () {
	"use strict";

	//Pre-identifying elements
	var content = $('#board');
	var input = $('#chatBox');
	//var status = $('#status');

	//Recieve message from server
	function writeMessage(author, message, color, dt) {
		if(canWebsocket){
			var oldscrollHeight = content.prop("scrollHeight") - 20;
			content.append('<p><span style="color:' + color + '"><b>' + author + '</b></span> @ ' +
				+ (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
				+ (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes())
				+ ': ' + message + '</p>');
			var newscrollHeight = content.prop("scrollHeight") - 20;
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
			a.src = 'http://www.youtube.com/player_api';
			a.async = false;
			var b = document.getElementsByTagName('script')[0];
			b.parentNode.insertBefore(a, b);
		})();
	}

	//Set height of chat log box
	content.height(($(window).height())*0.63);
	$("#activePpl").css("max-height", content.height());
	
	$("#ytplayer").ready(function(){
		prepYtplayerDiv();
	});

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

			socket.emit('message',JSON.stringify(prepedMsg));
			$(this).val('');
			// disable the input field to make the user wait until server
			// sends back response
			input.prop('disabled', 'disabled');
		}
	});

	if (checkForWebsockets() == false){
		canWebsocket = false;
		content.html("<p class='errorSpace'><b>Sorry, your browser does not support websockets. You will be unable to use the chat but the YouTube player will still work !</b></p>");
		input.hide();
		content.prop('disabled', 'disabled');
	}
	else{

		socket = io.connect('http://ec2-54-213-34-40.us-west-2.compute.amazonaws.com:8080');
		//socket = io.connect('http://arbiter:1337');


		socket.on('connect', function () {
            //enable and clear chatbox
			$("#chatBox").val("");
			$("#chatBox").removeAttr('disabled');
			var msg = {
			    type: "setup",
			    id: getUrlVars()['id'],
			    username:   sessionUsername,
			    date: Date.now()
			};
			socket.emit('setup', JSON.stringify(msg));
        });

        socket.on('error', function (data) {
           markElementsForError();
			canWebsocket = false;
			if(!checkPlaylist){
				checkPlaylist = setInterval(doThings, 2000);
			}
        });

        socket.on('connect_failed', function (data) {
            markElementsForError();
			canWebsocket = false;
			if(!checkPlaylist){
				checkPlaylist = setInterval(doThings, 2000);
			}
        });


        socket.on('message', function(data){
        	var json = properJson(data);
        	// it's a single message
				input.removeAttr('disabled'); // let the user write another message
				writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));

        });

        socket.on('system', function(data){
        	var json = properJson(data);
        	logThis(json);
        	writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
				setFooterMessage(json.data.text);
				var temp = json.data.activeUsers;
				var activeUsers = temp.split(";");
				$("#activeUsers").empty();
				for (var i=0; i < activeUsers.length-1; i++) {
					logThis(activeUsers[i]);
					$("#activeUsers").append('<li><i class="icon-user"></i <p>'+activeUsers[i]+'</p></li>');
				}
        });

        socket.on('control', function(data){
        	var json = properJson(data);
        	if(json.operation === 'skipToNext' && json.step === 'fin'){
					if(trimStuff(json.data.author) === trimStuff(currentDJ)){
						logThis("Skipping to next video");
						player.stopVideo();
						writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
						setFooterMessage(json.data.text);
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
        });

        socket.on('ytplayer', function(data){
        	var json = properJson(data);
        	writeMessage(json.data.author, json.data.text,json.data.color, new Date(json.data.time));
				setFooterMessage(json.data.text);
				getInQueue();
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
        });
	}
});