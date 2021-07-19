var barsNav = document.querySelector('.bars-nav');
var sidebar = document.querySelector('.sidebar');
var sidebarClose = document.querySelector('.sidebar-close');
var adminContent = document.querySelector('.admin-content');

barsNav.addEventListener('click', function(){
    sidebar.style.width = '250px';
    sidebar.style.transition = '0.4s';
    sidebarClose.style.display = 'block';
})

sidebarClose.addEventListener('click', function(){
    sidebar.style.width = '0';
    sidebarClose.style.display = 'none';
})

function onResize(evnt){
    sidebarClose.style.display = 'none';
    if(window.innerWidth > 1250){
        sidebar.style.width = '25%';
        adminContent.style.marginLeft = '25%';
        adminContent.style.width = '75%';
    } else {
        sidebar.style.width = '0';
        adminContent.style.marginLeft = '0';
        adminContent.style.width = '100%';
    }
    return true;
}
window.onresize = onResize;