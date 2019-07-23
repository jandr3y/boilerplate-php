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
      setTimeout(() => $("#msg-error").slideUp(), 3521);
    }
  },
  manage: {
    enableCommit: (rowID) => {
      let row = document.getElementById(rowID);
      row.querySelector("#save").disabled = false;
    },
    /** Cancela todos os campos de edição */
    cancelEdit: (e) => {
      let row = e.path[2];
      let childs = Array.from(row.childNodes).filter( element => typeof element.localName !== 'undefined' && element.localName == 'td' )
      
      childs.forEach( cell => {
        if ( typeof cell.firstChild.value !== 'undefined' ){
          e.target.removeEventListener('focusout', Admin.manage.cancelEdit);
          cell.innerHTML = cell.firstChild.value;
        }
      })

      Admin.manage.enableCommit( row.id );
    },
    editField: (field, value, cellID) => {
      if ( field != 'id' ) {
        let cell = document.getElementById(cellID);
        
        let textField = document.createElement("input");
        
        textField.setAttribute('type', 'text');
        textField.setAttribute('placeholder', field);
        textField.classList.add('form-control');
        textField.value = value;
        textField.addEventListener('focusout', Admin.manage.cancelEdit)
        
        cell.innerHTML = '';
        cell.appendChild(textField);
        textField.focus();
      }
    }
  }
}