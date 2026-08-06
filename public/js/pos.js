/**
 * DesiTeks POS JavaScript
 * Handles cart management, price calculation, and payment validation
 */

let cart = []; // Array of cart items

/**
 * Add fabric to cart. If already exists (same id + satuan), increase qty.
 */
function addToCart(id, name, hargaMeter, hargaRol, stokMeter, stokRol) {
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
            stokMeter,
            stokRol,
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
    if (isNaN(v) || v <= 0) { cart.splice(idx,1); renderCart(); return; }
    item.jumlah = v;
    renderCart();
}

function updateSatuan(idx, satuan) {
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

function renderCart() {
    const cartEmpty = document.getElementById('cartEmpty');
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    const cartInputs = document.getElementById('cartInputs');
    const btnBayar  = document.getElementById('btnBayar');

    if (cart.length === 0) {
        cartEmpty.style.display = 'block';
        cartItems.innerHTML = '';
        cartCount.textContent = '0 item';
        cartTotal.textContent = 'Rp 0';
        cartInputs.innerHTML = '';
        btnBayar.disabled = true;
        btnBayar.classList.add('disabled');
        document.getElementById('kembalian').textContent = 'Rp 0';
        return;
    }

    cartEmpty.style.display = 'none';
    cartCount.textContent = cart.length + ' item';

    let html = '';
    let inputs = '';

    cart.forEach((item, idx) => {
        const subtotal = getSubtotal(item);
        html += `
        <div class="dt-cart-item">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="fw-600" style="font-size:13px;flex:1">${item.name}</div>
                <button type="button" class="btn btn-sm p-0 ms-2" onclick="removeFromCart(${idx})" style="color:var(--dt-danger);line-height:1">
                    <i class="bi bi-trash3" style="font-size:13px"></i>
                </button>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <select class="dt-select" style="width:90px;padding:4px 6px;font-size:12px" onchange="updateSatuan(${idx}, this.value)">
                    <option value="meter" ${item.satuan==='meter'?'selected':''}>Meter</option>
                    <option value="rol"   ${item.satuan==='rol'  ?'selected':''}>Rol</option>
                </select>
                <input type="number" class="dt-input" style="width:80px;padding:4px 8px;font-size:13px"
                    value="${item.jumlah}" min="0.01" step="${item.satuan==='rol'?'1':'0.1'}"
                    onchange="updateQty(${idx}, this.value)">
                <span style="font-size:12px;color:var(--dt-muted)">× ${formatRp(getHarga(item))}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span style="font-size:11px;color:var(--dt-muted)">
                    Stok: ${item.stokMeter.toFixed(1)} m / ${item.stokRol} rol
                </span>
                <span class="fw-700" style="font-size:14px;color:var(--dt-navy)">${formatRp(subtotal)}</span>
            </div>
        </div>`;

        inputs += `<input type="hidden" name="items[${idx}][fabric_id]"    value="${item.id}">
<input type="hidden" name="items[${idx}][satuan]"      value="${item.satuan}">
<input type="hidden" name="items[${idx}][jumlah]"      value="${item.jumlah}">
<input type="hidden" name="items[${idx}][harga_satuan]" value="${getHarga(item)}">`;
    });

    cartItems.innerHTML = html;
    cartInputs.innerHTML = inputs;

    const total = getTotalCart();
    cartTotal.textContent = formatRp(total);

    btnBayar.disabled = false;
    btnBayar.classList.remove('disabled');

    hitungKembalian();
}

function hitungKembalian() {
    const total   = getTotalCart();
    const bayar   = parseFloat(document.getElementById('jumlahBayar').value) || 0;
    const kembalian = bayar - total;
    const el = document.getElementById('kembalian');

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

function validateSale() {
    if (cart.length === 0) {
        alert('Keranjang masih kosong. Pilih kain terlebih dahulu.');
        return false;
    }

    // Validate quantities against stock
    for (let i = 0; i < cart.length; i++) {
        const item = cart[i];
        if (item.satuan === 'meter' && item.jumlah > item.stokMeter) {
            alert(`Stok meter "${item.name}" tidak cukup. Tersedia: ${item.stokMeter.toFixed(1)} m`);
            return false;
        }
        if (item.satuan === 'rol' && item.jumlah > item.stokRol) {
            alert(`Stok rol "${item.name}" tidak cukup. Tersedia: ${item.stokRol} rol`);
            return false;
        }
    }

    const total = getTotalCart();
    const bayar = parseFloat(document.getElementById('jumlahBayar').value) || 0;

    if (bayar <= 0) {
        alert('Masukkan jumlah pembayaran terlebih dahulu.');
        document.getElementById('jumlahBayar').focus();
        return false;
    }
    if (bayar < total) {
        alert('Pembayaran belum mencukupi. Kurang: ' + formatRp(total - bayar));
        return false;
    }

    return confirm(`Konfirmasi transaksi:\nTotal: ${formatRp(total)}\nBayar: ${formatRp(bayar)}\nKembalian: ${formatRp(bayar - total)}\n\nLanjutkan?`);
}
