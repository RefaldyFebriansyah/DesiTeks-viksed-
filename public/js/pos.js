/**
 * DesiTeks POS JavaScript - Redesigned & Enhanced
 * Handles cart management, price calculation, stepper controls, payment validation,
 * barcode scanner auto-add, and fabric requirement calculator.
 */

let cart = []; // Array of cart items

/**
 * Add fabric to cart. If already exists (same id + satuan), increase qty.
 */
function addToCart(id, name, hargaMeter, hargaRol, stokMeter, stokRol, meterPerRol = 50) {
    const sMeter = parseFloat(stokMeter) || 0;
    const sRol = parseInt(stokRol) || 0;
    const mPerRol = parseFloat(meterPerRol) || 50;
    const totalStokMeter = sMeter + (sRol * mPerRol);

    // Calculate current consumed meters in cart for this fabric
    let currentInCart = 0;
    cart.filter(i => i.id === id).forEach(i => {
        currentInCart += (i.satuan === 'rol' ? (i.jumlah * mPerRol) : i.jumlah);
    });

    if (currentInCart + 1 > totalStokMeter) {
        alert('Stok kain ini tidak mencukupi untuk ditambah lagi.');
        return;
    }

    // Check if already in cart with meter (default)
    const existing = cart.find(i => i.id === id && i.satuan === 'meter');
    if (existing) {
        existing.jumlah += 1;
    } else {
        cart.push({
            id,
            name,
            hargaMeter,
            hargaRol,
            satuan: 'meter',
            jumlah: 1,
            stokMeter: sMeter,
            stokRol: sRol,
            meterPerRol: mPerRol,
            totalStokMeter: totalStokMeter,
        });
    }
    renderCart();
}

