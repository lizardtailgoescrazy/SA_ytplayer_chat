
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
											socket.emit('ytplayer',JSON.stringify(msg));
										}
        							}
    		});
}

function searchThings(){
	var searchStuff = $("#searchStuff");
	//var overlayDisplay = '';
	if($("#searchStuff").css('display') == "none"){
		$("#searchStuff").css('display', "");
		$("#searchTerm").focus();


		$("#overlayClose").click(function(){
			$("#searchResultDiv").html("");
			$("#searchTerm").val("");
			$("#searchStuff").css('display', "none");
		});
		
		//On click handler for search button
		$("#searchThis").ready(function(){
			$("#searchResultDiv").html("");
			$("#searchTerm").val("");
			$("#searchResultDiv").focus();
			
			$("#searchThis").click(function(){
				var toAdd = $("#searchTerm").val();
				toAdd = trimStuff(toAdd);
				if(toAdd == ""){
					return false;
				}
				else{
					if (validateURL(toAdd) == false) {
						//Not youtube URL but keyword
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
										//$("#searchStuff").html("");
										addThingsFromSearch($(this).attr('destination'));
										$("#searchResultDiv").html("");
										$("#searchTerm").val("");
									});
								}						
							},
						});
					} 
					else{
						//Valid youtube URL
						$("#searchStuff").css('display', "none");
						$("#searchTerm").val("");
						var temp = toAdd.split("?");
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
																	$("#videoDetails").html("<p>You just added <b>"+title+"</b> to the playlist !</p>");

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
							}
						}
					}
				}
			});
		});

		//Auto complete handler for textbox
		$("#searchTerm").ready(function(){
			logThis("Applying autocomplete to #searchTerm");
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
					//val = searchTerm.val();
					//searchTerm.autocomplete( "search", val );
					//searchTerm.blur();
				}
			});
		});	
	}
	else{
		//Do nothing
	}
	return false;
}