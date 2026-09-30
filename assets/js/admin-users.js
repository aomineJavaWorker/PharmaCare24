const userTableBody =
    document.getElementById('userTableBody');

const userCount =
    document.getElementById('userCount');

const userMessage =
    document.getElementById('userMessage');

const userModal =
    document.getElementById('userModal');

const openUserModal =
    document.getElementById('openUserModal');

const closeUserModal =
    document.getElementById('closeUserModal');

const cancelUserModal =
    document.getElementById('cancelUserModal');

const userForm =
    document.getElementById('userForm');

const userFormMessage =
    document.getElementById('userFormMessage');


loadUsers();


async function loadUsers() {

    try {

        const response =
            await fetch(
                '../api/users.php'
            );


        if (response.status === 401) {

            window.location.href =
                'login.php';

            return;
        }


        if (response.status === 403) {

            window.location.href =
                'dashboard.php';

            return;
        }


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            throw new Error(
                result.message
            );
        }


        renderUsers(
            result.data
        );


    } catch (error) {

        console.error(error);


        userMessage.textContent =
            'Neizdevās ielādēt lietotājus.';

        userMessage.classList.add(
            'error'
        );
    }
}


function renderUsers(users) {

    userTableBody.innerHTML = '';

    userCount.textContent =
        users.length;


    if (users.length === 0) {

        userMessage.style.display =
            'block';

        userMessage.textContent =
            'Lietotāji nav atrasti.';

        return;
    }


    userMessage.style.display =
        'none';


    users.forEach(function (user) {

        const row =
            document.createElement('tr');


        const roleName =
            user.role === 'admin'
                ? 'Galvenais administrators'
                : 'Moderators';


        row.innerHTML = `

            <td>
                #${user.id}
            </td>

            <td>
                <strong>
                    ${escapeHtml(
                        user.username
                    )}
                </strong>
            </td>

            <td>
                ${roleName}
            </td>

            <td>
                ${formatDate(
                    user.created_at
                )}
            </td>

            <td>
                ${formatDate(
                    user.updated_at
                )}
            </td>
        `;


        userTableBody.appendChild(
            row
        );
    });
}


openUserModal.addEventListener(
    'click',
    function () {

        userForm.reset();

        clearMessage();

        userModal.classList.add(
            'active'
        );

        document.body.style.overflow =
            'hidden';
    }
);


function closeModal() {

    userModal.classList.remove(
        'active'
    );

    document.body.style.overflow =
        '';

    userForm.reset();

    clearMessage();
}


closeUserModal.addEventListener(
    'click',
    closeModal
);


cancelUserModal.addEventListener(
    'click',
    closeModal
);


userModal.addEventListener(
    'click',
    function (event) {

        if (
            event.target === userModal
        ) {

            closeModal();
        }
    }
);


userForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const data = {

            username:
                document
                    .getElementById(
                        'newUsername'
                    )
                    .value
                    .trim(),

            password:
                document
                    .getElementById(
                        'newPassword'
                    )
                    .value,

            password_confirm:
                document
                    .getElementById(
                        'confirmPassword'
                    )
                    .value
        };


        if (
            data.password !==
            data.password_confirm
        ) {

            showMessage(
                'Paroles nesakrīt.',
                'error'
            );

            return;
        }


        try {

            const response =
                await fetch(
                    '../api/users.php',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body:
                            JSON.stringify(
                                data
                            )
                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.success
            ) {

                showMessage(
                    result.message
                    ?? 'Neizdevās izveidot moderatoru.',
                    'error'
                );

                return;
            }


            showMessage(
                result.message,
                'success'
            );


            await loadUsers();


            setTimeout(
                closeModal,
                700
            );


        } catch (error) {

            console.error(error);


            showMessage(
                'Neizdevās sazināties ar serveri.',
                'error'
            );
        }
    }
);


function showMessage(
    message,
    type
) {

    userFormMessage.textContent =
        message;

    userFormMessage.className =
        `form-message ${type}`;
}


function clearMessage() {

    userFormMessage.textContent = '';

    userFormMessage.className =
        'form-message';
}


function formatDate(value) {

    if (!value) {
        return '-';
    }


    return new Date(
        value.replace(
            ' ',
            'T'
        )
    ).toLocaleString(
        'lv-LV'
    );
}


function escapeHtml(value) {

    const element =
        document.createElement('div');

    element.textContent =
        value ?? '';

    return element.innerHTML;
}