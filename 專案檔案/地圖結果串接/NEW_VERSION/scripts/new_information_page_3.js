/* %數值更新 */
function updateValue(id) {
    var slider = document.getElementById(id);
    var output = document.getElementById(id + '_value');
    output.textContent = slider.value + '%';
}