function removeFromCart(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function updateQty(idx, val) {
    const item = cart[idx];
    const v = parseFloat(val);
    if (isNaN(v) || v <= 0) {
        cart.splice(idx, 1);
        renderCart();
        return;
    }

    const mPerRol = item.meterPerRol || 50;
    const neededMeters = item.satuan === 'rol' ? (v * mPerRol) : v;
    let otherInCart = 0;
    cart.forEach((c, i) => {
        if (i !== idx && c.id === item.id) {
            otherInCart += (c.satuan === 'rol' ? (c.jumlah * mPerRol) : c.jumlah);
        }
    });

    if (otherInCart + neededMeters > item.totalStokMeter) {
        alert(`Jumlah melebihi stok yang tersedia (${item.totalStokMeter.toFixed(1)} m).`);
        return;
    }

    item.jumlah = v;
    renderCart();
}

function stepQty(idx, dir) {
    const item = cart[idx];
    const step = item.satuan === 'rol' ? 1 : 0.5;
    let newVal = parseFloat(item.jumlah) + (dir * step);
    if (newVal <= 0) {
        removeFromCart(idx);
        return;
    }
    // Round to 1 decimal place
    newVal = Math.round(newVal * 10) / 10;

    const mPerRol = item.meterPerRol || 50;
    const neededMeters = item.satuan === 'rol' ? (newVal * mPerRol) : newVal;
    let otherInCart = 0;
    cart.forEach((c, i) => {
        if (i !== idx && c.id === item.id) {
            otherInCart += (c.satuan === 'rol' ? (c.jumlah * mPerRol) : c.jumlah);
        }
    });

    if (dir > 0 && (otherInCart + neededMeters > item.totalStokMeter)) {
        alert(`Jumlah melebihi stok yang tersedia (${item.totalStokMeter.toFixed(1)} m).`);
        return;
    }

    item.jumlah = newVal;
    renderCart();
}

function updateSatuan(idx, satuan) {
    const item = cart[idx];
    const mPerRol = item.meterPerRol || 50;
    const neededMeters = satuan === 'rol' ? mPerRol : 1;

    let otherInCart = 0;
    cart.forEach((c, i) => {
        if (i !== idx && c.id === item.id) {
            otherInCart += (c.satuan === 'rol' ? (c.jumlah * mPerRol) : c.jumlah);
        }
    });

    if (otherInCart + neededMeters > item.totalStokMeter) {
        alert(`Stok tidak mencukupi untuk 1 rol (${mPerRol} m).`);
        return;
    }

    cart[idx].satuan = satuan;
    cart[idx].jumlah = 1;
    renderCart();
}

function getHarga(item) {
    return item.satuan === 'meter' ? item.hargaMeter : item.hargaRol;
}

function getSubtotal(item) {
    return getHarga(item) * item.jumlah;
}

function getTotalCart() {
    return cart.reduce((sum, item) => sum + getSubtotal(item), 0);
}

function formatRp(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function getDiscountedTotal() {
    const subtotal = getTotalCart();
    const customerSelect = document.getElementById('customer_id');
    let discountPct = 0;
    if (customerSelect) {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.tipe === 'member') {
            discountPct = parseFloat(selectedOption.dataset.diskon) || 0;
        }
    }
    const discountAmt = (subtotal * discountPct) / 100;
    const finalTotal = Math.max(0, subtotal - discountAmt);
    return {
        subtotal,
        discountPct,
        discountAmt,
        finalTotal
    };
}

// Override setExactAmount to use final discounted total
window.setExactAmount = function () {
    const calcs = getDiscountedTotal();
    const total = Math.round(calcs.finalTotal);
    const input = document.getElementById('jumlahBayar');
    if (input) {
        input.value = total > 0 ? total.toLocaleString('id-ID') : '';
    }
    hitungKembalian();
};

window.addPay = function (amt) {
    const input = document.getElementById('jumlahBayar');
    if (!input) return;
    let current = parseFloat((input.value || '').replace(/\D/g, '')) || 0;
    let nextVal = current + amt;
    input.value = nextVal > 0 ? nextVal.toLocaleString('id-ID') : '';
    hitungKembalian();
};

window.formatJumlahBayarInput = function (el) {
    if (!el) return;
    let cursorPosition = el.selectionStart;
    let originalLength = el.value.length;
    let rawVal = el.value.replace(/\D/g, '');

    if (rawVal === '') {
        el.value = '';
    } else {
        let formatted = parseInt(rawVal, 10).toLocaleString('id-ID');
        el.value = formatted;
        let newLength = formatted.length;
        cursorPosition = cursorPosition + (newLength - originalLength);
        if (cursorPosition >= 0 && cursorPosition <= newLength) {
            el.setSelectionRange(cursorPosition, cursorPosition);
        }
    }
    hitungKembalian();
};

function renderCart() {
    const cartEmpty = document.getElementById('cartEmpty');
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const cartInputs = document.getElementById('cartInputs');
    const btnBayar = document.getElementById('btnBayar');

    const cartFooter = document.getElementById('cartFooter');

    if (cart.length === 0) {
        cartEmpty.style.display = 'block';
        if (cartFooter) cartFooter.classList.add('d-none');
        cartItems.innerHTML = '';
        cartCount.textContent = '0 item';
        cartTotal.textContent = 'Rp 0';
        cartInputs.innerHTML = '';
        btnBayar.disabled = true;
        btnBayar.classList.add('disabled');
        document.getElementById('kembalian').textContent = 'Rp 0';
        document.getElementById('kembalian').style.color = 'var(--dt-muted)';

        const posSubtotal = document.getElementById('posSubtotal');
        if (posSubtotal) posSubtotal.textContent = 'Rp 0';
        const discountRow = document.getElementById('posDiscountRow');
        if (discountRow) discountRow.classList.add('d-none');

        // Reset mobile cart badge
        const mobileCartBadge = document.getElementById('mobileCartBadge');
        if (mobileCartBadge) {
            mobileCartBadge.textContent = '0';
        }
        updateFabricCardsStock();
        return;
    }

    cartEmpty.style.display = 'none';
    if (cartFooter) cartFooter.classList.remove('d-none');
    cartCount.textContent = cart.length + ' item';

    let html = '';
    let inputs = '';

    cart.forEach((item, idx) => {
        const subtotal = getSubtotal(item);
        html += `
        <div class="dt-cart-item">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-700 text-navy" style="font-size:13.5px; line-height: 1.3;">${item.name}</div>
                    <div class="text-muted" style="font-size:11px;">Stok: ${item.totalStokMeter.toFixed(1)} m (${item.stokRol} rol)</div>
                </div>
                <button type="button" class="btn btn-sm p-1" onclick="removeFromCart(${idx})" style="color:var(--dt-danger); line-height:1; background: transparent; border: none;">
                    <i class="bi bi-trash3-fill" style="font-size:14px"></i>
                </button>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex gap-2 align-items-center">
                    <!-- Unit Segmented Switch -->
                    <div class="dt-unit-group">
                        <button type="button" class="dt-unit-btn ${item.satuan === 'meter' ? 'active' : ''}" onclick="updateSatuan(${idx}, 'meter')">Meter</button>
                        <button type="button" class="dt-unit-btn ${item.satuan === 'rol' ? 'active' : ''}" onclick="updateSatuan(${idx}, 'rol')">Rol</button>
                    </div>
                    
                    <!-- Stepper Qty -->
                    <div class="dt-stepper">
                        <button type="button" class="dt-stepper-btn" onclick="stepQty(${idx}, -1)">−</button>
                        <input type="number" class="dt-stepper-input" value="${item.jumlah}" min="0.01" step="${item.satuan === 'rol' ? '1' : '0.1'}" onchange="updateQty(${idx}, this.value)">
                        <button type="button" class="dt-stepper-btn" onclick="stepQty(${idx}, 1)">+</button>
                    </div>
                </div>
                
                <div class="text-end">
                    <div style="font-size:11.5px; color:var(--dt-muted)">${formatRp(getHarga(item))}</div>
                    <div class="fw-800 text-navy" style="font-size:14.5px">${formatRp(subtotal)}</div>
                </div>
            </div>
        </div>`;

        inputs += `<input type="hidden" name="items[${idx}][fabric_id]"    value="${item.id}">
<input type="hidden" name="items[${idx}][satuan]"      value="${item.satuan}">
<input type="hidden" name="items[${idx}][jumlah]"      value="${item.jumlah}">
<input type="hidden" name="items[${idx}][harga_satuan]" value="${getHarga(item)}">`;
    });

    cartItems.innerHTML = html;
    cartInputs.innerHTML = inputs;

    const calcs = getDiscountedTotal();

    // Update subtotal
    const posSubtotal = document.getElementById('posSubtotal');
    if (posSubtotal) posSubtotal.textContent = formatRp(calcs.subtotal);

    // Update discount row
    const discountRow = document.getElementById('posDiscountRow');
    const posDiscount = document.getElementById('posDiscount');
    if (discountRow && posDiscount) {
        if (calcs.discountAmt > 0) {
            discountRow.classList.remove('d-none');
            posDiscount.textContent = '- ' + formatRp(calcs.discountAmt);
        } else {
            discountRow.classList.add('d-none');
        }
    }

    // Set hidden input
    const inputDiskon = document.getElementById('inputDiskon');
    if (inputDiskon) inputDiskon.value = calcs.discountAmt;

    cartTotal.textContent = formatRp(calcs.finalTotal);

    btnBayar.disabled = false;
    btnBayar.classList.remove('disabled');

    hitungKembalian();

    // Update mobile cart badge and play animation
    const mobileCartBadge = document.getElementById('mobileCartBadge');
    if (mobileCartBadge) {
        mobileCartBadge.textContent = cart.length;
        mobileCartBadge.classList.remove('pulse-animation');
        void mobileCartBadge.offsetWidth; // trigger reflow
        mobileCartBadge.classList.add('pulse-animation');
    }

    updateFabricCardsStock();
}

/**
 * Synchronize available stock dynamically on POS fabric cards based on items in cart
 */
function updateFabricCardsStock() {
    // 1. Group total meters used from cart per fabric id
    const usage = {};
    cart.forEach(item => {
        const id = item.id;
        const mPerRol = item.meterPerRol || 50;
        const consumed = item.satuan === 'rol' ? (item.jumlah * mPerRol) : item.jumlah;
        usage[id] = (usage[id] || 0) + consumed;
    });

    // 2. Update each fabric card
    document.querySelectorAll('.dt-fabric-item[data-fabric-id]').forEach(card => {
        const id = parseInt(card.getAttribute('data-fabric-id'));
        const initialMeter = parseFloat(card.getAttribute('data-initial-meter')) || 0;
        const initialRol = parseInt(card.getAttribute('data-initial-rol')) || 0;
        const meterPerRol = parseFloat(card.getAttribute('data-meter-per-rol')) || 50;
        const minStock = parseFloat(card.getAttribute('data-min-stock')) || 10;

        const totalInitialMeters = initialMeter + (initialRol * meterPerRol);
        const usedMeters = usage[id] || 0;
        const remainingTotalMeters = Math.max(0, totalInitialMeters - usedMeters);

        let remainingRolls = 0;
        if (remainingTotalMeters > 0) {
            remainingRolls = Math.floor(remainingTotalMeters / meterPerRol);
        }

        const meterTextEl = card.querySelector('.meter-text');
        const rolTextEl = card.querySelector('.rol-text');
        const badgeContainer = card.querySelector('.badge-stock-container');
        const badgeHabis = card.querySelector('.badge-habis');
        const badgeMenipis = card.querySelector('.badge-menipis');

        if (meterTextEl) {
            meterTextEl.textContent = remainingTotalMeters.toFixed(1) + ' m';
        }
        if (rolTextEl) {
            rolTextEl.textContent = remainingRolls + ' r';
        }

        if (remainingTotalMeters <= 0) {
            card.classList.add('out-of-stock');
            if (badgeHabis) badgeHabis.classList.remove('d-none');
            if (badgeContainer) badgeContainer.classList.add('d-none');
            if (badgeMenipis) badgeMenipis.classList.add('d-none');
        } else {
            card.classList.remove('out-of-stock');
            if (badgeHabis) badgeHabis.classList.add('d-none');
            if (badgeContainer) badgeContainer.classList.remove('d-none');
            if (badgeMenipis) {
                if (remainingTotalMeters <= minStock) {
                    badgeMenipis.classList.remove('d-none');
                } else {
                    badgeMenipis.classList.add('d-none');
                }
            }
        }
    });
}

function hitungKembalian() {
    const calcs = getDiscountedTotal();
    const total = calcs.finalTotal;
    const inputEl = document.getElementById('jumlahBayar');
    const rawVal = inputEl ? inputEl.value.replace(/\D/g, '') : '';
    const bayar = parseFloat(rawVal) || 0;
    const kembalian = bayar - total;
    const el = document.getElementById('kembalian');

    if (!el) return;

    if (bayar === 0) {
        el.textContent = 'Rp 0';
        el.style.color = 'var(--dt-muted)';
    } else if (kembalian < 0) {
        el.textContent = '- ' + formatRp(Math.abs(kembalian)) + ' (kurang)';
        el.style.color = 'var(--dt-danger)';
    } else {
        el.textContent = formatRp(kembalian);
        el.style.color = 'var(--dt-success)';
    }
}

function showToastAlert(title, message, type = 'danger') {
    let container = document.querySelector('.dt-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'dt-toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `dt-toast dt-toast-${type} shadow-lg`;
    toast.role = 'alert';

    const bgClass = type === 'success' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger';
    const iconClass = type === 'success' ? 'bi-check-lg' : 'bi-exclamation-triangle-fill';

    toast.innerHTML = `
        <div class="dt-toast-content d-flex align-items-center gap-3">
            <div class="dt-toast-icon ${bgClass} rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; flex-shrink:0;">
                <i class="bi ${iconClass}" style="font-size:18px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-700 text-navy" style="font-size:13px; line-height: 1.2;">${title}</div>
                <div class="text-muted" style="font-size:12px; margin-top: 2px;">${message}</div>
            </div>
            <button type="button" class="btn-close ms-2" onclick="closeToast(this)" aria-label="Close" style="background-size: 10px; opacity: 0.6; border: none; background-color: transparent;"></button>
        </div>
        <div class="dt-toast-progress bg-${type}"></div>
    `;

    container.appendChild(toast);

    void toast.offsetWidth;
    toast.classList.add('show');

    setTimeout(() => {
        if (toast && toast.classList.contains('show')) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 400);
        }
    }, 4000);
}

