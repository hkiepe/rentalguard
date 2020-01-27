<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta http-equiv="content-type" content="text/html; carset=utf-8">
        <title>Rent Vehcile</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" rel="stylesheet">
        <link href="hide-show-fields-form.css" rel="stylesheet"/>
    </head>
    <body>
        
            <button id= "Reply-open-button-1" class="t-button t-state-default reply-button Reply-open-button-1" value="1">Reply</button>
            <button id= "Alert-open-button-1" class="Alert-open-button-1" value="1">Alert</button>

            <div style="display: none" class="Reply-div-1 div-1">
                <p>Reply</p>
            </div>

            <div style="display: none" class="Alert-div-1 div-1">
                <p> Alert - TBC</p>
            </div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script type="text/javascript">
$(".Reply-open-button-1").click(function () {
    $("div.Reply-div-1").show("slow");
    
    $("div.Alert-div-1").hide("slow");
    $("div.Close-div-1").hide("slow");
});

$(".Alert-open-button-1").click(function () {
    $("div.Alert-div-1").show("slow");
    
    $("div.Reply-div-1").hide("slow");
    $("div.Close-div-1").hide("slow");
});
        </script>
    </body>    
</html>