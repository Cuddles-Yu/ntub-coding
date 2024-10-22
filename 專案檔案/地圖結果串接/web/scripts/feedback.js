document.querySelectorAll('.feedback-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.feedback-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

function sendFeedback() {
  const feedback = document.getElementById('suggestions').value.trim();
  const verify = emailVerify('email');
  if (verify!=='') {
    showAlert('red', verify);
    return;
  }
  if (feedback === '') {
    showAlert('red', '請輸入建議內容');
    return;
  }
  showAlert('dark-blue', '我們已收到您的建議');
}