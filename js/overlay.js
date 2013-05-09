
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
										if(canWebsocket){
											connection.send(JSON.stringify(msg));
										}
        								$("#videoDetails").html("<p>You just added <b>"+title+"</b> to the playlist !</p>");
        							}
    		});
}

function searchThings(){
	var searchStuff = $("#searchStuff");
	var overlayDisplay = "<div id=\"container\"><div class=\"jumbotron\">"+
	"<input type=\"text\" placeholder=\"Search for youtube video...\" id=\"searchTerm\" />    <button class=\"btn\"id=\"searchThis\" >Search</button><hr><div id=\"searchResultDiv\"></div><br><p id=\"overlayClose\"> [x] Close search</p></div></div>";
	if($("#searchStuff").css('display') == "none"){
		$("#searchStuff").css('display', "");
		$("#searchStuff").html(overlayDisplay);
		$("#overlayClose").click(function(){
			$("#searchStuff").css('display', "none");
			$("#searchStuff").html("");
		});

		//On click handler for search button
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
								addThingsFromSearch($(this).prop('destination'));
							});
						}						
					},
				});
			});
		});

		//Auto complete handler for textbox
		$("#searchTerm").ready(function(){
			var searchTerm = $("#searchTerm");
			searchTerm.autocomplete({ 
				source: function(request, response) {
					$.ajax({
						url: 'http://query.yahooapis.com/v1/public/streaming/yql',
						dataType: 'JSONP',
						data: {
							format: 'json',
							q: 'select * from xml where url="http://google.com/complete/search?hl=nl&output=toolbar&q=' + encodeURIComponent(request.term) + '"'
						},
						success: function(data) {
							if (typeof data == 'string') data = $.parseJSON(data);
							response($.map(data.query.results.toplevel.CompleteSuggestion, function(item) {
								return { label: item.suggestion.data, value: item.suggestion.data };
							}));
						}
					});
				},
				open: function(){
					doSearch($('.ui-autocomplete li:first-child a').text(), true, false);
					$(".ui-autocomplete :first-child a").addClass("ui-state-hover");
					searchTerm.focus();
					return false;
				},
				select: function(e, ui){
					searchTerm.autocomplete('search', ui.item.value);
				},
				close : function (event, ui) {
					val = searchTerm.val();
					searchTerm.autocomplete( "search", val );
					searchTerm.blur();
				}
			});
		});	
	}
	else{
		//Do nothing
	}
	return false;
}