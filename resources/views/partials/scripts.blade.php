@php
    $bookingAnchors = [];

    foreach (config('fleet.carts', []) as $code => $cart) {
        $bookingAnchors['#'.$cart['anchor']] = $code;
    }
@endphp

<script>
    const bookingAnchors = @json($bookingAnchors);

    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        const icon = document.getElementById('menuIcon');

        menu.classList.toggle('hidden');

        if (menu.classList.contains('hidden')) {
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        }
    }

    function syncCartQuantityFields() {
        const select = document.getElementById('cartSelectionSelect');

        if (!select) {
            return;
        }

        const selected = select.value;

        document
            .querySelectorAll('[data-cart-quantity]')
            .forEach((element) => {
                const cartType = element.dataset.cartQuantity;

                const shouldShow =
                    selected === 'mix' ||
                    selected === cartType;

                element.classList.toggle(
                    'hidden',
                    !shouldShow
                );
            });
    }

    function openModal(cartType = null) {
        const modal = document.getElementById('bookingModal');
        const select = document.getElementById('cartSelectionSelect');

        modal.classList.remove('hidden');

        if (select && cartType) {
            select.value = cartType;
        }

        syncCartQuantityFields();

        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        document
            .getElementById('bookingModal')
            .classList
            .add('hidden');

        document.body.classList.remove('overflow-hidden');
    }

    function checkUrlAndOpenModal() {
        const hash = window.location.hash;

        const isCartBooking = Object.prototype.hasOwnProperty.call(
            bookingAnchors,
            hash
        );

        if (!isCartBooking && hash !== '#book-now') {
            return;
        }

        const fleetSection = document.getElementById('fleet');

        if (fleetSection) {
            fleetSection.scrollIntoView({
                behavior: 'smooth'
            });
        }

        setTimeout(() => {
            const cartType =
                bookingAnchors[hash] ?? null;

            openModal(cartType);

            history.replaceState(
                null,
                null,
                window.location.pathname
            );
        }, 800);
    }

    window.addEventListener('load', () => {
        syncCartQuantityFields();
        checkUrlAndOpenModal();
    });

    window.addEventListener(
        'hashchange',
        checkUrlAndOpenModal
    );
</script>