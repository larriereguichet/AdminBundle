export default class Collapse {
    constructor(element, options) {
        this.element = element;
        this.options = Object.assign({
            toggleClass: 'collapse',
        }, options || {});
    }
    
    bind() {
        this.element.addEventListener('click', (event) => {
            event.preventDefault();
            document.querySelectorAll(this.element.hash).forEach((target) => {
                target.classList.toggle(this.options.toggleClass);
                let text = this.element.text;
                
                if (this.element.dataset.alternateIcon) {
                    icon = '';
                }
    
    
                let icon = this.element.querySelector('i');
    
                this.element.text = this.element.dataset.alternateText;
                this.element.dataset.alternateText = text;
            });
        });
    }
}
