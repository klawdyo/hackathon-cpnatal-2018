(function() {

  const btnNew = document.querySelector('button#add-new')
  btnNew.addEventListener('click', addNew)

  const form = document.querySelector('script#new-form')
  const listTemplate = _.template(form.innerText)

  const insertRef = document.querySelector('div.buttons')

  let cont = 0
  function addNew() {
    insertRef.insertAdjacentHTML("beforebegin", listTemplate({ index: cont }))
    cont++
  }

  addNew()

  function removeItem(id) {
    const itemToRemove = document.querySelector(`#form-data-${id}`)
    itemToRemove.parentNode.removeChild(itemToRemove);
  }

  window.removeItem = removeItem;
})()