addRadioChangeListener('condition');

window.addEventListener('load', function () {
  updateArea('condition');
  preloading = false;
});

function syncToPreferences() {
  updatePreferences('condition', false);
  showAlert('green', '已同步至偏好設定');
}