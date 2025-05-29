class ChatBot {
    constructor() {
        this.container = null;
        this.button = null;
        this.messagesContainer = null;
        this.input = null;
        this.sendButton = null;
        this.isOpen = false;
        this.isTyping = false;
        
        this.init();
    }
    
    init() {
        this.createChatButton();
        this.createChatContainer();
        this.addEventListeners();
    }
    
    createChatButton() {
        this.button = document.createElement('div');
        this.button.className = 'chat-bot-button';
        this.button.innerHTML = '<i class="las la-comment"></i>';
        document.body.appendChild(this.button);
    }
    
    createChatContainer() {
        this.container = document.createElement('div');
        this.container.className = 'chat-bot-container';
        
        const header = document.createElement('div');
        header.className = 'chat-bot-header';
        header.innerHTML = `
            <h3>Assistant Virtuel</h3>
            <div class="chat-bot-close"><i class="las la-times"></i></div>
        `;
        
        this.messagesContainer = document.createElement('div');
        this.messagesContainer.className = 'chat-bot-messages';
        
        const inputContainer = document.createElement('div');
        inputContainer.className = 'chat-bot-input-container';
        
        this.input = document.createElement('input');
        this.input.type = 'text';
        this.input.className = 'chat-bot-input';
        this.input.placeholder = 'Tapez votre message...';
        
        this.sendButton = document.createElement('button');
        this.sendButton.className = 'chat-bot-send';
        this.sendButton.innerHTML = '<i class="las la-paper-plane"></i>';
        this.sendButton.disabled = true;
        
        // Ajout d'un bouton pour envoyer des images
        this.imageButton = document.createElement('button');
        this.imageButton.className = 'chat-bot-image';
        this.imageButton.innerHTML = '<i class="las la-image"></i>';
        
        inputContainer.appendChild(this.imageButton);
        inputContainer.appendChild(this.input);
        inputContainer.appendChild(this.sendButton);
        
        this.container.appendChild(header);
        this.container.appendChild(this.messagesContainer);
        this.container.appendChild(inputContainer);
        
        document.body.appendChild(this.container);
    }
    
    addEventListeners() {
        this.button.addEventListener('click', () => this.toggleChat());
        this.container.querySelector('.chat-bot-close').addEventListener('click', () => this.toggleChat());
        
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
        
        this.input.addEventListener('input', () => {
            this.sendButton.disabled = this.input.value.trim() === '';
        });
        
        // Gestion de l'envoi d'images
        this.imageButton.addEventListener('click', () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = (e) => this.handleImageUpload(e.target.files[0]);
            input.click();
        });
        
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.container.contains(e.target) && e.target !== this.button) {
                this.toggleChat();
            }
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.container.classList.add('active');
            if (this.messagesContainer.children.length === 0) {
                this.addBotMessage("Bonjour ! Je suis votre assistant virtuel. Cherchez un produit ou posez-moi une question !");
            }
            setTimeout(() => this.input.focus(), 300);
        } else {
            this.container.classList.remove('active');
        }
    }
    
    addBotMessage(message, products = []) {
        this.showTypingAnimation();
        
        setTimeout(() => {
            this.hideTypingAnimation();
            
            const messageElement = document.createElement('div');
            messageElement.className = 'chat-bot-message bot';
            messageElement.textContent = message;
            
            this.messagesContainer.appendChild(messageElement);
            
            if (products.length > 0) {
                this.addProductCarousel(products);
            }
            
            this.scrollToBottom();
            this.isTyping = false;
        }, 1000 + Math.random() * 1000);
    }
    
    addUserMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'chat-bot-message user';
        messageElement.textContent = message;
        
        this.messagesContainer.appendChild(messageElement);
        this.scrollToBottom();
    }
    
    addUserImage(imageUrl) {
        const imageElement = document.createElement('div');
        imageElement.className = 'chat-bot-message user image';
        imageElement.innerHTML = `<img src="${imageUrl}" alt="User uploaded image" />`;
        
        this.messagesContainer.appendChild(imageElement);
        this.scrollToBottom();
    }
    
    showTypingAnimation() {
        this.isTyping = true;
        const typingElement = document.createElement('div');
        typingElement.className = 'chat-bot-typing';
        typingElement.innerHTML = `
            Bot Estuaire Achats est en train d'écrire
            <div class="typing-animation">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        
        this.messagesContainer.appendChild(typingElement);
        this.scrollToBottom();
    }
    
    hideTypingAnimation() {
        const typingElement = this.messagesContainer.querySelector('.chat-bot-typing');
        if (typingElement) typingElement.remove();
    }
    
    addProductCarousel(products) {
        const carousel = document.createElement('div');
        carousel.className = 'chat-bot-product-carousel';
        
        products.forEach(product => {
            const productElement = document.createElement('div');
            productElement.className = 'chat-bot-product';
            productElement.innerHTML = `
                <img src="${product.image}" alt="${product.name}" />
                <h4>${product.name}</h4>
                <p>${product.price} €</p>
                <a href="${product.url}" target="_blank">Voir le produit</a>
            `;
            carousel.appendChild(productElement);
        });
        
        this.messagesContainer.appendChild(carousel);
        this.scrollToBottom();
    }
    
    async sendMessage() {
        const message = this.input.value.trim();
        if (message === '') return;
        
        this.addUserMessage(message);
        this.input.value = '';
        this.sendButton.disabled = true;
        
        try {
            const response = await fetch('/api/v2/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'System-Key': 'AjoutezVotreCleSecreteTresComplexeIci123!@'
                },
                body: JSON.stringify({ message })
            });
            
            const data = await response.json();
            this.addBotMessage(data.message, data.products);
        } catch (error) {
            this.addBotMessage("Désolé, une erreur est survenue. Réessayez plus tard.");
        }
    }
    
    async handleImageUpload(file) {
        const formData = new FormData();
        formData.append('image', file);
        
        try {
            const response = await fetch('/api/v2/chatbot/upload-image', {
                method: 'POST',
                headers: {
                    'System-Key': 'AjoutezVotreCleSecreteTresComplexeIci123!@'
                },
                body: formData
            });
            
            const data = await response.json();
            this.addUserImage(data.imageUrl);
            this.addBotMessage("Merci pour l'image ! Je ne peux pas encore analyser les images, mais je peux répondre à vos questions sur les produits.");
        } catch (error) {
            this.addBotMessage("Erreur lors du téléchargement de l'image. Réessayez.");
        }
    }
    
    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new ChatBot();
});