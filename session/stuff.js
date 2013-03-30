var tempee = 1;
var checkPlaylist;
var player;
var currentlyPlaying;
var getNext;
var seek = -1;

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

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



// Load the IFrame Player API code asynchronously.
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
// Replace the 'ytplayer' element with an <iframe> and
// YouTube player after the API code downloads.
function onYouTubePlayerAPIReady() {
	checkPlaylist = setInterval(doThings, 2000);
}

function readForNext(){
	seek = -1;
	$.ajax({
		url: "read_file.php?mode=next",
		cache: false,
		success: function(response){
			if(response == "ERROR_1"){
					$("#message").html("Nothing to play yet...!");
			}
			else if(response == "ERROR_2"){
				$("#message").html("Playlist finished, please add more videos...!");
			}
			else{
				$("#message").html("");
			//Beginning
				var bit = response.split(';');
				currentlyPlaying = bit[1];
				player.loadVideoById( currentlyPlaying, 0, "small");
				clearInterval(getNext);
			}
			//End							
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
						//alert(response);
				  	},
			});

		getNext = setInterval(readForNext, 1000);
	}
	else if(player.getPlayerState() == 3){
		if(seek > 0){
			player.seekTo(seek + 3, true);
			seek = -1;
		}
	}
}

function doThings(){		
		$.ajax({
			url: "read_file.php?mode=entry",
			cache: false,
			async: false,
			success: function(response){
				if(response == "ERROR_1"){
					$("#message").html("Nothing to play yet...!");
				}
				else if(response == "ERROR_2"){
					$("#message").html("Playlist finished, please add more videos...!");
				}
				else{
					$("#message").html("");
					var bit = response.split(';');
					seek = parseInt(bit[0]);
					currentlyPlaying = bit[1];
					//Beginning
					
					player = new YT.Player('ytplayer', {
						   height: '320',
						   width: '520',
						   videoId: currentlyPlaying,
						   playerVars: {
						   	autoplay: '1',
						   	vq: 'small',
						   	//controls: '0',
						   	rel: '0'
					   	},
						events: {
						    	'onStateChange': onPlayerStateChange
								}
					}); 
					clearInterval(checkPlaylist);
				}
				//End							
		  	},
		});
	}


function addThings(){
	var URLtoAdd = $("#URLAdd").val();
	$("#URLAdd").val("");
	var temp = URLtoAdd.split("?");
	var alsoTemp = temp[1].split("&");
	for(var n=0; n<alsoTemp.length; n++){
		if(alsoTemp[n][0] == 'v'&& alsoTemp[n][1] == '='){
			var vID = alsoTemp[n].substring(2, 13);
			//$("#temp").html($("#temp").html()+vID+"<br>");
			$.ajax({
				url: "build.php?vid="+vID,
				cache: false,
				success: function(response){
				  	},
			});
			break;
		}
	}
		
}