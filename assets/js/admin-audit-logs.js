const auditTableBody =
    document.getElementById(
        'auditTableBody'
    );

const auditMessage =
    document.getElementById(
        'auditMessage'
    );


loadAuditLogs();


async function loadAuditLogs() {

    try {

        const response =
            await fetch(
                '../api/audit_logs.php'
            );


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


        renderLogs(
            result.data
        );


    } catch (error) {

        console.error(error);

        auditMessage.textContent =
            'Neizdevās ielādēt darbību žurnālu.';
    }
}


function renderLogs(logs) {

    auditTableBody.innerHTML = '';


    if (logs.length === 0) {

        auditMessage.style.display =
            'block';

        auditMessage.textContent =
            'Darbību žurnāls ir tukšs.';

        return;
    }


    auditMessage.style.display =
        'none';


    logs.forEach(function (log) {

        const row =
            document.createElement('tr');


        row.innerHTML = `

            <td>
                ${formatDate(
                    log.created_at
                )}
            </td>

            <td>
                <strong>
                    ${escapeHtml(
                        log.username
                    )}
                </strong>
            </td>

            <td>
                ${log.role === 'admin'
                    ? 'Administrators'
                    : 'Moderators'
                }
            </td>

            <td>
                ${escapeHtml(
                    log.action
                )}
            </td>

            <td>
                ${escapeHtml(
                    log.entity_type
                )}
            </td>

            <td>
                ${log.entity_id
                    ?? '-'
                }
            </td>

            <td>
                ${escapeHtml(
                    log.details
                    ?? '-'
                )}
            </td>

            <td>
                ${escapeHtml(
                    log.ip_address
                    ?? '-'
                )}
            </td>
        `;


        auditTableBody.appendChild(
            row
        );
    });
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