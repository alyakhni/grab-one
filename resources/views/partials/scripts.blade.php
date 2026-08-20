<script>
    // منطق قائمة الموبايل
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

    // 1. التعديل هنا: جعلنا القيمة الافتراضية null (لا تختار شيئاً محدداً)
    function openModal(cartType = null) {
        const modal = document.getElementById('bookingModal');
        const select = document.getElementById('cartSizeSelect');
        
        modal.classList.remove('hidden');
        
        // إذا تم تمرير نوع العربة، قم بتغيير القائمة المنسدلة، وإلا اتركها كما هي
        if (select && cartType) {
            select.value = cartType;
        }
        
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // منطق التمرير الذكي
    function checkUrlAndOpenModal() {
        const hash = window.location.hash;

        // 2. التعديل هنا: أضفنا #book-now كخيار جديد للروابط
        if (hash === '#book-4-seater' || hash === '#book-6-seater' || hash === '#book-now') {
            
            const fleetSection = document.getElementById('fleet');
            if (fleetSection) {
                fleetSection.scrollIntoView({ behavior: 'smooth' });
            }

            setTimeout(() => {
                let cartType = null; // الافتراضي بدون تحديد
                
                if (hash === '#book-4-seater') cartType = '4-Seater';
                if (hash === '#book-6-seater') cartType = '6-Seater';
                // إذا كان الرابط #book-now ستبقى القيمة null ولن يفرض اختياراً
                
                openModal(cartType);
                
                history.replaceState(null, null, window.location.pathname);
            }, 800);
        }
    }

    window.addEventListener('load', checkUrlAndOpenModal);
    window.addEventListener('hashchange', checkUrlAndOpenModal);
</script>