window.validateSale = function () {
    if (cart.length === 0) {
        showToastAlert('Peringatan!', 'Keranjang masih kosong.', 'danger');
        return false;
    }

    for (let i = 0; i < cart.length; i++) {
        const item = cart[i];
        if (item.satuan === 'meter') {
            const totalAvailable = parseFloat(item.stokMeter) + (parseInt(item.stokRol) * parseFloat(item.meterPerRol));
            if (item.jumlah > totalAvailable) {
                showToastAlert('Stok Tidak Cukup!', `Stok meter "${item.name}" tidak cukup. Tersedia: ${totalAvailable.toFixed(1)} m`, 'danger');
                return false;
            }
        }
        if (item.satuan === 'rol' && item.jumlah > item.stokRol) {
            showToastAlert('Stok Tidak Cukup!', `Stok rol "${item.name}" tidak cukup. Tersedia: ${item.stokRol} rol`, 'danger');
            return false;
        }
    }

    const calcs = getDiscountedTotal();
    const total = calcs.finalTotal;
    const inputEl = document.getElementById('jumlahBayar');
    const rawVal = inputEl ? inputEl.value.replace(/\D/g, '') : '';
    const bayar = parseFloat(rawVal) || 0;

    if (bayar <= 0) {
        showToastAlert('Peringatan!', 'Masukkan jumlah pembayaran terlebih dahulu.', 'danger');
        if (inputEl) inputEl.focus();
        return false;
    }
    if (bayar < total) {
        showToastAlert('Pembayaran Kurang!', 'Pembayaran belum mencukupi. Kurang: ' + formatRp(total - bayar), 'danger');
        return false;
    }

    if (inputEl) {
        inputEl.value = rawVal;
    }

    return true; // Langsung kirim dan masuk ke halaman struk/sukses
}

