
$(document).ready(function() {

   checkUrlForCode();

   function checkUrlForCode(){

        //returning from email link show menu
        if(location.search.indexOf('?code=') > -1){
             $('#loginModal').reveal();
             $("#login-container").hide();
            $("#menu-buttons").show();
        }
      
    } 

});