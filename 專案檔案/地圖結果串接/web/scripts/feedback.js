document.querySelectorAll('.feedback-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.feedback-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});

function sendFeedback() {
  const suggestions = document.getElementById('suggestions').value.trim();
  const thoughts = document.getElementById('thoughts').value.trim();
  const verify = emailVerify('email');
  if (verify !== '') {
    showAlert('red', verify);
    return;
  }
  if (suggestions === '') {
    showAlert('red', '請輸入建議內容');
    return;
  }
  const formData = new FormData();
  formData.set('email', document.getElementById('email').value);
  formData.set('suggestions', suggestions);
  formData.set('thoughts', thoughts);
  fetch('/member/handler/send-feedback.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showAlert('dark-blue', '我們已收到您的建議');
        document.getElementById('suggestions').value = '';
        document.getElementById('thoughts').value = '';
      } else {
        showAlert('red', data.message);
      }
    })
    .catch(() => {showAlert('red', '提交建議過程中發生非預期的錯誤');});
}