// ═══ FABRIC REQUIREMENT CALCULATOR LOGIC ═══

document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('kalkulatorKainModal');
    if (modalEl) {
        // Update list of items in the cart inside the calculator dropdown when modal opens
        modalEl.addEventListener('show.bs.modal', function () {
            const select = document.getElementById('calcApplyTo');
            const wrapper = document.getElementById('calcApplyToWrapper');

            if (cart.length === 0) {
                wrapper.style.display = 'none';
                return;
            }

            wrapper.style.display = 'block';
            select.innerHTML = '';
            cart.forEach((item, idx) => {
                const option = document.createElement('option');
                option.value = idx;
                option.textContent = item.name + ' (' + item.satuan + ')';
                select.appendChild(option);
            });
        });

        // Set up real-time calculate event listeners
        ['change', 'input'].forEach(evt => {
            ['calcJenis', 'calcUkuran', 'calcJumlah'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener(evt, hitungKebutuhan);
                }
            });
        });
    }
});

function hitungKebutuhan() {
    const jenis = parseFloat(document.getElementById('calcJenis').value) || 2.0;
    const ukuran = parseFloat(document.getElementById('calcUkuran').value) || 1.0;
    const jumlah = parseInt(document.getElementById('calcJumlah').value) || 1;

    const total = jenis * ukuran * jumlah;
    const rounded = Math.round(total * 10) / 10;

    const resultEl = document.getElementById('calcResult');
    if (resultEl) {
        resultEl.textContent = rounded.toFixed(1);
    }
}

function applyCalculatorResult() {
    if (cart.length === 0) {
        alert('Keranjang belanja kosong. Silakan masukkan kain terlebih dahulu.');
        return;
    }
    const select = document.getElementById('calcApplyTo');
    const idx = parseInt(select.value);
    const value = parseFloat(document.getElementById('calcResult').textContent);

    if (!isNaN(idx) && cart[idx]) {
        cart[idx].jumlah = value;
        renderCart();

        // Hide modal
        const modalEl = document.getElementById('kalkulatorKainModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }
}
