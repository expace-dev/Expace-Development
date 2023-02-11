    let menuLeft = document.getElementById("menu-left");
    let menuMobile = document.getElementById("nav-mobile");

    let menuRight = document.getElementById("menu-right");
    let infoMobile = document.getElementById("info-mobile");

    menuLeft.addEventListener("click", () => {
        if(getComputedStyle(menuMobile).display != "none"){
            menuMobile.style.display = "none";
         } else {
            menuMobile.style.display = "block";
            infoMobile.style.display = "none";
        }
    })

    menuRight.addEventListener("click", () => {
        if(getComputedStyle(infoMobile).display != "none"){
            infoMobile.style.display = "none";
         } else {
            infoMobile.style.display = "block";
            menuMobile.style.display = "none";
        }
    })

    function navigation(){
        if(getComputedStyle(menuMobile).display != "none"){
            menuMobile.style.display = "none";
        } else {
            menuMobile.style.display = "block";
        }
    };

