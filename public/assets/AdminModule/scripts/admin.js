/**
 * Classe que contém toda a logica javascript do painel administrativo.
 * 
 * Futuramente pretendo:
 *  Desacoplar o código em modulos
 *  Colocar um build do script minificado e uglificado
 */
const API_URL = "http://localhost:8080";
const URL = "http://127.0.0.1:8080/admin";
const Admin = {
  /**
   * Mostra a mensagem de erro
   */
  hiddenError: ( ) => {
    let elements = document.getElementsByClassName('message');
    
    Array.from( elements ).map( element => {
      element.style.display = 'none';
    });
  },
  /**
   * Scripts da página index
   */
  index: {
    /**
     *  Inicia tela de login
     */
    init: () => {
      setTimeout(() => Admin.hiddenError(), 3541);
    }
  },
  manage: {
    fillForm: (preObject) => {
      
      let form = document.getElementById("create-form");
      
      try {
        preObject = JSON.parse(preObject);
      }catch(e){
        throw e;
      }

      let attributes = Object.keys( preObject );

      attributes.map( attribute => {
  
        let input = form.querySelector('#' + attribute);
        if ( input ){
          input.value = preObject[attribute];
        }
      
      });

    },
    toggleForm: () => {
      
      let form = document.getElementById("create-form");
      
      if ( form.style.top != '0px' || form.style.top == '' ){
        form.style.top = '0';
      }else{
        form.style.top = '-100vh';
      }
    },
    commit: ( rowID, modelName ) => {
      let body = Admin.manage._mapRowAttributes(rowID);
      
      $.ajax({
        url: URL + '/crud/' + modelName,
        method: 'PUT',
        data: body,
        success: (data) => {
          Admin.manage.enableCommit(rowID, false);
        }
      });
      
    },
    delete: (rowID, modelName) => {
      let body = Admin.manage._mapRowAttributes( rowID );

      $.ajax({
        url: URL + '/crud/' + modelName,
        method: 'DELETE',
        data: body,
        success: (data) => {
          document.getElementById(rowID).remove();
        }
      });

    },
    /** 
     * Cria um objeto com os valores da linha
     * @param {int} rowID ID da Linha
     * @return {mixed} Linha em forma de objeto
     */
    _mapRowAttributes: (rowID) => {
      let row = document.getElementById(rowID);
      let genericObject = {};

      Array.from(row.childNodes).map(element => {
        if( typeof element.id === 'string' ){
          if ( typeof element.id.split('-')[1] !== 'undefined' ){
            genericObject[ element.id.split('-')[1] ] = element.innerText
          }
        }
      })
      
      return genericObject;
    },
    /**
     * Habilita o botão de salvar
     */
    enableCommit: (rowID, state = true) => {
      let row = document.getElementById(rowID);
      let button = row.querySelector("#save");
      
      // button.addEventListener('click', commit)
      
      button.disabled = !state;

    },
    /** 
     * Cancela todos os campos de edição 
     */
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
    /**
     * Inicia uma nova edição de um campo
     */
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