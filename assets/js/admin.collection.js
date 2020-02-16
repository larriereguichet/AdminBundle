const widgets = document.querySelectorAll('.collection-widget');

widgets.forEach((widget) => {
    let collection = widget.querySelector('.collection-container');
    collection.dataset.index = collection.querySelectorAll('.form-group').length.toString();
    
    bindAdd();
    bindRemove(collection);
    
    function bindAdd() {
        let addButton = widget.querySelector('.add-button');
        addButton.addEventListener('click', (event) => {
            let prototype = collection.dataset.prototype;
            let removeButton = collection.dataset.removeButton;
        
            let index = collection.dataset.index;
            let newForm = prototype;
        
            newForm = newForm.replace(/__name__label__/g, index);
            newForm = newForm.replace(/__name__/g, index);
        
            let element = document.createElement('div');
            element.innerHTML = newForm + removeButton;
            element.classList.add('item-container');
        
            collection.dataset.index = (parseInt(index) + 1).toString();
            collection.appendChild(element);
    
            bindAdd();
            bindRemove(collection);
        
            event.preventDefault();
        });
    
    }
    
    function bindRemove(collection) {
        collection.querySelectorAll('.item-container').forEach((element) => {
            element.querySelectorAll('.remove-link').forEach((button) => {
                button.addEventListener('click', (event) => {
                    element.remove();
                    collection.dataset.index = (parseInt(collection.dataset.index) - 1).toString();
                    event.stopPropagation();
                })
            });
        });
    }
});
