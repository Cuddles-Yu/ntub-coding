document.querySelectorAll('.roster-menu').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});
document.querySelectorAll('.roster-page').forEach(page => {
  page.removeAttribute('href');
  page.setAttribute('style', 'cursor:default;');
});


window.addEventListener('load', function () {
  generateStoreSuggestion('tab-content-environmental');
});

document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function() {
      const targetTab = tab.getAttribute('data-tab');
      if (tab.classList.contains('active')) return;
      const targetContent = document.getElementById(targetTab);
      if (!targetContent) return;
      var regenerate = (targetContent.getElementsByClassName('carousel-container').length == 0);
      document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
      document.querySelectorAll('[class^="tab-content"]').forEach(content => content.classList.remove('active'));
      tab.classList.add('active');
      targetContent.classList.add('active');
      if (regenerate) generateStoreSuggestion(targetTab);
    });
  });

});

function generateStoreSuggestion(tabId) {
  var resultsContainer = document.getElementById(tabId);
  resultsContainer.innerHTML = `
    <div style="width:100%;height:550px;align-content:center;">
      <div class="rotating">
        <img src="./images/icon-loading.png" width="50" height="50">
      </div>
      <p style="text-align:center;">正在載入餐廳名冊...</p>
    </div>`;
  const formData = new FormData();
  formData.append('mode', tabId.replaceAll('tab-content-', ''));
  fetch('/struc/store-roster.php', {
    method: 'POST',
    credentials: 'same-origin',
    body: formData
  })
    .then(response => response.text())
    .then(data => {
      resultsContainer.innerHTML = data
    })
    .catch(() => { exceptionAlert('載入餐廳名冊'); });
}