<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Widget</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

    </style>
</head>

<body>

    <div class="widget">
        <div class="card">

            <h2 class="title">
                Обратная связь
            </h2>

            <form id="ticket-form">
                @csrf

                <div>
                    <div class="label">Имя</div>
                    <input class="input" type="text" name="name" required>
                </div>

                <div style="display:flex; gap:10px; margin-top:12px;">
                    <div style="flex:1;">
                        <div class="label">Email</div>
                        <input class="input" type="email" name="email" required>
                    </div>
                    <div style="flex:1;">
                        <div class="label">Телефон</div>
                        <input class="input" type="text" name="phone" placeholder="+7..." required>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <div class="label">Тема</div>
                    <input class="input" type="text" name="subject" required>
                </div>

                <div style="margin-top:12px;">
                    <div class="label">Сообщение</div>
                    <textarea class="input" name="message" rows="3" required></textarea>
                </div>

                <div style="margin-top:12px;">
                    <div class="label">Файлы</div>
                    <input type="file" name="files[]" multiple style="margin-top:6px; font-size:12px;">
                </div>

                <div style="margin-top:16px;">
                    <button class="btn" type="submit" id="submit-btn">
                        Отправить
                    </button>
                </div>
            </form>

            <div id="response-msg" class="msg" style="display:none;"></div>
        </div>
    </div>

    <script>
        const form = document.getElementById('ticket-form');
        const btn = document.getElementById('submit-btn');
        const msgBox = document.getElementById('response-msg');

        const params = new URLSearchParams(window.location.search);
        const projectId = params.get('project_id');

        // 🔹 Очистка ошибок
        function clearErrors() {
            document.querySelectorAll('.input').forEach(el => {
                el.classList.remove('input-error');
            });

            document.querySelectorAll('.error-text').forEach(el => el.remove());
        }

        // 🔹 Показ ошибок
        function showErrors(errors) {
            Object.entries(errors).forEach(([field, messages]) => {
                const input = form.querySelector(`[name="${field}"]`);
                if (!input) return;

                input.classList.add('input-error');

                const error = document.createElement('div');
                error.className = 'error-text';
                error.innerText = messages[0];

                input.parentNode.appendChild(error);
            });
        }

        // 🔹 Очистка ошибки при вводе
        form.querySelectorAll('.input').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('input-error');
                const err = input.parentNode.querySelector('.error-text');
                if (err) err.remove();
            });
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            clearErrors();

            const formData = new FormData(form);

            if (projectId) {
                formData.append('project_id', projectId);
            }

            btn.disabled = true;
            btn.innerText = 'Отправка...';
            msgBox.style.display = 'none';

            try {
                const response = await fetch('/api/tickets/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    form.innerHTML = `
                <div style="text-align:center; padding:20px;">
                    <div style="font-size:18px; margin-bottom:10px;">✅</div>
                    <div style="font-weight:500;">Заявка отправлена</div>
                    <div style="font-size:13px; color:#6b7280; margin-top:5px;">
                        Мы свяжемся с вами
                    </div>
                </div>
            `;
                } else {
                    if (result.errors) {
                        showErrors(result.errors);
                    }

                    msgBox.innerText = result.message || 'Ошибка отправки';
                    msgBox.className = 'msg error';
                    msgBox.style.display = 'block';
                }

            } catch (e) {
                msgBox.innerText = 'Ошибка сети';
                msgBox.className = 'msg error';
                msgBox.style.display = 'block';
            }

            btn.disabled = false;
            btn.innerText = 'Отправить';
        });
    </script>

</body>

</html>