var player;
var currentlyPlaying;
var getNext;
var seek = -1;
var currentDJ = null;

/******************************************
	browser events
*******************************************/
window.onbeforeunload = function (e) {
  	var message = "Do you really want to exit the chat session ? You will be logged out automatically.",
  	e = e || window.event;
  	// For IE and Firefox
  	if (e) {
    	e.returnValue = message;
  	}
  	// For Safari
  	return message;
};

$(window).unload(function() {
	//Client leaving
	$.ajax({
		url: "departure.php",
		cache: false,
		async: false,
		success: function(response){
			//Do notihng
	  	}
	});
});

$(window).resize(function() {
	logThis("The windows was resized !");
	prepYtplayerDiv();
	blockYtplayer();
});

/******************************************
	youtube api events
*******************************************/
function onPlayerReady() {
	logThis("Youtube player is ready !");
}

function onYouTubePlayerAPIReady() {
	logThis("Yes, youtube API ready.");
	//Activate player controls
	$("#searchButton").html("Add video to queue");
	$("#searchButton").removeAttr("disabled");

	if(canWebsocket){
		doThings();
	}
	else{
		checkPlaylist = setInterval(doThings, 2000);
	}
}

function onPlayerStateChange(newState) {
	//alert(player.getPlayerState());
	if ( player.getPlayerState() == 0 ) {
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
	else if(player.getPlayerState() == 1){
		logThis("State 1 ot a seek value of " + seek);
		if(seek > 0){
			player.seekTo(seek + 3, true);
			seek = -1;
		}
	}
}

function prepYtplayerDiv(){
	var divPlayer = $("#ytplayer");
	var w = divPlayer.width();
	var h = ((9/16)*w);
	divPlayer.height(h);
}

function blockYtplayer(){
	var bubble = $("#bubble");
	var divPlayer = $("#ytplayer");
	var posYtplayer = divPlayer.position();

	bubble.css({
	    "position":"absolute", 
	    "top": posYtplayer.top + "px",
	    "left": posYtplayer.left + "px",
	});
	bubble.width(divPlayer.width());
	bubble.height(divPlayer.height());
}

function makeControlsLive(){
	/*Make controls live*/
	$("#vol_up").click( function(){
		if(player){
			var currentVol = player.getVolume();
			if((currentVol+10) <= 100){
				player.setVolume(currentVol+10);
				$("#vol_value").text((currentVol+10)+"%");
				$("#vol_mute").text(" mute ");
			}
		}
	});

	$("#vol_down").click( function(){
		if(player){
			var currentVol = player.getVolume();
			if((currentVol-10) >= 0){
				player.setVolume(currentVol-10);
				$("#vol_value").text((currentVol-10)+"%");
			}
		}
	});

	$("#vol_mute").click( function(){
		if(player){
			if(player.isMuted()){
				$("#vol_mute").text(" mute ");
				player.unMute();
			}
			else{
				$("#vol_mute").text(" unmute ");
				player.mute();
			}
		}
	});

	$("#exportPl").click(function(){
		if(playlistState != "ERROR_1"){
			window.open("exportPl.php");
		}
	});
}

/******************************************
	playlist events
*******************************************/
function amITheDJ(){
	logThis("Checking if I'm the DJ");
	if(trimStuff(sessionUsername) === trimStuff(currentDJ)){
		logThis("Yes, its me !");
		$("#ifImTheDJ").removeProp("disabled");
	}
	else{
		$("#ifImTheDJ").prop("disabled",true);
	}
}

function skipThis(){
	if(trimStuff(sessionUsername) === trimStuff(currentDJ)){
		logThis("Init-ing the process to skip this video");
		var msg = {
		    type: "control",
		    operation: "skipToNext",
		    step: "init"
		};
		if(canWebsocket){
			socket.emit('control',JSON.stringify(msg));
		}
	}
}

function getInQueue(){
	$.ajax({
		url: "siq.php",
		cache: false,
		async: false,
		success: function(response){
			setInQueue(response);
		}
		
	});
}

function doThings(){
	logThis("This is the doThings function");
	$.ajax({
		url: "read_file.php?mode=entry",
		cache: false,
		async: false,
		success: function(response){
			if(response == "ERROR_1"){
				$("#message").html("Nothing to play, add a video to the playlist...!");
				playlistState = "ERROR_1";
			}
			else if(response == "ERROR_2"){
				$("#message").html("Playlist finished, please add more videos...!");
				playlistState = "ERROR_2";
			}
			else{
				var bit = response.split(';');
				seek = parseInt(bit[0]);
				logThis("Got a seek value of " + seek);
				currentlyPlaying = bit[1];
				currentDJ = bit[2];		
				logThis("Fetching video title...!");
				$.ajax({
				    url: "http://gdata.youtube.com/feeds/api/videos/"+currentlyPlaying+"?v=2&alt=json",
				    dataType: "jsonp",
				    success: function (data){
				    	$("#message").html("&#9658; " + data.entry.title.$t);
						setCurrentDJ(currentDJ);
						},
					error: function(data){
						logThis("youtube request failed with "+data);
					}
				});
				var h = ($("#ytplayer").width()*(9/16));
				player = new YT.Player('ytplayer', {
					   height: h,
					   videoId: currentlyPlaying,
					   playerVars: {
					   	wmode: 'opaque',
					   	autoplay: '1',
					   	vq: 'small',
					   	controls: '0',
					   	iv_load_policy: '3',
					   	rel: '0'
				   	},
					events: {
							'onReady': onPlayerReady,
					    	'onStateChange': onPlayerStateChange
							}
				});
				makeControlsLive();
				playlistState = "AYOK";
				if(canWebsocket == false){
					clearInterval(checkPlaylist);
				}
				amITheDJ();
				blockYtplayer();
				getInQueue();
			}						
	  	},
	});
}

function readForNext(){
	logThis("This is the readForNext function");
	seek = -1;
	$.ajax({
		url: "read_file.php?mode=next",
		cache: false,
		success: function(response){
			if(response == "ERROR_1"){
				$("#message").html("Nothing to play yet...!");
				playlistState = "ERROR_1";
				currentDJ = null;
				setCurrentDJ(" --- ");
			}
			else if(response == "ERROR_2"){
				$("#message").html("Playlist finished, please add more videos...!");
				playlistState = "ERROR_2";
				currentDJ = null;
				setCurrentDJ(" --- ");
			}
			else{
				//$("#message").html("");
				var bit = response.split(';');
				currentlyPlaying = bit[1];
				currentDJ = bit[2];
				logThis("Fetching video title...!");
				$.ajax({
				    url: "http://gdata.youtube.com/feeds/api/videos/"+currentlyPlaying+"?v=2&alt=json",
				    dataType: "jsonp",
				    success: function (data){
				    	$("#message").html("&#9658; " + data.entry.title.$t);
						setCurrentDJ(currentDJ);
					},
					error: function(data){
						logThis("youtube request failed with "+data);
					}
				});
				player.loadVideoById( currentlyPlaying, 0, "small");
				playlistState = "AYOK";
				if(canWebsocket == false){
					clearInterval(getNext);
				}
			}
			amITheDJ();
			getInQueue();
	  	},
	});
}

function addThings(){
	var URLtoAdd = $("#URLAdd").val();
	$("#URLAdd").val("");
	if(trimStuff(URLtoAdd) == ""){
		return false;
	}

	if (validateURL(URLtoAdd) == false) {
		//Not a valid youtube URL
		//NEED TO implement soundcloud
	    $("#videoDetails").html("<h4>**This is not a valid youtube URL !</h4>");
    } 
    else{
    	//Valid youtube URL
    	var temp = URLtoAdd.split("?");
		var alsoTemp = temp[1].split("&");
		for(var n=0; n<alsoTemp.length; n++){
			if(alsoTemp[n][0] == 'v'&& alsoTemp[n][1] == '='){
				var vID = alsoTemp[n].substring(2, 13);
				var title = "";
		        $.ajax({
		                url: "http://gdata.youtube.com/feeds/api/videos/"+vID+"?v=2&alt=json",
		                dataType: "jsonp",
		                success: function (data){ 
		                							title = data.entry.title.$t;
        											var msg = {
													    type: "ytplayer",
													    name: title
													};
													if(canWebsocket){
														socket.emit('ytplayer',JSON.stringify(msg));
													}
	                								$.ajax({
														url: "build.php?vid="+vID,
														cache: false,
														success: function(response){
														  	}
													});

	                							},
	                	error: function(data){
	                		logThis("youtube request failed with "+data);
	                	}
		        	});
		        setTimeout(function() {
				  $("#videoDetails").fadeOut().empty();
				}, 5000);
			}
		}
    }
}