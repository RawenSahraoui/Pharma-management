// Point of Sale JavaScript
console.log('POS loaded');

document.addEventListener('DOMContentLoaded', function() {
    // POS functionality
    const barcodeInput = document.getElementById('barcode');
    const cartItems = [];
    
    if (barcodeInput) {
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const barcode = this.value;
                console.log('Scanning:', barcode);
                // Add to cart logic here
                this.value = '';
            }
        });
    }
    
    // Calculator functionality
    const calcButtons = document.querySelectorAll('.calc-btn');
    calcButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Calc:', this.textContent);
        });
    });
});