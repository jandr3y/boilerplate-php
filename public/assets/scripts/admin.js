/**
 * Classe que contém toda a logica javascript do painel administrativo.
 * 
 * Futuramente pretendo:
 *  Desacoplar o código em modulos
 *  Colocar um build do script minificado e uglificado
 */
const API_URL = "http://localhost:8080";
const Admin = {
  /**
   * Mostra a mensagem de erro
   */
  errorHandler: ( error ) => {
    $("#msg-error").html( error.responseJSON.error )
    $("#msg-error").slideDown();
    setTimeout(() => $("#msg-error").slideUp(), 3521);
  },
  /**
   * Scripts da página index
   */
  index: {
    /**
     *  Inicia tela de login
     */
    init: () => {
      $("#form-login").submit((e) => Admin.index.submit(e));
    },
    /**
     * Envia o Login
     */
    submit: ( e ) => {

      e.preventDefault();
      
      let data = $("#form-login").serializeArray();
      
      $.ajax({
        url: API_URL + "/auth",
        method: "POST",
        dataType: 'json',
        contentType: "application/json;charset=UTF-8",
        data: JSON.stringify({
          username: data[0].value,
          password: data[1].value
        }),
        success: (data) => {
          if ( data.token ) {
            sessionStorage.setItem( "token", data.token );
          }
        },
        error: (error) => {
          Admin.errorHandler( error );
          document.getElementById("form-login").reset();
        }
      });
      
    }
  }
}