document.querySelectorAll('.feedback-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.feedback-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});


document.getElementById('submit-btn').addEventListener('click', function() {
  showAlert('dark-blue', '我們已收到您的回饋');
});