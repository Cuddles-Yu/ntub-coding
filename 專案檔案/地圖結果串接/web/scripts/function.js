function syncButtonsByID(primaryBtnId, secondaryBtnId) {
    document.getElementById(secondaryBtnId).addEventListener('click', function() {
        document.getElementById(primaryBtnId).click();
    });
}

function copyAttributesByElement(sourceElement, targetElement) {
  for (let attr of sourceElement.attributes) {
    if (attr.name === 'id') continue;
    if (attr.name === 'class') {
      targetElement.classList.add(attr.value);
      continue;
    }
    targetElement.setAttribute(attr.name, attr.value);
  }
}