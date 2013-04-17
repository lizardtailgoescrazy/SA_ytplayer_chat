var tempee = 1;
var player;
var currentlyPlaying;
var getNext;
var seek = -1;

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

$(document).ready(function(){
	$("#board").height(($(window).height())*0.63);
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
});

// Load the IFrame Player API code asynchronously.
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

// Replace the 'ytplayer' element with an <iframe> and
// YouTube player after the API code downloads.
function onYouTubePlayerAPIReady() {
	if(canWebsocket){
		console.log("Running dothings only once because websockets are enabled");
		doThings();
	}
	else{
		console.log("Running dothings on repeat because websockets are disabled");
		checkPlaylist = setInterval(doThings, 2000);
	}
	$("#playlistBuilder").html("Add URL to Playlist");
	$("#playlistBuilder").removeAttr("disabled");
	$("#searchButton").html("Search for Video");
	$("#searchButton").removeAttr("disabled");
	$("#URLAdd").removeAttr("disabled");
	
}

function readForNext(){
	console.log("This is the readForNext function");
	seek = -1;
	$.ajax({
		url: "read_file.php?mode=next",
		cache: false,
		success: function(response){
			if(response == "ERROR_1"){
				$("#message").html("Nothing to play yet...!");
				playlistState = "ERROR_1";
			}
			else if(response == "ERROR_2"){
				$("#message").html("Playlist finished, please add more videos...!");
				playlistState = "ERROR_2";
			}
			else{
				$("#message").html("");
				var bit = response.split(';');
				currentlyPlaying = bit[1];
				player.loadVideoById( currentlyPlaying, 0, "small");
				playlistState = "AYOK";
				if(canWebsocket == false){
					clearInterval(getNext);
				}
			}						
	  	},
	});
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
	else if(player.getPlayerState() == 3){
		if(seek > 0){
			player.seekTo(seek + 3, true);
			seek = -1;
		}
	}
}

function doThings(){
	console.log("This is the doThings function");
	$.ajax({
		url: "read_file.php?mode=entry",
		cache: false,
		async: false,
		success: function(response){
			if(response == "ERROR_1"){
				$("#message").html("Nothing to play yet...!");
				playlistState = "ERROR_1";
			}
			else if(response == "ERROR_2"){
				$("#message").html("Playlist finished, please add more videos...!");
				playlistState = "ERROR_2";
			}
			else{
				$("#message").html("");
				var bit = response.split(';');
				seek = parseInt(bit[0]);
				currentlyPlaying = bit[1];		
				player = new YT.Player('ytplayer', {
					   height: '200',
					   width: '325',
					   videoId: currentlyPlaying,
					   playerVars: {
					   	autoplay: '1',
					   	vq: 'small',
					   	controls: '0',
					   	iv_load_policy: '3',
					   	rel: '0'
				   	},
					events: {
					    	'onStateChange': onPlayerStateChange
							}
				}); 
				playlistState = "AYOK";
				if(canWebsocket == false){
					clearInterval(checkPlaylist);
				}
			}						
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
													connection.send(JSON.stringify(msg));
	                								$("#videoDetails").html("<p>You just added <b>"+title+"</b> to the playlist !</p>");

	                								$.ajax({
														url: "build.php?vid="+vID,
														cache: false,
														success: function(response){
														  	}
													});

	                							},
	                	error: function(data){
	                		console.log("youtube request failed with "+data);
	                	}
		        	});
			}
		}
    }
}

function searchThings(){
	var searchStuff = $("#searchStuff");
	if($("#searchStuff").css('display') == "none"){
		$("#searchStuff").css('display', "");
	}
	else{
		$("#searchStuff").css('display', "none");
		$("#searchStuff").html("");
	}
	return false;
}