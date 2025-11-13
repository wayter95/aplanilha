(function () {
    'use strict';
    if (localStorage.getItem("ynexdarktheme")) {
        document.querySelector("html").setAttribute("class", "dark")
        document.querySelector("html").setAttribute("data-menu-styles", "dark")
        document.querySelector("html").setAttribute("data-header-styles", "dark")
    }
    if (localStorage.ynexrtl) {
        let html = document.querySelector('html');
        html.setAttribute("dir", "rtl");
    }
    if (localStorage.ynexlayout) {
        let html = document.querySelector('html');
        html.setAttribute("data-nav-layout", "horizontal");
        document.querySelector("html").setAttribute("data-menu-styles", "light")
    }
    if (localStorage.getItem("ynexlayout") == "horizontal") {
        document.querySelector("html").setAttribute("data-nav-layout", "horizontal")
    }
    if (localStorage.loaderEnable == 'true') {
        document.querySelector("html").setAttribute("loader", "enable");
    } else {
        if (!document.querySelector("html").getAttribute("loader")) {
            document.querySelector("html").setAttribute("loader", "disable");
        }
    }

    function localStorageBackup() {

        // if there is a value stored, update color picker and background color
        // Used to retrive the data from local storage
        if (localStorage.primaryRGB) {
            document
                .querySelector("html")
                .style.setProperty("--primary", localStorage.primaryRGB1);
            document
                .querySelector("html")
                .style.setProperty("--primary-rgb", localStorage.primaryRGB);
        }
        if (localStorage.bodyBgRGB) {
            document
              .querySelector("html")
              .style.setProperty("--body-bg", localStorage.bodyBgRGB);
            document
              .querySelector("html")
              .style.setProperty("--dark-bg", localStorage.darkBgRGB);
              document
                .querySelector("html")
                .style.setProperty("--light", localStorage.darkBgRGB);
            let html = document.querySelector("html");
            html.classList.add("dark");
            html.classList.remove("light");
            html.setAttribute("data-menu-styles", "dark");
            html.setAttribute("data-header-styles", "dark");
          }
        if (localStorage.ynexdarktheme) {
            let html = document.querySelector('html');
            html.setAttribute('class', 'dark');
        }
        if (localStorage.ynexlayout) {
            let html = document.querySelector('html');
            let layoutValue = localStorage.getItem('ynexlayout');
            html.setAttribute('data-nav-layout', 'horizontal');
            setTimeout(() => {
                
            }, 5000);
            html.setAttribute('data-nav-style', 'menu-click');
            setTimeout(() => {
               
            }, 5000);
        }
        if (localStorage.ynexnavstyles) {
            let html = document.querySelector('html');
            let navStyles = localStorage.getItem('ynexnavstyles');
            if (navStyles == 'menu-click') {
                html.setAttribute('data-nav-style', 'menu-click');
                localStorage.removeItem("ynexverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'menu-hover') {
                html.setAttribute('data-nav-style', 'menu-hover');
                localStorage.removeItem("ynexverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'icon-click') {
                html.setAttribute('data-nav-style', 'icon-click');
                localStorage.removeItem("ynexverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
            if (navStyles == 'icon-hover') {
                html.setAttribute('data-nav-style', 'icon-hover');
                localStorage.removeItem("ynexverticalstyles");
                html.removeAttribute('data-vertical-style');
            }
        }
        if (localStorage.ynexclassic) {
            let html = document.querySelector('html');
            html.setAttribute('data-page-style', 'classic');
        }
        if (localStorage.ynexmodern) {
            let html = document.querySelector('html');
            html.setAttribute('data-page-style', 'modern');
        }
        if (localStorage.ynexboxed) {
            let html = document.querySelector('html');
            html.setAttribute('data-width', 'boxed');
        }
        if (localStorage.ynexheaderfixed) {
            let html = document.querySelector('html');
            html.setAttribute('data-header-position', 'fixed');
        }
        if (localStorage.ynexheaderscrollable) {
            let html = document.querySelector('html');
            html.setAttribute('data-header-position', 'scrollable');
        }
        if (localStorage.ynexmenufixed) {
            let html = document.querySelector('html');
            html.setAttribute('data-menu-position', 'fixed');
        }
        if (localStorage.ynexmenuscrollable) {
            let html = document.querySelector('html');
            html.setAttribute('data-menu-position', 'scrollable');
        }
        if (localStorage.ynexMenu) {
            let html = document.querySelector('html');
            let menuValue = localStorage.getItem('ynexMenu');
            switch (menuValue) {
                case 'light':
                    html.setAttribute('data-menu-styles', 'light');
                    break;
                case 'dark':
                    html.setAttribute('data-menu-styles', 'dark');
                    break;
                case 'color':
                    html.setAttribute('data-menu-styles', 'color');
                    break;
                case 'gradient':
                    html.setAttribute('data-menu-styles', 'gradient');
                    break;
                case 'transparent':
                    html.setAttribute('data-menu-styles', 'transparent');
                    break;
                default:
                    break;
            }
        }
        if (localStorage.ynexHeader) {
            let html = document.querySelector('html');
            let headerValue = localStorage.getItem('ynexHeader');
            switch (headerValue) {
                case 'light':
                    html.setAttribute('data-header-styles', 'light');
                    break;
                case 'dark':
                    html.setAttribute('data-header-styles', 'dark');
                    break;
                case 'color':
                    html.setAttribute('data-header-styles', 'color');
                    break;
                case 'gradient':
                    html.setAttribute('data-header-styles', 'gradient');
                    break;
                case 'transparent':
                    html.setAttribute('data-header-styles', 'transparent');
                    break;

                default:
                    break;
            }
        }
        if (localStorage.bgimg) {
            let html = document.querySelector('html');
            let value = localStorage.getItem('bgimg');
            html.setAttribute('bg-img', value);
        }
    }
    localStorageBackup()

})();