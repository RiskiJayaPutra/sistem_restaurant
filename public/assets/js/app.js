/**
 * RestoReserve — Client-Side Vanilla JS
 *
 * Mengatur progressive enhancement, micro-interactions,
 * form submit loading indicator, dinamisasi baris order,
 * dan kalkulasi preview subtotal/total di frontend.
 *
 * CATATAN PENTING:
 * Seluruh kalkulasi harga dan otorisasi tetap divalidasi dan
 * dihitung ulang secara mutlak di sisi SERVER.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ------------------------------------------------------------
    // 1. Flash Alert Close Handling
    // ------------------------------------------------------------
    const flashAlerts = document.querySelectorAll('[data-flash-alert]');
    flashAlerts.forEach(alert => {
        const closeBtn = alert.querySelector('[data-flash-close]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                alert.style.transition = 'opacity 200ms ease-out, transform 200ms ease-out';
                setTimeout(() => alert.remove(), 210);
            });
        }
    });

    // ------------------------------------------------------------
    // 2. Submit Button Loading State
    // ------------------------------------------------------------
    const formsWithLoading = document.querySelectorAll('form[data-loading]');
    formsWithLoading.forEach(form => {
        form.addEventListener('submit', (e) => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                // Biarkan event submit berjalan ke server, update visual tombol
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    const originalText = submitBtn.dataset.originalText || submitBtn.textContent.trim();
                    submitBtn.dataset.originalText = originalText;
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Memproses...
                    `;
                }, 10);
            }
        });
    });

    // ------------------------------------------------------------
    // 3. Konfirmasi Dialog Hapus / Batal
    // ------------------------------------------------------------
    const confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ------------------------------------------------------------
    // 4. Mobile Navigation Drawer Toggle
    // ------------------------------------------------------------
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileBackdrop = document.getElementById('mobile-backdrop');

    if (mobileMenuBtn && mobileMenu) {
        const toggleMenu = () => {
            const isOpen = !mobileMenu.classList.contains('hidden');
            if (isOpen) {
                mobileMenu.classList.add('hidden');
                if (mobileBackdrop) mobileBackdrop.classList.add('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            } else {
                mobileMenu.classList.remove('hidden');
                if (mobileBackdrop) mobileBackdrop.classList.remove('hidden');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
            }
        };

        mobileMenuBtn.addEventListener('click', toggleMenu);
        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', toggleMenu);
        }
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                toggleMenu();
            }
        });
    }

    // ------------------------------------------------------------
    // 5. Dynamic Order Form Lines & Real-Time Calculation
    // ------------------------------------------------------------
    const orderItemsContainer = document.getElementById('order-items-container');
    const addMenuItemBtn = document.getElementById('add-menu-item-btn');
    const totalPreviewEl = document.getElementById('order-total-preview');
    const menuTemplate = document.getElementById('menu-item-row-template');

    if (orderItemsContainer && totalPreviewEl) {
        const formatRupiah = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(amount);
        };

        const calculateTotals = () => {
            let total = 0;
            const rows = orderItemsContainer.querySelectorAll('.order-item-row');

            rows.forEach(row => {
                const select = row.querySelector('.menu-select');
                const qtyInput = row.querySelector('.qty-input');
                const subtotalEl = row.querySelector('.subtotal-preview');

                const price = select && select.selectedOptions[0]
                    ? parseInt(select.selectedOptions[0].dataset.price || '0', 10)
                    : 0;

                const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value || '1', 10)) : 1;
                const subtotal = price * qty;
                total += subtotal;

                if (subtotalEl) {
                    subtotalEl.textContent = formatRupiah(subtotal);
                }
            });

            totalPreviewEl.textContent = formatRupiah(total);

            // Highlight border sejenak saat angka total berubah
            totalPreviewEl.classList.add('text-[#FF5722]');
            setTimeout(() => {
                totalPreviewEl.classList.remove('text-[#FF5722]');
            }, 200);
        };

        // Attach event listeners ke baris yang ada
        const attachRowListeners = (row) => {
            const select = row.querySelector('.menu-select');
            const qtyInput = row.querySelector('.qty-input');
            const removeBtn = row.querySelector('.remove-row-btn');

            if (select) {
                select.addEventListener('change', calculateTotals);
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', calculateTotals);
            }
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    const rowCount = orderItemsContainer.querySelectorAll('.order-item-row').length;
                    if (rowCount <= 1) {
                        alert('Minimal pesanan harus memiliki 1 item menu.');
                        return;
                    }
                    row.style.opacity = '0';
                    row.style.transform = 'translateY(-6px)';
                    row.style.transition = 'opacity 160ms ease-out, transform 160ms ease-out';
                    setTimeout(() => {
                        row.remove();
                        calculateTotals();
                    }, 160);
                });
            }
        };

        // Pasang listener pada baris bawaan awal
        orderItemsContainer.querySelectorAll('.order-item-row').forEach(attachRowListeners);

        // Tambah baris baru
        if (addMenuItemBtn && menuTemplate) {
            addMenuItemBtn.addEventListener('click', () => {
                const clone = menuTemplate.content.cloneNode(true);
                const newRow = clone.querySelector('.order-item-row');
                newRow.style.opacity = '0';
                newRow.style.transform = 'translateY(8px)';
                orderItemsContainer.appendChild(newRow);

                // Animate entry
                requestAnimationFrame(() => {
                    newRow.style.transition = 'opacity 200ms ease-out, transform 200ms ease-out';
                    newRow.style.opacity = '1';
                    newRow.style.transform = 'translateY(0)';
                });

                attachRowListeners(newRow);
                calculateTotals();
            });
        }

        // Kalkulasi awal
        calculateTotals();
    }
});
