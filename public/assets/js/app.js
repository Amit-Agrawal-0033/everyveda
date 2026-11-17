/**
 * EveryVeda.com Frontend JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. PIN Code Checker
    const pincodeBtn = document.getElementById('btnCheckPincode');
    const pincodeInput = document.getElementById('inputPincode');
    const pincodeResult = document.getElementById('pincodeResult');

    if (pincodeBtn && pincodeInput && pincodeResult) {
        pincodeBtn.addEventListener('click', function () {
            const pincode = pincodeInput.value.trim();
            if (!/^[1-9]\d{5}$/.test(pincode)) {
                pincodeResult.innerHTML = '<span class="text-danger">Please enter a valid 6-digit Indian PIN code.</span>';
                return;
            }

            pincodeBtn.disabled = true;
            pincodeBtn.innerText = 'Checking...';

            fetch('/api/check-pincode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ pincode: pincode })
            })
            .then(res => res.json())
            .then(data => {
                pincodeBtn.disabled = false;
                pincodeBtn.innerText = 'Check';
                if (data.serviceable) {
                    pincodeResult.innerHTML = '<span class="text-success fw-bold">✓ ' + data.message + '</span>';
                } else {
                    pincodeResult.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                }
            })
            .catch(() => {
                pincodeBtn.disabled = false;
                pincodeBtn.innerText = 'Check';
                pincodeResult.innerHTML = '<span class="text-danger">Could not check delivery. Please try again.</span>';
            });
        });
    }

    // 2. Variant Selector on Product Page
    const variantSelects = document.querySelectorAll('input[name="variant_id_radio"]');
    const displayPrice = document.getElementById('productDisplayPrice');
    const displayOriginalPrice = document.getElementById('productOriginalPrice');
    const hiddenVariantId = document.getElementById('hiddenVariantId');
    const stockStatusDisplay = document.getElementById('productStockStatus');

    if (variantSelects.length > 0) {
        variantSelects.forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    const price = this.dataset.price;
                    const salePrice = this.dataset.salePrice;
                    const stock = parseInt(this.dataset.stock, 10);
                    const vid = this.value;

                    if (hiddenVariantId) hiddenVariantId.value = vid;

                    if (salePrice && parseFloat(salePrice) > 0) {
                        displayPrice.innerText = '₹' + parseFloat(salePrice).toFixed(2);
                        if (displayOriginalPrice) {
                            displayOriginalPrice.innerText = '₹' + parseFloat(price).toFixed(2);
                            displayOriginalPrice.style.display = 'inline';
                        }
                    } else {
                        displayPrice.innerText = '₹' + parseFloat(price).toFixed(2);
                        if (displayOriginalPrice) displayOriginalPrice.style.display = 'none';
                    }

                    if (stockStatusDisplay) {
                        if (stock > 0) {
                            stockStatusDisplay.className = 'text-success fw-bold';
                            stockStatusDisplay.innerText = '✓ In Stock (' + stock + ' units)';
                        } else {
                            stockStatusDisplay.className = 'text-danger fw-bold';
                            stockStatusDisplay.innerText = '✗ Temporarily Out of Stock';
                        }
                    }
                }
            });
        });
    }

    // 3. Product Gallery Image Switcher
    const mainImg = document.getElementById('productMainImage');
    const thumbs = document.querySelectorAll('.product-thumb-img');
    if (mainImg && thumbs.length > 0) {
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function () {
                mainImg.src = this.dataset.fullSrc;
                thumbs.forEach(t => t.classList.remove('active-thumb'));
                this.classList.add('active-thumb');
            });
        });
    }

    // 4. Quantity Increment/Decrement
    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentNode.querySelector('.qty-input');
            let val = parseInt(input.value, 10) || 1;
            if (val > 1) {
                input.value = val - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentNode.querySelector('.qty-input');
            let val = parseInt(input.value, 10) || 1;
            input.value = val + 1;
            input.dispatchEvent(new Event('change'));
        });
    });

    // 5. Hero Carousel: Strict Single-Slide Text Display (Zero Overlap Guaranteed)
    const heroCarousel = document.getElementById('homeHeroCarousel');
    if (heroCarousel) {
        heroCarousel.addEventListener('slide.bs.carousel', function (e) {
            // Outgoing slide elements immediately hidden to prevent cross-fade text ghosting
            const outgoingSlide = heroCarousel.querySelector('.carousel-item.active');
            if (outgoingSlide) {
                outgoingSlide.querySelectorAll('.hero-badge, .hero-title, .hero-subtitle, .hero-actions').forEach(el => {
                    el.style.opacity = '0';
                    el.style.visibility = 'hidden';
                });
            }
        });

        heroCarousel.addEventListener('slid.bs.carousel', function (e) {
            // Clean up inline styles so active CSS classes govern presentation
            heroCarousel.querySelectorAll('.hero-badge, .hero-title, .hero-subtitle, .hero-actions').forEach(el => {
                el.style.opacity = '';
                el.style.visibility = '';
            });
        });
    }
});
