var barsNav = document.querySelector('.bars-nav');
var sidebar = document.querySelector('.sidebar');
var sidebarClose = document.querySelector('.sidebar-close');
var adminContent = document.querySelector('.admin-content');
var sidebarContent = document.querySelector('.sidebar-content');
var rotateGestions = document.querySelector('.rotate-gestions');
var rotateDashboard = document.querySelector('.rotate-dashboard');
var gestionsDive = document.querySelector('.gestions-dive');
var dashboardDive = document.querySelector('.dashboard-dive');
var _allProducts = document.querySelector('._all-products'); 
var _littleProducts = document.querySelector('._little-products');
var _length = _allProducts.id;

barsNav.addEventListener('click', function(){
    sidebar.style.width = '350px';
    sidebar.style.transition = '0.4s';
    sidebarClose.style.display = 'block';
    sidebarContent.style.display = 'block';
})

sidebarClose.addEventListener('click', function(){
    sidebar.style.width = '0';
    sidebarClose.style.display = 'none';
    sidebarContent.style.display = 'none';
})

rotateDashboard.addEventListener('click', function(){
    if(rotateDashboard.classList.contains('down')){
        rotateDashboard.classList.remove('down');
        dashboardDive.style.display = 'none';
        dashboardDive.style.visibility = 'hidden';
    } else {
        rotateDashboard.classList.add('down');
        dashboardDive.style.display = 'block';
        dashboardDive.style.visibility = 'visible';
        rotateGestions.classList.remove('down');
        gestionsDive.style.display = 'none';
        gestionsDive.style.visibility = 'hidden';
    }
});

rotateGestions.addEventListener('click', function(){
    if(rotateGestions.classList.contains('down')){
        rotateGestions.classList.remove('down');
        gestionsDive.style.display = 'none';
        gestionsDive.style.visibility = 'hidden';
    } else {
        rotateGestions.classList.add('down');
        gestionsDive.style.display = 'block';
        gestionsDive.style.visibility = 'visible';
        rotateDashboard.classList.remove('down');
        dashboardDive.style.display = 'none';
        dashboardDive.style.visibility = 'hidden';
    }
});

_allProducts.addEventListener('click', function(){
    for(var i = 5; i < _length; i++){
        document.querySelector('#_tr' + i).classList.remove('_none');
        _allProducts.classList.add('_none');
        _littleProducts.classList.remove('_none');
    }
});

_littleProducts.addEventListener('click', function(){
    for(var i = 5; i < _length; i++){
        document.querySelector('#_tr' + i).classList.add('_none');
        _allProducts.classList.remove('_none');
        _littleProducts.classList.add('_none');
    }
})

function onResize(evnt){
    sidebarClose.style.display = 'none';
    if(window.innerWidth > 1250){
        sidebar.style.width = '25%';
        adminContent.style.marginLeft = '25%';
        adminContent.style.width = '75%';
        sidebarContent.style.display = 'block';
    } else {
        sidebar.style.width = '0';
        adminContent.style.marginLeft = '0';
        adminContent.style.width = '100%';
        sidebarContent.style.display = 'none';
    }
    return true;
}
window.onresize = onResize;