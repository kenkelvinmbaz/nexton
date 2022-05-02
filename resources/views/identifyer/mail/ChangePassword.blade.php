
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Create New Password</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="shortcut icon" type="image/x-icon" href="http://www.nuvenspay.com/images/nuvenspay2.png" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <link type="text/css" rel="stylesheet" href="{{asset('css/changepassword.css') }}">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<div class="container">
	<header>
		<h1>
			<a href="www.nuvenspay.com">
				<img src="http://www.nuvenspay.com/images/nuvenspay2.png" alt="Authentic Collection">
			</a>
		</h1>
	</header>
    <div id="show" style="display: none;"></div>
	<h1 class="text-center">Créer mot de passe</h1>
    <form id="send_validation" method="post" class="content" class="registration-form">
		<label>
			<span class="label-text">Email</span>
            <input type="email" name="email" id="email" value="{{$email}}" required>
		</label>
	
		<label class="password">
			<span class="label-text">Mot de passe</span>
			<button class="toggle-visibility" title="toggle password visibility" tabindex="-1">
				<span class="glyphicon glyphicon-eye-close"></span>
			</button>
			<input type="password" name="password" id="password" minlength="6" required>
		</label>
	
        <div class="mt-2">
            <button type="button"  onclick="validation()" class="btn btn-dark btn-block" data-loading-text="Loading..." id="btnFetch"> CONFIRMER <span class="fa fa-check-circle" ></span></button>
        </div>
        <div class="loading" style="display: none;">
            <button class="btn btn-dark btn-block">
                <i class="fa fa-spinner fa-spin"></i> Attendez un instant...
              </button>
        </div>
	</form>
</div>

<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>

<script>

$(window).load(function() {
        $(".loader").fadeOut(3000);
});

$('form input').on('keypress', function(e) {
    return e.which !== 13;
});

$("#btnFetch").click(function() {
    $("#btnFetch").hide();

    $(".loading").show().delay(1000).fadeOut();

    setTimeout(function(){
        $("#btnFetch").show();// or fade, css display however you'd like.
    }, 1000);
     
});

     function validation()
      {
              $.ajax({
                  type        : 'post',
                  url         : '../../reset/password',
                  data        : $('#send_validation').serialize(),
                  dataType    : "text",
                  processData : false,

                  success: function (response) {
                      $('#show').html(response);                                                
                  },
                  statusCode: {
                        402: function(xhr) {
                            swal("ERREUR!", "Vous devez tapez votre mot de passe, avec plus de 6 caractères. Votre mot de passe doit contenir une lettre en majuscule,lettre en miniscule,un caractère et un chiffre, pour garantir la sécurité de votre compte.","error", {button: "Reprendre",});
                        } ,

                        201: function(xhr){
                            swal("Validé!","Votre mot de passe a été actualisé! Veuillez vous connecter sur votre compte avec le nouveau mot de passe informé","success", {button: "Fermer",});
                            $("#send_validation")[0].reset();
                        }
                    }  
            
            
            });
               
      }
</script>