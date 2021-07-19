window.onscroll = function () { myFunction() };

//var header = document.getElementById("myHeader");
var side_div = document.getElementById("firstd_id");
var foter_id_ = document.getElementById("footer_id");

var foot__ = foter_id_.scrollHeight - foter_id_.scrollTop ; //hauter du footer
var Y = foot__ +400; // position du scroll

function myFunction() {
    console.log(" quand je scroll fais ceci ==>  " + Y);
    console.log(" footer height ===> " + foot__);
    if (window.scrollY > Y) {
        side_div.classList.add("sticky");
    } else {
        side_div.classList.remove("sticky");
    }
}