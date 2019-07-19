/**
 * Classe que contém toda a logica javascript do painel administrativo.
 * 
 * Futuramente pretendo:
 *  Desacoplar o código em modulos
 *  Colocar um build do script minificado e uglificado
 */
const API_URL = "http://localhost:8080";
const URL = "http://localhost:8080/admin";
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
      // $("#form-login").submit((e) => Admin.index.submit(e));
      setTimeout(() => $("#msg-error").slideUp(), 3521);
    }
  }
}