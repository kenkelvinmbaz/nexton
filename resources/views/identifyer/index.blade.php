<!DOCTYPE html>
<html lang="en">
<head>
  <title>ID Identification</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link type="text/css" rel="stylesheet" href="{{asset('css/background_blue.css') }}">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
  </script>
</head>
<body>

<div class="container" style="margin-top: -50px;">
  <div id="show" style="display: none;"></div>
  <form  id="send_validation"  role="form" method="POST" enctype="multipart/form-data">
    <div align="center" class="mt-5">
        <img src="https://image.freepik.com/vecteurs-libre/illustration-concept-fille-millenaire_114360-3341.jpg" class="image-size"width="350" >
    </div>
    <div align="center">
        <p class="texto-size text-center">Votre carte d'identité est très importante pour vous identifier 
          lors du retrait dans l'une de nos agences se trouvant à proximité de vous. Vous pouvez aussi enregistrer l'un des documents suivants: <b>Passeport, Carte d'électeur
        ou encore Permis de conduire.</b></p>
    </div>

   
    <label>Carte d'identité recto</label>
    <div align="center" >
      <input type="file" name="identity_cardFace" id="identity_cardFace" class="form-control" required>
    </div><br>

    <label>Carte d'identité verso</label>
    <div align="center" >
      <input type="file" name="identity_cardBack" id="identity_cardBack" class="form-control" required>
    </div>

    <div align="center" class="mt-3">
      <input type="text" name="email" id="email" class="form-control" placeholder="confirmer votre e-mail" required>
    </div>
    
  
    <div class="mt-3">
      <button type="button" onclick="validation()" class="btn btn-outline-primary btn-block"  data-loading-text="Loading..." id="btnFetch">CONFIRMER <i class="fa fa-check-circle"></i></button>
    </div>
    <div class="loading" style="display: none;">
      <button class="btn btn-dark btn-block">
          <i class="fa fa-spinner fa-spin"></i> Veuillez patienter un instant...
        </button>
  </div>
  <div class="footer">
    <p></p>
  </div>
  </form>
</div>

<script>
// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var identity_card_face = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(identity_card_face);
});
</script>

<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
$('form input').on('keypress', function(e) {
    return e.which !== 13;
});

$("#btnFetch").click(function() {
 
        $("#btnFetch").hide();
        $(".loading").show().delay(10000).fadeOut();
        setTimeout(function(){
            $("#btnFetch").show();// or fade, css display however you'd like.
        }, 10000);
     
});

function validation()
      {
              $.ajax({
                  type        : 'post',
                  url         : 'http://127.0.0.1:8000/api/save/idcard',
                  data        : new FormData($('#send_validation')[0]),
                  dataType    : "text",
                  dataType:'json',
                async:false,
                processData: false,
                contentType: false,

                  success: function (response) {
                      $('#show').html(response);                                                
                  },
                  statusCode: {
                        401: function(xhr) {
                            $(".loading").show().delay(2000).fadeOut();
                            $("#btnFetch").show().delay(2000).fadeIn();
                           
                                swal("ERREUR!", "Carte d'identité non enregistrée! Placez une image du recto et verso de votre carte d'identité et insérez votre e-mail pour valider l'opération.","error", {button: "Reprendre",});
                        } ,

                        402: function(xhr) {
                            $(".loading").show().delay(2000).fadeOut();
                            $("#btnFetch").show().delay(2000).fadeIn();
                           
                                swal("ERREUR!", "E-mail non reconnu! L'adresse mail que vous avez fourni n'existe pas dans notre base de données.","error", {button: "Reprendre",});
                        } ,

                        201: function(xhr){
                          
                            swal("Validé!","Votre photo de profil a été actualisée. Pour retourner à la page précédente, veuillez cliquer sur le bouton <-- situé à la partie supérieure de votre écran","success", {button: "Fermer",});
                            $("#send_validation")[0].reset();
                        }
                    }  
            
            
            });
               
      }
</script>
</body>
</html>
