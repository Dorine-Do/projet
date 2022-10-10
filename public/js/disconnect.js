document.addEventListener('DOMContentLoaded', function(){
    const logoutBtn = document.getElementById('logout-btn');
    logoutBtn.onclick = () => {
        google.accounts.id.disableAutoSelect();
    }
});