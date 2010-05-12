//Toggles element with name "n" between visible and hidden
function toggle_element(n)
{
    var e = document.getElementById(n);
    e.style.display = (e.style.display ? '' : 'none');
}
