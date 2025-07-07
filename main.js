// नेपाली नोट: यो फाइलमा सबै कस्टम JavaScript राखिएको छ

// नेपाली नोट: भाषा चयन बटनको लागि कोड
document.addEventListener('DOMContentLoaded', function() {
    const languageSelector = document.querySelector('button:has(span:contains("EN"))');
    if (languageSelector) {
        languageSelector.addEventListener('click', function() {
            // नेपाली नोट: भाषा चयन गर्दा के हुन्छ यहाँ लेख्न सकिन्छ
            console.log('Language selector clicked');
        });
    }

    // नेपाली नोट: मनपर्ने (favorite) बटनको लागि कोड
    const favoriteButtons = document.querySelectorAll('.property-card .ri-heart-line');
    favoriteButtons.forEach(button => {
        button.parentElement.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon.classList.contains('ri-heart-line')) {
                icon.classList.remove('ri-heart-line');
                icon.classList.add('ri-heart-fill');
                icon.classList.add('text-secondary');
            } else {
                icon.classList.remove('ri-heart-fill');
                icon.classList.remove('text-secondary');
                icon.classList.add('ri-heart-line');
            }
        });
    });

    // नेपाली नोट: प्रोपर्टी कार्ड hover गर्दा छायाँ देखाउने कोड
    const propertyCards = document.querySelectorAll('.property-card');
    propertyCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-md');
        });
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-md');
        });
    });

    // नेपाली नोट: मोबाइल मेनु खोल्ने/बन्द गर्ने कोड
    const menuButton = document.querySelector('.ri-menu-line')?.parentElement;
    if (menuButton) {
        menuButton.addEventListener('click', function() {
            console.log('Mobile menu button clicked');
            // नेपाली नोट: यहाँ मोबाइल मेनु देखाउने/लुकाउने कोड राख्न सकिन्छ
        });
    }

    // नेपाली नोट: फिल्टर बटन क्लिक गर्दा के गर्ने कोड
    const filterButtons = document.querySelectorAll('button:has(.ri-filter-line), button:contains("Budget"), button:contains("Room Type"), button:contains("More Filters")');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Filter button clicked:', this.textContent.trim());
            // नेपाली नोट: यहाँ फिल्टर लागू गर्ने कोड राख्न सकिन्छ
        });
    });
});