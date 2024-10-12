document.querySelectorAll('.feedback-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.feedback-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});


document.getElementById('submit-btn').addEventListener('click', function() {
    // 這裡放你的自訂送出處理邏輯
    alert('自訂送出處理！');
    // 例如，你可以使用 fetch 或 XMLHttpRequest 來手動處理表單送出
});