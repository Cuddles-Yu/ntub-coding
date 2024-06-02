document.addEventListener('DOMContentLoaded', () => {
    const adminTable = document.getElementById('adminTable');
    const addAdminButton = document.getElementById('addAdmin');
    const emailInput = document.getElementById('email');
    const roleInputs = document.getElementsByName('role');

    const admins = [
        { email: 'adm@system', added: 'Mon Jan 18 14:42:20 2016', role: '系統管理者' },
        { email: 'user@m2kalan.com', added: 'Wed Jan 27 14:22:42 2016', role: '系統管理者' },
        { email: 'user2@m2kalan.com', added: 'Wed Jan 27 14:23:27 2016', role: '系統協助管理人員' }
    ];

    function renderTable() {
        adminTable.innerHTML = '';
        admins.forEach((admin, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${admin.email}</td>
                <td>${admin.added}</td>
                <td>${admin.role}</td>
                <td class="action-column"><button class="icon-button"><img src="icons/change_password.png" alt="Edit"></button></td>
                <td class="action-column"><button class="icon-button"><img src="icons/edit.png" alt="Edit"></button></td>
                <td class="action-column"><button class="icon-button" data-index="${index}"><img src="icons/delete.png" alt="Delete"></button></td>
            `;
            adminTable.appendChild(row);
        });
    }

    addAdminButton.addEventListener('click', () => {
        const email = emailInput.value;
        if (email == "") {
            return;
        }
        let role = '系統管理者';
        roleInputs.forEach(input => {
            if (input.checked) {
                role = input.nextSibling.textContent.trim();
            }
        });

        const newAdmin = {
            email,
            added: new Date().toString(),
            role
        };
        admins.push(newAdmin);
        renderTable();
        emailInput.value = '';
    });

    adminTable.addEventListener('click', (e) => {
        if (e.target.closest('.icon-button[data-index]')) {
            const index = e.target.closest('.icon-button').dataset.index;
            admins.splice(index, 1);
            renderTable();
        }
    });

    renderTable();
});
