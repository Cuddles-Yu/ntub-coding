function logoutRequest() {
  fetch('/member/handler/logout.php', {
    method: 'POST',
    credentials: 'same-origin',
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        cancelModal();
        generateLoadingOverlay();
        localStorage.setItem('justLoggedOut', 'true');
        setTimeout(function() {
          window.location.reload(true);
        }, LOADING_DURATION);
      }
    })
    .catch(() => {showAlert('red', '登出失敗，請稍後再試');});
}