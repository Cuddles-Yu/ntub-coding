document.addEventListener('DOMContentLoaded', () => {
    const executeCrawlerForm = document.getElementById('execute-crawler-form');
    const scheduleCrawlerForm = document.getElementById('schedule-crawler-form');
    const checkTagsButton = document.getElementById('check-tags');
    const tagsStatusDiv = document.getElementById('tags-status');
    const adminAccountForm = document.getElementById('admin-account-form');
    const adminList = document.getElementById('admin-list');
    const scheduleList = document.getElementById('schedule-list');
    const tagList = document.getElementById('tag-list');
    const refreshDataButton = document.getElementById('refresh-data');
    const dashboardContent = document.getElementById('dashboard-content');

    // Load existing data on page load
    loadAdminList();
    loadScheduleList();
    loadTagList();
    loadDashboard();

    executeCrawlerForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const keyword = document.getElementById('crawler-keyword').value;
        const maxAttempts = document.getElementById('max-attempts').value;

        fetch('/execute-crawler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ keyword, maxAttempts })
        })
        .then(response => response.json())
        .then(data => {
            alert('爬蟲已啟動: ' + data.message);
        })
        .catch(error => console.error('Error:', error));
    });

    scheduleCrawlerForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const keyword = document.getElementById('schedule-keyword').value;
        const maxAttempts = document.getElementById('schedule-attempts').value;
        const scheduleTime = document.getElementById('schedule-time').value;

        fetch('/schedule-crawler', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ keyword, maxAttempts, scheduleTime })
        })
        .then(response => response.json())
        .then(data => {
            alert('爬蟲排程已設定: ' + data.message);
            loadScheduleList();
        })
        .catch(error => console.error('Error:', error));
    });

    checkTagsButton.addEventListener('click', () => {
        fetch('/check-tags')
        .then(response => response.json())
        .then(data => {
            if (data.unclassifiedTags.length > 0) {
                tagsStatusDiv.innerHTML = '<ul>' + data.unclassifiedTags.map(tag => `<li>${tag}</li>`).join('') + '</ul>';
            } else {
                tagsStatusDiv.textContent = '所有標籤均已分類';
            }
        })
        .catch(error => console.error('Error:', error));
    });

    adminAccountForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const email = document.getElementById('admin-email').value;

        fetch('/add-admin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        })
        .then(response => response.json())
        .then(data => {
            alert('管理者帳號已新增: ' + data.message);
            loadAdminList();
        })
        .catch(error => console.error('Error:', error));
    });

    adminList.addEventListener('click', (event) => {
        if (event.target.classList.contains('delete-admin')) {
            const email = event.target.dataset.email;

            fetch('/delete-admin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                alert('管理者帳號已刪除: ' + data.message);
                loadAdminList();
            })
            .catch(error => console.error('Error:', error));
        }
    });

    function loadAdminList() {
        fetch('/list-admins')
        .then(response => response.json())
        .then(data => {
            adminList.innerHTML = data.admins.map(admin => `
                <tr>
                    <td>${admin.email}</td>
                    <td><button class="action-button delete-admin" data-email="${admin.email}">刪除</button></td>
                </tr>
            `).join('');
        })
        .catch(error => console.error('Error:', error));
    }

    function loadScheduleList() {
        fetch('/list-schedules')
        .then(response => response.json())
        .then(data => {
            scheduleList.innerHTML = data.schedules.map(schedule => `
                <tr>
                    <td>${schedule.keyword}</td>
                    <td>${schedule.maxAttempts}</td>
                    <td>${schedule.scheduleTime}</td>
                    <td><button class="action-button delete-schedule" data-id="${schedule.id}">刪除</button></td>
                </tr>
            `).join('');
        })
        .catch(error => console.error('Error:', error));
    }

    function loadTagList() {
        fetch('/list-tags')
        .then(response => response.json())
        .then(data => {
            tagList.innerHTML = data.tags.map(tag => `
                <tr>
                    <td>${tag.name}</td>
                    <td>${tag.category}</td>
                </tr>
            `).join('');
        })
        .catch(error => console.error('Error:', error));
    }

    scheduleList.addEventListener('click', (event) => {
        if (event.target.classList.contains('delete-schedule')) {
            const id = event.target.dataset.id;

            fetch('/delete-schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                alert('排程已刪除: ' + data.message);
                loadScheduleList();
            })
            .catch(error => console.error('Error:', error));
        }
    });

    refreshDataButton.addEventListener('click', () => {
        // 更新資料的邏輯
        loadDashboard();
        alert('資料已更新');
    });

    function loadDashboard() {
        // 模擬儀表板數據加載
        dashboardContent.innerHTML = `
            <div>
                <h3>數據統計</h3>
                <p>總商家數: 100</p>
                <p>總標籤數: 50</p>
                <p>使用者註冊數: 20</p>
            </div>
        `;
    }
});
