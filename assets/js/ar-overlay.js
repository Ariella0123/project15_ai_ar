class AROverlay {
    constructor(container, cardElement) {
        this.container = container;
        this.card = cardElement;
        this.box = null;
    }

    render(boundingBox, data) {
        this.clear();

        const rect = this.container.getBoundingClientRect();
        const top = (boundingBox.ymin / 1000) * rect.height;
        const left = (boundingBox.xmin / 1000) * rect.width;
        const height = ((boundingBox.ymax - boundingBox.ymin) / 1000) * rect.height;
        const width = ((boundingBox.xmax - boundingBox.xmin) / 1000) * rect.width;

        this.box = document.createElement('div');
        this.box.className = 'ar-box';
        this.box.style.top = `${top}px`;
        this.box.style.left = `${left}px`;
        this.box.style.width = `${width}px`;
        this.box.style.height = `${height}px`;

        this.box.addEventListener('click', () => this.showCard(data));
        this.container.appendChild(this.box);

        this.showCard(data);
    }

    showCard(data) {
        this.card.innerHTML = `
            <h3>${data.productName}</h3>
            <p><strong>Manufacturer:</strong> ${data.manufacturer}</p>
            <p><strong>Spec:</strong> ${data.specification}</p>
            <p><strong>Description:</strong> ${data.description}</p>
            <p><strong>Confidence:</strong> ${(data.confidence * 100).toFixed(0)}% (${data.provider})</p>
        `;
        this.card.classList.add('active');
    }

    clear() {
        if (this.box) this.box.remove();
        this.card.classList.remove('active');
    }
}