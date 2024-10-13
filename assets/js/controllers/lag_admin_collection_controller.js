import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    targets() {
        return this.element.querySelectorAll(this.element.dataset.target)
    }

    addCollectionElement(event)
    {
        event.preventDefault()

        const item = document.createElement('div')
        item.innerHTML = this.element.dataset.prototypeValue.replace(/__name__/g, this.element.dataset.indexValue)
        this.targets().forEach(target => target.appendChild(item))
        this.element.dataset.indexValue = (parseInt(this.element.dataset.indexValue) + 1).toString()
    }

    removeCollectionElement(event) {
        event.preventDefault()

        const index = event.currentTarget.dataset.formCollectionIndex
        const item = this.element.querySelector('[data-form-collection="item"][data-form-collection-index="' + index + '"]')
        item.remove()
    }

    addTagFormDeleteLink(item) {
        const removeFormButton = document.createElement('button');
        removeFormButton.innerText = this.element.dataset.deleteLabel;
        removeFormButton.classList.add('btn', 'btn-danger');

        item.append(removeFormButton);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            item.remove();
        });
    }
};
