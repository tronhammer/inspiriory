<!DOCTYPE html>
<!--[if lte IE 6]><html class="preIE7 preIE8 preIE9"><![endif]-->
<!--[if IE 7]><html class="preIE8 preIE9"><![endif]-->
<!--[if IE 8]><html class="preIE9"><![endif]-->
<!--[if gte IE 9]><!--><html><!--<![endif]-->
	<head>
		<meta charset="UTF-8">
		<title>title</title>
		<meta name="author" content="tronnet">
		<meta name="description" content="description here">
		<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
		<link rel="stylesheet" href="stylesheet.css" type="text/css">
		<style type="text/css">

		</style>
	</head>
	<body>
		
		<input name="question[name]" id="question-name" placeholder="Name this question..."/>
		<input name="question[body]" id="question-body" placeholder="Ask a question..."/>
		<button type="submit" id="ask-question">Ask</button>
			
		<div id="questions">
			
		</div>
		
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			window.inspiriory = {
				heartbeat: function(){
					$.ajax({
						url: "http://inspiriory.com/api/index.php",
						dataType: "json",
						data: {
							action: "questions"
						}
					}).done(window.inspiriory.updates);
				},
				updates: function(data){
					var questions = data.data;
					var $container = $("#questions");
					for(var i=0;i<data.data.length;i++){
						var id = "q-"+questions[i].id;
						if (!$("#"+id).length){
							$container.append( $("<div/>").addClass("question-container").attr("id", id).data("id", questions[i].id).text( questions[i].name ) );
						}
					}
				}
			};
			
			$(function ()
			{
				window.inspiriory.heartbeat();
				setInterval(window.inspiriory.heartbeat, 5000);

				
				
				$("#ask-question").bind("click", function(e){
					$.ajax({
						url: "http://inspiriory.com/api/index.php",
						method: "POST",
						dataType: "json",
						data: {
							"action": "create_question",
							"name": $("#question-name").val(),
							"body": $("#question-body").val(),
						}
					}).done(function(resp){
						console.log("SUCCESS!");
					}).fail(function(resp){
						console.log("FAIL!");
					}).always(function(resp){
						console.log(resp);
					});
				});
				
				$("#questions").delegate(".question-container", "click", function(e){
					var _this = $(this);
					$.ajax({
						url: "http://inspiriory.com/api/index.php",
						method: "POST",
						dataType: "json",
						data: {
							"action": "delete_question",
							"id": $(this).data("id"),
						}
					}).done(function(resp){
						console.log("SUCCESS!");
						_this.remove();
						
					}).fail(function(resp){
						console.log("FAIL!");
					}).always(function(resp){
						console.log(resp);
					});
				})
			})
		</script>
	</body>
</html>