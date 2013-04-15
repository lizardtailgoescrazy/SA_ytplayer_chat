
function addThingsFromSearch(vid){
	if(trimStuff(vid) == "" || vid.length != 11){
		return false;
	}
	$.ajax({
		url: "build.php?vid="+vid,
		cache: false,
		success: function(response){
			//do nothing
		  	}
	});
	var title = "";
    $.ajax({
            url: "http://gdata.youtube.com/feeds/api/videos/"+vid+"?v=2&alt=json",
            dataType: "jsonp",
            success: function (data){ 
            							title = data.entry.title.$t;
										var msg = {
										    type: "ytplayer",
										    name: title
										};
										connection.send(JSON.stringify(msg));
        								$("#videoDetails").html("<p>You just added <b>"+title+"</b> to the playlist !</p>");
        							}
    		});
}

function searchThings(){
	var searchStuff = $("#searchStuff");
	var overlayDisplay = "<div id=\"container\"><div class=\"jumbotron\">"+
	"<input type=\"text\" placeholder=\"Search for youtube video...\" id=\"searchTerm\" /><br><button class=\"btn\"id=\"searchThis\" >Search for video</button><div id=\"searchResultDiv\"></div><br><p id=\"overlayClose\"> [x] Close search</p></div></div>";
	if($("#searchStuff").css('display') == "none"){
		$("#searchStuff").css('display', "");
		$("#searchStuff").html(overlayDisplay);
		$("#overlayClose").click(function(){
			$("#searchStuff").css('display', "none");
			$("#searchStuff").html("");
		});
		$("#searchThis").ready(function(){
			$("#searchThis").click(function(){
				$.ajax({
					url: "yt.php?q="+$("#searchTerm").val(),
					cache: false,
					async: false,
					success: function(response){
						if(response == ""){
							$("#searchResultDiv").html("No results found...!");
						}
						else{
							$("#searchResultDiv").html(response);
							$(".result").click(function(){
								$("#searchStuff").css('display', "none");
								$("#searchStuff").html("");
								addThingsFromSearch($(this).attr('destination'));
							});
						}						
					},
				});
			});
		});
	}
	else{
		//Do nothing
	}
	return false;
}
