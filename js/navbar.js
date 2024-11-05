function navbarFunction() {
    var navbar = document.getElementById("navbarLinks");
    var mobileNavbar = document.querySelector(".navbar-mobile");

    // Toggle display of navbar links
    if (navbar.style.display === "block") {
        navbar.style.display = "none";
        mobileNavbar.style.height = "3rem"; // Reset height when hidden
    } else {
        navbar.style.display = "block";
        mobileNavbar.style.height = `max-content`;
    }
}
