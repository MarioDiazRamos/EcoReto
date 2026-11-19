"use strict";
const menuButton = document.querySelector("#menu-toggle");
const mobileMenu = document.querySelector("#mobile-menu");

if (menuButton && mobileMenu) {
  menuButton.addEventListener("click", () => {
    mobileMenu.hidden = !mobileMenu.hidden;
    menuButton.setAttribute("aria-expanded", String(!mobileMenu.hidden));
  });
}
const form = document.querySelector("#participation-preview");
const previewButton = document.querySelector("#preview-state");
if (form && previewButton) {
  form.addEventListener("submit", (event) => event.preventDefault());
  previewButton.disabled = false;
  previewButton.addEventListener("click", () => {
    if (form.reportValidity()) window.location.assign("/pendiente.php");
  });
}
