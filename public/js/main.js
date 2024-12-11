
document.addEventListener('DOMContentLoaded', function(){

    document.querySelectorAll('img[alt]').forEach(function(item) {
        if(item.getAttribute('alt').indexOf('Картинка') !== -1 && !item.getAttribute('title'))
            item.setAttribute('title', item.getAttribute('alt').replace('Картинка', 'Фото'));
        else if(item.getAttribute('alt').indexOf('картинка') !== -1 && !item.getAttribute('title'))
            item.setAttribute('title', item.getAttribute('alt').replace('картинка', 'фото'));
        else if(item.getAttribute('alt').indexOf('Фото') !== -1 && !item.getAttribute('title'))
            item.setAttribute('title', item.getAttribute('alt').replace('Фото', 'Картинка'));
        else if(item.getAttribute('alt').indexOf('фото') !== -1 && !item.getAttribute('title'))
            item.setAttribute('title', item.getAttribute('alt').replace('фото', 'картинка'));
    });

    if(window.location.hash) {
        setTimeout(() => {
            window.scrollTo(0, document.querySelector(window.location.hash).offsetTop - 150);
        }, 0);
    }

    let arrowsMain = document.querySelectorAll(".js-arrows-main");
    
    function initialSliderMain() {
    var sliderAutoStart;
        for(var i = 0;arrowsMain.length > i; i++) {
            let slider = arrowsMain[i].closest(".js-slider");
            let arrowNext = arrowsMain[i].querySelector('.next');
            let arrowPrev = arrowsMain[i].querySelector('.prev');
            let allItems = slider.querySelectorAll('.js-main-slider');
            
            
            function showSliderNext(item, className) {
                if(item.nextElementSibling == null) {
                    item.classList.remove(className)
                    allItems[0].classList.add(className);
                    return;
                }
                
                item.nextElementSibling.classList.add(className);
                item.classList.remove(className);
            }
            
            function showSliderPrev(item, className) {
                if(item.previousElementSibling == null) {
                    item.classList.remove(className)
                    allItems[allItems.length - 1].classList.add(className);
                    return;
                }
                
                item.previousElementSibling.classList.add(className);
                item.classList.remove(className);
            }
            
            function autoPlay() {
                showSliderNext(slider.querySelector('.js-main-slider.show'), "show");
                showSliderNext(slider.querySelector('.js-main-slider.next'), "next");
                showSliderNext(slider.querySelector('.js-main-slider.prev'), "prev");
                changeDots();
            }
            
            if(window.innerWidth > 1160){
                slider.addEventListener('mouseover', function(){
                    clearInterval(sliderAutoStart);
                });
                
                    
                slider.addEventListener('mouseleave', function(){
                    sliderAutoStart = setInterval(autoPlay, 10000);
                });
                
                sliderAutoStart = setInterval(autoPlay, 10000);
            }
            
            function changeDots() {
                let activeItemId = document.querySelector(".js-main-slider.show").getAttribute('data-index');
                let allDots = document.querySelectorAll('.js-dot');
                
                allDots.forEach(function(item){
                    item.classList.remove('active');
                });
                
                document.querySelector('.js-dot[data-index = "'+ activeItemId + '"]').classList.add("active");
            }
            
            arrowNext.addEventListener('click', function() {
                showSliderNext(slider.querySelector('.js-main-slider.show'), "show");
                showSliderNext(slider.querySelector('.js-main-slider.next'), "next");
                showSliderNext(slider.querySelector('.js-main-slider.prev'), "prev");
                changeDots();
            });
            
            arrowPrev.addEventListener('click', function() {
                showSliderPrev(slider.querySelector('.js-main-slider.show'), "show");
                showSliderPrev(slider.querySelector('.js-main-slider.next'), "next");
                showSliderPrev(slider.querySelector('.js-main-slider.prev'), "prev");
                changeDots();
            });

            var startPointX;
            var startPointY;
            slider.addEventListener("touchstart", function(event) {
                startPointX = event.changedTouches[0].screenX;
                startPointY = event.changedTouches[0].screenY;
            }, {passive: true});
            slider.addEventListener("touchend", function(event){
                var endPointX = event.changedTouches[0].screenX;
                var endPointY = event.changedTouches[0].screenY;
                
                if(startPointX - endPointX > 40) {
                    arrowNext.click();
                } else if(endPointX - startPointX > 40) {
                    arrowPrev.click();
                }
            }, false);
        }
    }
    

    initialSliderMain();
    
    
    document.addEventListener('click', function(e){
        let elem = e.target;
        
        if(elem.closest('.js-dot')) {
            let allItems = document.querySelectorAll('.js-main-slider');
            
            document.querySelectorAll('.js-dot').forEach(function(item){
                item.classList.remove('active');
            });
            
            let mainItem = document.querySelectorAll('.js-main-slider')
            mainItem.forEach(function(item){
                item.classList.remove('show');
                item.classList.remove('next');
                item.classList.remove('prev');
                // item.classList.remove('next-animation');
                // item.classList.remove('prev-animation');
            });
            
            let index = elem.getAttribute('data-index');
            let activeElem = document.querySelector('.js-main-slider[data-index= "'+ index + '"]');
            activeElem.classList.add('show');
            activeElem.classList.add('next-animation');
            
            if(activeElem.nextElementSibling == null) {
                allItems[0].classList.add("next");
            }else {
                activeElem.nextElementSibling.classList.add('next');
            }
            
            if(activeElem.previousElementSibling == null) {
                allItems[allItems.length - 1].classList.add("prev");
            }else {
                activeElem.previousElementSibling.classList.add('prev')
            }
            
            elem.classList.add('active');
        }
    });
    
    // /SLider
    
    
    // Burger and search and noty
    
    document.addEventListener('click', function(e){
        let elem = e.target;
        let mobileMenu = document.querySelector('.header__nav');
        
        if(elem.closest('.header-burger')) {
            
            if(mobileMenu) {
                mobileMenu.classList.toggle("active");
                document.querySelector('.header-burger').classList.toggle('active');
            }
        }
        
        if(!elem.closest(".header__nav") && !elem.closest(".header-burger")) {
            mobileMenu.classList.remove("active");
            document.querySelector('.header-burger').classList.remove('active');
        }
        
        if(elem.closest('.header__button-search')) {
            document.querySelector('.header__search-tablet').classList.toggle("active");
            elem.closest('.header__button-search').classList.toggle('active');
        }
        
        if(!elem.closest(".header__button-search") && !elem.closest('.header__search-tablet')) {
            document.querySelector('.header__search-tablet').classList.remove("active");
            document.querySelector('.header__button-search').classList.remove("active");
        }
        
        let notyWrapper = document.querySelector('.js-noty-wrapper');
        let notyButton = document.querySelectorAll('.js-noty-button');
        
        if(elem.closest('.js-noty-button')) {
            elem.closest('.js-noty-button').classList.toggle('active');
            notyWrapper.classList.toggle('active');
            notyWrapper.querySelector(".header__noty__list").scrollTop = 0;
        }
        
        if(!elem.closest('.js-noty-button') && !elem.closest(".js-noty-wrapper")) {
            notyButton.forEach((item) => {
                item.classList.remove('active');
            });
            notyWrapper.querySelector(".header__noty__list").scrollTop = 0;
            notyWrapper.classList.remove('active');
        }
    });
    
    // //Burger and search and noty
    
    // Show menu 
    
    let wrapperToTop = document.querySelector('.header__list');
    let compare = document.querySelector('.compare__wrapper');
    
    var lastScrollTop = 0;
    
    document.addEventListener('scroll', function(){
        let pageOffset = window.pageYOffset;
        
        if(window.innerWidth < 577){
            if(window.scrollY > 80) {
                if(pageOffset > lastScrollTop) {
                    wrapperToTop.classList.add('hide');

                    if(compare)
                        compare.classList.add('show');
                }else {
                    wrapperToTop.classList.remove('hide');

                    if(compare)
                        compare.classList.remove('show');
                }
            }
        }
        
        lastScrollTop = pageOffset;
    });
    
    // Show menu 
    
    
    // Drop
    document.addEventListener('click', function(e){
        let element = e.target;
        var dropList = document.querySelectorAll('.js-drop-item');
        
        if(element.closest('.js-drop-button')){
            let isActive = element.closest('.js-drop-item').classList.contains('active')? true: false;
            
            dropList.forEach(item => {item.classList.remove('active')});
            
            if(isActive)
                element.closest('.js-drop-item').classList.remove('active');
            else
                element.closest('.js-drop-item').classList.add('active');
        }
        
        if(element.closest('.js-drop-contains')){
            let dropListContainer = element.closest('.js-drop-item');
            let dropItems = dropListContainer.querySelectorAll('.js-drop-contains');
            
            dropItems.forEach(item => {item.classList.remove('active')});
            element.closest('.js-drop-contains').classList.add('active');
            let innerContent = element.closest('.js-drop-contains').innerHTML;
            let dropInput = dropListContainer.querySelector('.js-drop-input');
            let dropButton = dropListContainer.querySelector('.js-drop-button .text');
            
            if(dropInput) {
                dropInput.value = innerContent;
            }
            
            if(dropButton) {
                dropButton.innerHTML = innerContent;
            }
            
            // close dropdown
            dropListContainer.classList.remove('active');
        }
    });
    
    document.querySelector('body').addEventListener('click', function(event){
        if(!event.target.closest('.js-drop-item')) {
            document.querySelectorAll('.js-drop-item').forEach(function(item){
                item.classList.remove('active');
            }); 
        }
    });
    
    // //Drop
    
    
     // Infinty slider
    
    let arrowsInfinity = document.querySelectorAll('.js-arrow-infinity');
    
    function initialInfinitySlider() {
        for(var i = 0;arrowsInfinity.length > i; i++) {
            let slider = arrowsInfinity[i].closest(".slider-infinity");
            let arrowNext = arrowsInfinity[i].querySelector('.next');
            let arrowPrev = arrowsInfinity[i].querySelector('.prev');
            let sliderList = slider.querySelector('.js-infinity-slider-list');
            
            
            var checkSlider = true;
            
            arrowNext.addEventListener('click', function() {
                
                setTimeout(() => {
                    checkSlider = true;
                }, 400);
                
                if(!checkSlider) {
                    return;
                }
                
                checkSlider = false;
                
                let itemShow = slider.querySelector('.js-slider-item-infinity.show');
                itemShow.nextElementSibling.classList.add('show');
                itemShow.classList.remove('show');
                
                setTimeout(function(){
                    let newElem = itemShow;
                    itemShow.remove();
                    sliderList.append(newElem);
                },390);
                
            });
            
            arrowPrev.addEventListener('click', function() {
                setTimeout(() => {
                    checkSlider = true;
                }, 400);
                
                if(!checkSlider) {
                    return;
                }
                
                checkSlider = false;
                
                let itemShow = slider.querySelector('.js-slider-item-infinity.show');
                let lastElem = sliderList.lastElementChild;

                sliderList.prepend(lastElem);
                
                setTimeout(function(){
                    itemShow.previousElementSibling.classList.add('show');
                    itemShow.classList.remove('show');
                },20);
            });
            
            var startPointX;
            var startPointY;
            slider.addEventListener("touchstart", function(event) {
                startPointX = event.changedTouches[0].screenX;
                startPointY = event.changedTouches[0].screenY;
            }, {passive: true});
            slider.addEventListener("touchend", function(event){
                var endPointX = event.changedTouches[0].screenX;
                var endPointY = event.changedTouches[0].screenY;
                
                if(startPointX - endPointX > 40) {
                    arrowNext.click();
                } else if(endPointX - startPointX > 40) {
                    arrowPrev.click();
                }
            }, false);
        }
    }
    
    initialInfinitySlider();
    
    // //Infinty slider
    
    // SLider
    
    let arrows = document.querySelectorAll(".js-arrows");
    
     function initialSlider() {
        for(var i = 0;arrows.length > i; i++) {
            let slider = arrows[i].closest(".slider");
            let arrowNext = arrows[i].querySelector('.next');
            let arrowPrev = arrows[i].querySelector('.prev');
            let allItems = slider.querySelectorAll('.js-slider-item').length;
            
            if(allItems < 2) {
                arrowNext.classList.add("disabled");
            }
            
            arrowNext.addEventListener('click', function() {
                let itemShow = slider.querySelector('.js-slider-item.show');
                
                if(slider.querySelector('.js-slider-item.show').nextElementSibling == null) {
                    return;
                }
                
                arrowPrev.classList.remove('disabled');
                
                itemShow.nextElementSibling.classList.add('show');
                itemShow.classList.remove('show');
                
                if(slider.querySelector('.js-slider-item.show').nextElementSibling == null) {
                    arrowNext.classList.add('disabled');
                }
            });
            
            arrowPrev.addEventListener('click', function() {
                let itemShow = slider.querySelector('.js-slider-item.show');
                
                if(slider.querySelector('.js-slider-item.show').previousElementSibling == null) {
                    return;
                }

                arrowNext.classList.remove('disabled');
                
                itemShow.previousElementSibling.classList.add('show');
                itemShow.classList.remove('show');
                
                if(slider.querySelector('.js-slider-item.show').previousElementSibling == null) {
                    arrowPrev.classList.add('disabled');
                }
            });

            var startPointX;
            var startPointY;
            slider.addEventListener("touchstart", function(event) {
                startPointX = event.changedTouches[0].screenX;
                startPointY = event.changedTouches[0].screenY;
            }, {passive: true});
            slider.addEventListener("touchend", function(event){
                var endPointX = event.changedTouches[0].screenX;
                var endPointY = event.changedTouches[0].screenY;
                
                if(startPointX - endPointX > 40) {
                    arrowNext.click();
                } else if(endPointX - startPointX > 40) {
                    arrowPrev.click();
                }
            }, false);
        }
    }
    
    let allButtonPlan = document.querySelectorAll('.js-button-plan');
    
    allButtonPlan.forEach(function(item){
        item.addEventListener('click', function(){
            let dataIndex = item.getAttribute('data-index');
            let allPopupItem = document.querySelectorAll('.product-page__plan-item.js-slider-item');
            
            allPopupItem.forEach(function(item){
                item.classList.remove("show");
            });
            
            let activeItem = document.querySelector('.product-page__plan-item.js-slider-item[data-index = "' + dataIndex + '"]');
            activeItem.classList.add('show');
            
            let wraperrPopupPlan = document.querySelector('.popup-full-screen[data-target = "full-screen-plan"]');
            let buttonPrev = wraperrPopupPlan.querySelector('.popup-button.prev');
            let buttonNext = wraperrPopupPlan.querySelector('.popup-button.next');
            
            if(dataIndex == allPopupItem.length) {
                buttonPrev.classList.remove('disabled');
                buttonNext.classList.add('disabled');
            }
            
            if(dataIndex > 1 && dataIndex < allPopupItem.length) {
                buttonPrev.classList.remove('disabled');
                buttonNext.classList.remove('disabled');
            }
        });
    });

    initialSlider();
    
    // More info
        
      function showMoreInfo() {
            
        let info = document.querySelectorAll('.js-item .js-content div');
        let content = document.querySelectorAll('.js-item .js-content');
        let moreButton = document.querySelectorAll('.js-item .js-more');
        
        if(info) {
            for(var i = 0; info.length > i; i++) {
                if(content[i].scrollHeight > content[i].clientHeight) {
                    moreButton[i].classList.add('show');
                }else {
                    moreButton[i].classList.remove('show');
                }
            }
        }
    }
        
    showMoreInfo();
    document.showMoreInfo = function() {
        showMoreInfo();
    };
    
    window.addEventListener('resize', function(){
        showMoreInfo();
    });
    

    
    document.addEventListener('click', function(e){
        let elem = e.target;
        
        if(elem.closest(".js-more")) {
            let wrapper = elem.closest(".js-more").closest('.js-item');
            let firstElem = wrapper.querySelector(".reviews__header").cloneNode(true);
            let secondElem = wrapper.querySelector(".reviews__caption").cloneNode(true);
            let thirdElem = wrapper.querySelector(".reviews__text").cloneNode(true);
            document.querySelector('.popup-reviews .reviews__item').append(firstElem);
            document.querySelector('.popup-reviews .reviews__item').append(secondElem);
            document.querySelector('.popup-reviews .reviews__item').append(thirdElem);
        }
    });
    
    // /More info
    
    // Popup
        
    // let mainButton = document.querySelectorAll('.js-button');
    // let htmlOverflow = document.querySelector('html');
    
    // for(var i = 0; mainButton.length > i; i++) {
    //     if(mainButton[i] !== null) {
            
    //         mainButton[i].addEventListener('click', function(){
    //             let getData = this.getAttribute('data-target');
    //             let popup = document.querySelector('.popup[data-target = ' + getData + ']');
    //             popup.classList.add('active');
    //             htmlOverflow.classList.add('overflow');
    //         });
    //     }
    // }
    
    document.addEventListener('click', function(e){
        let elem = e.target;
        let popupActive = document.querySelector('.popup.active');
        let popupReviews = document.querySelector(".popup-reviews").closest('.popup.active');
        let popupMap = document.querySelector('.popup-full-map.active');
        let htmlOverflow = document.querySelector('html');
        
        if(elem.closest('.js-button')) {
            let getData = elem.closest('.js-button').getAttribute('data-target');
            let popup = document.querySelector('.popup[data-target = ' + getData + ']');
            popup.classList.add('active');
            htmlOverflow.classList.add('overflow');
        }
        
        if(elem.closest('.js-close')){
            if(popupReviews) {
                popupReviews.querySelector('.reviews__header').remove();
                popupReviews.querySelector('.reviews__caption').remove();
                popupReviews.querySelector('.reviews__text').remove();
            }
            
            if(popupActive) {
                popupActive.classList.remove('active');
                htmlOverflow.classList.remove('overflow');
            }
            
            if(popupMap) {
                document.map_popup.remove();
            }
        }
        
        if(!elem.closest(".popup__wrapper") && !elem.closest(".js-button") && !elem.closest('.js-close') && !elem.closest('.popup-button') && !elem.closest('.footer-callback__form') && !elem.closest('.js-filter') && !elem.closest(".js-filter-drop")) {
            if(popupActive) {
                popupActive.classList.remove('active');
                htmlOverflow.classList.remove('overflow');
            }
            if(popupReviews) {
                popupReviews.querySelector('.reviews__header').remove();
                popupReviews.querySelector('.reviews__caption').remove();
                popupReviews.querySelector('.reviews__text').remove();
            }
            if(popupMap) {
                document.map_popup.remove();
            }
        }
    });
    
    // //Popup
    
    // Pre catalog Filter 
    
    document.addEventListener('click', function(e){
        let item = e.target;
        let allWrappers = document.querySelectorAll('.js-filter-drop');
        let allItems = document.querySelectorAll('.js-filter');
        let htmlOVerflow = document.querySelector('html');
        
        if(item.closest('.js-filter')) {
            let target = item.closest('.js-filter').getAttribute('data-target');
            let filterWrapper = document.querySelector('.js-filter-drop[data-target = ' + target + ']');
            let activeElem = item.closest('.js-filter');
            
            if(target == "full-filter") {
                htmlOVerflow.classList.add('overflow');
            }
            
            if(filterWrapper.classList.contains("active")){
                filterWrapper.classList.remove('active');
                activeElem.classList.remove('active');
            }else {
                
                for(var i = 0; i < allWrappers.length; i++) {
                    allWrappers[i].classList.remove('active');
                }
                
                 filterWrapper.classList.add('active');
                 activeElem.classList.add('active');
            }
        }
        
        if(!item.closest('.js-filter') && !item.closest(".js-filter-drop")) {
            
            if(allWrappers) {
                for(var i = 0; i < allWrappers.length; i++) {
                    allWrappers[i].classList.remove('active');
                    allItems[i].classList.remove("active");
                }
            }
        }
        
        let catalogMoreItem = document.querySelector('.js-catalog-more-item');
        
        if(item.closest('.js-catalog-more')) {
            item.closest('.js-catalog-more').classList.toggle('active');
            catalogMoreItem.classList.toggle('active');
        }
        
        if(!item.closest('.js-catalog-more-item') && !item.closest('.js-catalog-more')) {
            if(item.closest('.js-catalog-more')) {
                item.closest('.js-catalog-more').classList.remove('active');
                catalogMoreItem.classList.remove('active');
            }
        }
        
        if(item.closest(".js-catalog-back")) {
            htmlOVerflow.classList.remove('overflow');
            document.querySelector('.js-filter-drop[data-target = full-filter ]').classList.remove('active');
            document.querySelector('.js-filter[data-target = full-filter ]').classList.remove('active');
        }
    });
    
    // Pre catalog filter 
    
    // Show product img
    
    let allImg = document.querySelectorAll('.js-image');
    let generalImg = document.querySelector('.js-general-image img');
    let buttonPrev = document.querySelector('.js-image-button-prev');
    let buttonNext = document.querySelector('.js-image-button-next');
    let setAllNumber = document.querySelector('.js-slider-number .all');
    let generalList = document.querySelector('.product-page__img-list'); 
    
    function setCurrentSlideIndex(item) {
        let slider = item.closest('.slider');
        let currentSlideIndex = slider.querySelector('.js-image.active').getAttribute('data-index');
        item.innerHTML = currentSlideIndex;
    }
    
    document.querySelectorAll('.js-slider-number .current').forEach(function(item){
        setCurrentSlideIndex(item);
    });
    
    function scrollListNext(activeItem) {
        let counterElem = activeItem.getAttribute('data-index');
        let itemWidth = activeItem.offsetWidth;
        
        generalList.scrollLeft = itemWidth * counterElem;
    }
    
    function scrollListPrev(activeItem) {
        let counterElem = activeItem.getAttribute('data-index');
        let itemWidth = activeItem.offsetWidth;
        
        generalList.scrollLeft = itemWidth * (counterElem - 1);
    }
    
    function changeImg(item) {
        let style = item.getAttribute('src');
        generalImg.setAttribute('src', style);
    }
    
    if(generalImg) {
        if(setAllNumber)
            setAllNumber.innerHTML = allImg.length;
            
        allImg.forEach(function(item){
            item.addEventListener('click', function(){
                allImg.forEach(function(item){
                    item.classList.remove("active");
                });
                item.classList.add('active');
                let getIndex = item.getAttribute('data-index');
                
                buttonNext.classList.remove('disabled');
                buttonPrev.classList.remove('disabled');
                
                if(getIndex == 1) {
                    buttonPrev.classList.add('disabled');
                }
                
                if(getIndex == allImg.length) {
                    buttonNext.classList.add('disabled');
                }
                
                changeImg(item.querySelector('img'));
                
                document.querySelectorAll('.js-slider-number .current').forEach(function(item){
                    setCurrentSlideIndex(item);
                });
            });
        });
        
        if(buttonPrev){
            buttonPrev.addEventListener('click', function(){
                let showImg = document.querySelector('.js-image.active');
                
                if(!showImg.previousElementSibling) {
                    return;
                }
                
                scrollListPrev(showImg.previousElementSibling);
                
                showImg.previousElementSibling.classList.add("active");
                showImg.classList.remove("active");
                
                changeImg(showImg.previousElementSibling.querySelector('img'));
                
                document.querySelectorAll('.js-slider-number .current').forEach(function(item){
                    setCurrentSlideIndex(item);
                });
                
                buttonNext.classList.remove("disabled");
                
                if(showImg.previousElementSibling.previousElementSibling == null) {
                    buttonPrev.classList.add('disabled');
                }
            });
            
            buttonNext.addEventListener("click", function(){
                let showImg = document.querySelector('.js-image.active');

                if(!showImg.nextElementSibling) {
                    return;
                }

                scrollListNext(showImg);
                
                showImg.nextElementSibling.classList.add("active");
                showImg.classList.remove("active");
                
                changeImg(showImg.nextElementSibling.querySelector('img'));
                
                document.querySelectorAll('.js-slider-number .current').forEach(function(item){
                    setCurrentSlideIndex(item);
                });
                
                buttonPrev.classList.remove("disabled");
                
                if(showImg.nextElementSibling.nextElementSibling == null) {
                    buttonNext.classList.add('disabled');
                }
                
            });
            
            var startPointX;
            var startPointY;
            document.querySelector(".js-general-image").addEventListener("touchstart", function(event) {
                startPointX = event.changedTouches[0].screenX;
                startPointY = event.changedTouches[0].screenY;
            }, {passive: true});
            document.querySelector(".js-general-image").addEventListener("touchend", function(event){
                var endPointX = event.changedTouches[0].screenX;
                var endPointY = event.changedTouches[0].screenY;
                
                if(startPointX - endPointX > 40) {
                    buttonNext.click();
                } else if(endPointX - startPointX > 40) {
                    buttonPrev.click();
                }
            }, false);
        
        }
    }
    
    // //Show product img
    
    // Check block
    
    document.addEventListener('click', function(e){
        let item = e.target;
        let checked = [];
        
        if(item.closest('.input-checkbox')) {
            let checkboxWrapper = item.closest('.general-voting__form');
            let allCheckboxInput = checkboxWrapper.querySelectorAll('.input-checkbox');
            let formButton = checkboxWrapper.querySelector('.main-button');
            
            allCheckboxInput.forEach((item) => {
                if(item.checked) {
                    checked.push(true);
                }else {
                    checked.push(false);
                }
            });
            
            if(checked.includes(true)){
                formButton.classList.remove('disabled');
            }else {
                formButton.classList.add('disabled');
            }
        }
    });
    
    // Check block 
    
    // Hover catalog button
    
    let catalogButton = document.querySelector('.catalog-filter__form .catalog__filter-button');
    
    if(catalogButton) {
        catalogButton.addEventListener('mouseover', function(){
            let container = this.closest('.catalog-filter__form');
            container.classList.add('hide-border');
        });
        
        catalogButton.addEventListener('mouseout', function(){
            let container = this.closest('.catalog-filter__form');
            container.classList.remove('hide-border');
        });
    }
    
    // //Hover catalog button
    
    // Hide category links
    
    let allLitemCategory = document.querySelectorAll('.js-catagory-links-item');
    
    allLitemCategory.forEach((item) => {
        allLinksLength = item.querySelectorAll(".js-sub-link").length;
        
        if(allLinksLength <= 6) {
            item.querySelector('.js-category-button').classList.add('hide');
        }
    });
    
     // //Hide category links
     
     // Catalog mobile
     
     function showCatalogLists() {
        let wrapper = document.querySelectorAll('.catalog-filter__drop');
        wrapper.forEach(function(item) {
            function moveNext() {
                let activeItem = item.querySelector(".wrapper.mobile-active");
                
                if(activeItem.nextElementSibling == null) {
                    return;
                }
                
                activeItem.nextElementSibling.classList.add("mobile-active");
                activeItem.classList.remove('mobile-active');
            }
            
            function movePrev() {
                let activeItem = item.querySelector(".wrapper.mobile-active");
                
                if(activeItem.previousElementSibling == null) {
                    return;
                }
                
                activeItem.previousElementSibling.classList.add("mobile-active");
                activeItem.classList.remove('mobile-active');
            }
            
            var startPointX;
            var startPointY;
            item.addEventListener("touchstart", function(event) {
                startPointX = event.changedTouches[0].screenX;
                startPointY = event.changedTouches[0].screenY;
            }, {passive: true});
            item.addEventListener("touchend", function(event){
                var endPointX = event.changedTouches[0].screenX;
                var endPointY = event.changedTouches[0].screenY;
                
                if(startPointX - endPointX > 40) {
                    moveNext();
                } else if(endPointX - startPointX > 40) {
                    movePrev();
                }
            }, false);
        });
     }
     
     showCatalogLists();
     
     // Catalog mobile 
     
    let allMapBUttons = document.querySelectorAll('.js-button-map');
    
    if(allMapBUttons) {
        allMapBUttons.forEach(function(item) {
            item.addEventListener("click", function(event){
                setTimeout(() => {
                    document.map_popup = new mapboxgl.Map({
                        container: 'general__map_popup',
                        style: 'mapbox://styles/mapbox/streets-v11',
                        center: [document.map.getCenter().lng,document.map.getCenter().lat],
                        zoom: document.map.getZoom() + 1,
                        minZoom: document.map.getMinZoom(),
                    });
    
                    document.map_popup.on('load', function() {
                        document.map_popup.getStyle().layers.forEach(function(thisLayer){
                            if(thisLayer.type == 'symbol'){
                                document.map_popup.setLayoutProperty(thisLayer.id, 'text-field', ['get','name_ru'])
                            }
                        });
                    })

    
                        currentMarkers.forEach(function(item) {
                            var lng = item._lngLat.lng;
                            var lat = item._lngLat.lat;
                            var marker = new mapboxgl.Marker()
                                        .setLngLat([lng, lat])
                                        .setPopup(item._popup)
                                        .addTo(document.map_popup)
                        });
                }, 1);
            });
        });
    }
});

