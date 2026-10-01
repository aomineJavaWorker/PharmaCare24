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

const userModalTitle =
    document.getElementById('userModalTitle');

const userModalDescription =
    document.getElementById(
        'userModalDescription'
    );

const userId =
    document.getElementById('userId');

const newUsername =
    document.getElementById('newUsername');

const userRole =
    document.getElementById('userRole');

const newPassword =
    document.getElementById('newPassword');

const confirmPassword =
    document.getElementById(
        'confirmPassword'
    );

const passwordLabel =
    document.getElementById(
        'passwordLabel'
    );

const confirmPasswordLabel =
    document.getElementById(
        'confirmPasswordLabel'
    );

const passwordHint =
    document.getElementById(
        'passwordHint'
    );

const saveUserButton =
    document.getElementById(
        'saveUserButton'
    );


let usersCache = [];

let currentUserId = null;

let editMode = false;


/* =========================================================
   IELĀDĒJAM LIETOTĀJUS
   ========================================================= */

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


        usersCache =
            result.data ?? [];


        currentUserId =
            Number(
                result.current_user_id
            );


        renderUsers(
            usersCache
        );


    } catch (error) {

        console.error(error);


        userMessage.style.display =
            'block';

        userMessage.textContent =
            'Neizdevās ielādēt lietotājus.';

        userMessage.classList.add(
            'error'
        );
    }
}


/* =========================================================
   TABULA
   ========================================================= */

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
                ? 'Administrators'
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

            <td>

                <div class="table-actions">

                    <button
                        type="button"
                        class="table-action-button edit-user-button"
                    >
                        Rediģēt
                    </button>

                    ${
                        Number(user.id) !==
                        currentUserId
                            ? `
                                <button
                                    type="button"
                                    class="
                                        table-action-button
                                        delete-action-button
                                        delete-user-button
                                    "
                                >
                                    Dzēst
                                </button>
                              `
                            : ''
                    }

                </div>

            </td>
        `;


        const editButton =
            row.querySelector(
                '.edit-user-button'
            );


        editButton.addEventListener(
            'click',
            function () {

                openEditModal(
                    user
                );
            }
        );


        const deleteButton =
            row.querySelector(
                '.delete-user-button'
            );


        if (deleteButton) {

            deleteButton.addEventListener(
                'click',
                function () {

                    deleteUser(
                        user
                    );
                }
            );
        }


        userTableBody.appendChild(
            row
        );
    });
}


/* =========================================================
   CREATE MODAL
   ========================================================= */

openUserModal.addEventListener(
    'click',
    openCreateModal
);


function openCreateModal() {

    editMode = false;


    userForm.reset();

    userId.value = '';


    userRole.value =
        'moderator';


    userModalTitle.textContent =
        'Pievienot lietotāju';


    userModalDescription.textContent =
        'Izveidojiet jaunu administratora vai moderatora kontu.';


    saveUserButton.textContent =
        'Izveidot lietotāju';


    passwordLabel.textContent =
        'Parole *';


    confirmPasswordLabel.textContent =
        'Atkārtot paroli *';


    newPassword.required =
        true;


    confirmPassword.required =
        true;


    passwordHint.style.display =
        'none';


    clearMessage();


    openModal();
}


/* =========================================================
   EDIT MODAL
   ========================================================= */

function openEditModal(user) {

    editMode = true;


    userForm.reset();


    userId.value =
        user.id;


    newUsername.value =
        user.username;


    userRole.value =
        user.role;


    newPassword.value = '';

    confirmPassword.value = '';


    userModalTitle.textContent =
        'Rediģēt lietotāju';


    userModalDescription.textContent =
        `Rediģējiet lietotāja "${user.username}" datus.`;


    saveUserButton.textContent =
        'Saglabāt izmaiņas';


    passwordLabel.textContent =
        'Jaunā parole';


    confirmPasswordLabel.textContent =
        'Atkārtot jauno paroli';


    newPassword.required =
        false;


    confirmPassword.required =
        false;


    passwordHint.style.display =
        'block';


    /*
     * Pašreizējais admins pats sev
     * nevar noņemt administratora lomu.
     */

    if (
        Number(user.id) ===
        currentUserId
    ) {

        userRole.value =
            'admin';

        userRole.disabled =
            true;

    } else {

        userRole.disabled =
            false;
    }


    clearMessage();

    openModal();
}


/* =========================================================
   MODAL
   ========================================================= */

function openModal() {

    userModal.classList.add(
        'active'
    );

    document.body.style.overflow =
        'hidden';
}


function closeModal() {

    userModal.classList.remove(
        'active'
    );


    document.body.style.overflow =
        '';


    userRole.disabled =
        false;


    userForm.reset();

    userId.value = '';


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


document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key === 'Escape' &&
            userModal.classList.contains(
                'active'
            )
        ) {

            closeModal();
        }
    }
);


/* =========================================================
   SAGLABĀŠANA
   ========================================================= */

userForm.addEventListener(
    'submit',
    async function (event) {

        event.preventDefault();


        const data = {

            username:
                newUsername
                    .value
                    .trim(),

            role:
                userRole.disabled
                    ? 'admin'
                    : userRole.value,

            password:
                newPassword.value,

            password_confirm:
                confirmPassword.value
        };


        if (editMode) {

            data.id =
                Number(
                    userId.value
                );

        } else {

            if (
                data.password.length < 8
            ) {

                showMessage(
                    'Parolei jābūt vismaz 8 simbolus garai.',
                    'error'
                );

                return;
            }
        }


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
                        method:
                            editMode
                                ? 'PUT'
                                : 'POST',

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
                    ?? 'Neizdevās saglabāt lietotāju.',
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


/* =========================================================
   DZĒŠANA
   ========================================================= */

async function deleteUser(user) {

    const confirmed =
        confirm(
            `Vai tiešām dzēst lietotāju "${user.username}"?`
        );


    if (!confirmed) {
        return;
    }


    try {

        const response =
            await fetch(
                '../api/users.php',
                {
                    method: 'DELETE',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body:
                        JSON.stringify({
                            id:
                                Number(
                                    user.id
                                )
                        })
                }
            );


        const result =
            await response.json();


        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message
                ?? 'Neizdevās dzēst lietotāju.'
            );

            return;
        }


        await loadUsers();


    } catch (error) {

        console.error(error);

        alert(
            'Neizdevās sazināties ar serveri.'
        );
    }
}


/* =========================================================
   PALĪGFUNKCIJAS
   ========================================================= */

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

    userFormMessage.textContent =
        '';

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