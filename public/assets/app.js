"use strict";
const menuButton = document.querySelector("#menu-toggle");
const mobileMenu = document.querySelector("#mobile-menu");

if (menuButton && mobileMenu) {
  menuButton.addEventListener("click", () => {
    mobileMenu.hidden = !mobileMenu.hidden;
    menuButton.setAttribute("aria-expanded", String(!mobileMenu.hidden));
  });
}