window.addEventListener("DOMContentLoaded", function() {
    [].forEach.call( document.querySelectorAll('input[type="tel"]'), function(input) {
    var keyCode;
    function mask(event) {
        event.keyCode && (keyCode = event.keyCode);
        var pos = this.selectionStart;
        if (pos < 3) event.preventDefault();
        var matrix = "+38(___)-___-____",
            i = 0,
            def = matrix.replace(/\D/g, ""),
            val = this.value.replace(/\D/g, ""),
            new_value = matrix.replace(/[_\d]/g, function(a) {
                return i < val.length ? val.charAt(i++) || def.charAt(i) : a
            });
        i = new_value.indexOf("_");
        if (i != -1) {
            i < 5 && (i = 3);
            new_value = new_value.slice(0, i)
        }
        var reg = matrix.substr(0, this.value.length).replace(/_+/g,
            function(a) {
                return "\\d{1," + a.length + "}"
            }).replace(/[+()]/g, "\\$&");
        reg = new RegExp("^" + reg + "$");
        if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) this.value = new_value;
        if (event.type == "blur" && this.value.length < 5)  this.value = ""
    }
  
    input.addEventListener("input", mask, false);
    input.addEventListener("focus", mask, false);
    input.addEventListener("blur", mask, false);
    input.addEventListener("keydown", mask, false)
  
  });
  
});