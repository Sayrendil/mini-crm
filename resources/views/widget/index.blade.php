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

        // 🌐 Получение параметров (white-label / project_id)
        const params = new URLSearchParams(window.location.search);
        const projectId = params.get('project_id');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            if (projectId) {
                formData.append('project_id', projectId);
            }

            btn.disabled = true;
            btn.innerText = 'Отправка...';
            msgBox.style.display = 'none';

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    msgBox.innerText = 'Заявка отправлена';
                    msgBox.className = 'msg success';
                    form.reset();
                } else {
                    const errors = result.errors ? Object.values(result.errors).flat().join(' ') : '';
                    msgBox.innerText = errors || 'Ошибка';
                    msgBox.className = 'msg error';
                }

            } catch (e) {
                msgBox.innerText = 'Ошибка сети';
                msgBox.className = 'msg error';
            }

            msgBox.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Отправить';
        });
    </script>

</body>

</html>