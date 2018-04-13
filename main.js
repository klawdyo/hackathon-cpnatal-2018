(function() {

  const btnNew = document.querySelector('button#add-new')
  btnNew.addEventListener('click', addNew)

  const form = document.querySelector('script#new-form')
  const listTemplate = _.template(form.innerText)

  const insertRef = document.querySelector('div.buttons')

  let cont = 0
  function addNew() {
    insertRef.insertAdjacentHTML("beforebegin", listTemplate({ index: cont }))
    enableFields(cont);
    cont++
  }

  addNew()

  function removeItem(id) {
    const itemToRemove = document.querySelector(`#form-data-${id}`)
    itemToRemove.parentNode.removeChild(itemToRemove);
  }

  function enableFields( id ){
    const value = document.querySelector( `#type-${id}` ).value;
    
    const slot = document.querySelector( `#slot-${id}` ); 
    const diameter = document.querySelector( `#diameter-${id}` );
    const materialName = document.querySelector( `#material-name-${id}` );
    const backgroundColor = document.querySelector( `#background-color-${id}` );

    slot.setAttribute( 'disabled', true );
    diameter.setAttribute( 'disabled', true );
    materialName.setAttribute('disabled', true);
    backgroundColor.setAttribute('disabled', true);

    switch( value ){
        case 'filter': 
            backgroundColor.removeAttribute('disabled');
            materialName.removeAttribute('disabled');
            slot.removeAttribute('disabled');
        break;
        case 'diameter': 
            diameter.removeAttribute('disabled');
        break;
        case 'coating':
        case 'lythologic':
        case 'complementary':
            backgroundColor.removeAttribute('disabled');
            materialName.removeAttribute('disabled');
        break;
    }
  }

  window.removeItem = removeItem;
  window.enableFields = enableFields;
})()