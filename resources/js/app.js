document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('[data-menu-toggle]');

    menuToggle?.addEventListener('click', () => {
        document.body.classList.toggle('menu-open');
    });

    document.addEventListener('click', (event) => {
        if (document.body.classList.contains('menu-open') && event.target instanceof Element) {
            if (event.target === document.body || !event.target.closest('.sidebar') || event.target.closest('.side-nav a')) {
                document.body.classList.remove('menu-open');
            }
        }
    });

    const orderForm = document.querySelector('[data-order-form]');

    if (!orderForm) {
        return;
    }

    const bouquetOptions = [...orderForm.querySelectorAll('input[name="bouquet_id"]')];
    const quantityInput = orderForm.querySelector('[data-bouquet-quantity]');
    const flowerInputs = [...orderForm.querySelectorAll('.flower-quantity input')];
    const bouquetName = orderForm.querySelector('[data-selected-bouquet]');
    const quantityLabel = orderForm.querySelector('[data-summary-quantity]');
    const totalLabel = orderForm.querySelector('[data-summary-total]');

    const updateSummary = () => {
        const selected = bouquetOptions.find((option) => option.checked);
        const quantity = Math.max(1, Number(quantityInput?.value || 1));
        const flowerTotal = flowerInputs.reduce((total, input) => total + Math.max(0, Number(input.value || 0)) * 15000, 0);
        const total = (selected ? Number(selected.dataset.price) * quantity : 0) + flowerTotal;

        if (selected && bouquetName) {
            bouquetName.textContent = selected.closest('.bouquet-choice')?.querySelector('.choice-main strong')?.textContent || 'Rangkaian pilihan';
        }

        if (quantityLabel) {
            quantityLabel.textContent = String(quantity);
        }

        if (totalLabel) {
            totalLabel.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        }
    };

    orderForm.addEventListener('input', updateSummary);
    orderForm.addEventListener('change', updateSummary);
    updateSummary();
});
